<?php
header('Content-Type: application/json');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'No autorizado']));
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/telegram/bot_commands.php";

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$card = $data['card'] ?? null;
$gate = $data['gate'] ?? null;
$pais = $data['pais'] ?? null;
$response = $data['response'] ?? null;

if (!$card || !$gate || !$pais || !$response) {
    die(json_encode(['error' => 'Datos incompletos']));
}

// Obtener el ID del usuario
$user_id = $_SESSION['user_id'];

// Log de la notificación
file_put_contents(__DIR__ . '/../telegram_notifications.log', 
    "[" . date('Y-m-d H:i:s') . "] Notificación enviada: " . json_encode($data) . "\n", 
    FILE_APPEND
);

// Enviar notificación usando el nuevo sistema
$result = sendLiveNotification($user_id, $card, $gate, $pais, $response);

if ($result && $result['ok']) {
    echo json_encode(['ok' => true, 'message' => 'Notificación enviada correctamente']);
} else {
    echo json_encode([
        'error' => 'Error al enviar notificación',
        'telegram_error' => $result['description'] ?? 'Error desconocido'
    ]);
} 