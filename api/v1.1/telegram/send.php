<?php
header('Content-Type: application/json');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'No autorizado']));
}

// Obtener el token del bot de Telegram desde la configuración
$bot_token = '7617080823:8237057073:AAHUR5G7fXTzTM6YKzEF9gbQbvZ6jS7TXBk'; // Reemplaza con tu token de bot

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$chat_id = $data['chat_id'] ?? null;
$text = $data['text'] ?? null;

if (!$chat_id || !$text) {
    die(json_encode(['error' => 'Datos incompletos']));
}

// Enviar mensaje a Telegram
$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
$params = [
    'chat_id' => $chat_id,
    'text' => $text,
    'parse_mode' => 'HTML'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // En caso de problemas con SSL
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    die(json_encode([
        'error' => 'Error de cURL: ' . curl_error($ch),
        'http_code' => $http_code
    ]));
}

curl_close($ch);

// Decodificar la respuesta de Telegram
$response = json_decode($result, true);

// Verificar si el mensaje se envió correctamente
if ($response['ok']) {
    echo json_encode(['ok' => true, 'message' => 'Mensaje enviado correctamente']);
} else {
    echo json_encode([
        'error' => 'Error al enviar mensaje',
        'telegram_error' => $response['description'] ?? 'Error desconocido',
        'http_code' => $http_code
    ]);
} 