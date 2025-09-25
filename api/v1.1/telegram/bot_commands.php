<?php
// Gestión de comandos del bot de Telegram
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";

$bot_token = '8369901800:AAGw0nW-YgDEEgP48DUmtpov8axTVQwOVno';

// Función para enviar mensaje a un usuario específico
function sendMessageToUser($user_id, $message) {
    global $bot_token;
    
    // Obtener chat_id del usuario
    $stmt = $connection->prepare("SELECT telegram_chat_id FROM breathe_users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !$user['telegram_chat_id']) {
        return false;
    }
    
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $params = [
        'chat_id' => $user['telegram_chat_id'],
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($result, true);
}

// Función para enviar notificación de live
function sendLiveNotification($user_id, $card, $gate, $pais, $response) {
    $message = "✅ *TARJETA LIVE DETECTADA*\n\n";
    $message .= "💳 *Tarjeta:* `$card`\n";
    $message .= "⚙️ *Gate:* $gate\n";
    $message .= "🌍 *País:* $pais\n";
    $message .= "📊 *Respuesta:* $response\n";
    $message .= "⏰ *Hora:* " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "🎉 ¡Felicidades! Has encontrado una tarjeta válida.";
    
    return sendMessageToUser($user_id, $message);
}

// Función para enviar notificación de error
function sendErrorNotification($user_id, $error_message) {
    $message = "❌ *ERROR EN EL SISTEMA*\n\n";
    $message .= "⚠️ *Mensaje:* $error_message\n";
    $message .= "⏰ *Hora:* " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "💡 Contacta al administrador si el problema persiste.";
    
    return sendMessageToUser($user_id, $message);
}

// Función para enviar notificación de créditos
function sendCreditsNotification($user_id, $credits_used, $credits_remaining) {
    $message = "💰 *ACTUALIZACIÓN DE CRÉDITOS*\n\n";
    $message .= "➖ *Usados:* $credits_used\n";
    $message .= "💎 *Restantes:* $credits_remaining\n";
    $message .= "⏰ *Hora:* " . date('Y-m-d H:i:s') . "\n\n";
    
    if ($credits_remaining < 10) {
        $message .= "⚠️ *Aviso:* Te quedan pocos créditos. Considera recargar tu cuenta.";
    }
    
    return sendMessageToUser($user_id, $message);
}

// Función para enviar notificación de suscripción
function sendSubscriptionNotification($user_id, $subscription_status) {
    $status_text = $subscription_status ? "ACTIVADA" : "DESACTIVADA";
    $emoji = $subscription_status ? "✅" : "❌";
    
    $message = "$emoji *SUSCRIPCIÓN $status_text*\n\n";
    $message .= "📅 *Fecha:* " . date('Y-m-d H:i:s') . "\n\n";
    
    if ($subscription_status) {
        $message .= "🎉 ¡Tu suscripción ha sido activada!\n";
        $message .= "Ahora tienes acceso completo a todas las funciones.";
    } else {
        $message .= "⚠️ Tu suscripción ha sido desactivada.\n";
        $message .= "Contacta al administrador para reactivarla.";
    }
    
    return sendMessageToUser($user_id, $message);
}

// Función para enviar notificación de mantenimiento
function sendMaintenanceNotification($user_id, $maintenance_message) {
    $message = "🔧 *MANTENIMIENTO DEL SISTEMA*\n\n";
    $message .= "📢 *Mensaje:* $maintenance_message\n";
    $message .= "⏰ *Hora:* " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "💡 El sistema estará disponible nuevamente pronto.";
    
    return sendMessageToUser($user_id, $message);
}

// Función para enviar notificación masiva
function sendBroadcastMessage($message) {
    global $connection;
    
    $stmt = $connection->prepare("SELECT id, telegram_chat_id FROM breathe_users WHERE telegram_chat_id IS NOT NULL");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    foreach ($users as $user) {
        $result = sendMessageToUser($user['id'], $message);
        $results[] = [
            'user_id' => $user['id'],
            'success' => $result['ok'] ?? false
        ];
    }
    
    return $results;
}

// Función para obtener estadísticas del bot
function getBotStats() {
    global $connection;
    
    $stats = [];
    
    // Total de usuarios con Telegram
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM breathe_users WHERE telegram_chat_id IS NOT NULL");
    $stmt->execute();
    $stats['total_telegram_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Usuarios activos en las últimas 24 horas
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM breathe_users WHERE telegram_chat_id IS NOT NULL AND updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute();
    $stats['active_24h'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de notificaciones enviadas (basado en logs)
    $log_file = __DIR__ . '/telegram_notifications.log';
    if (file_exists($log_file)) {
        $stats['total_notifications'] = substr_count(file_get_contents($log_file), 'Notificación enviada:');
    } else {
        $stats['total_notifications'] = 0;
    }
    
    return $stats;
}

// Función para limpiar logs antiguos
function cleanOldLogs($days = 30) {
    $log_files = [
        __DIR__ . '/webhook_log.txt',
        __DIR__ . '/telegram_notifications.log'
    ];
    
    foreach ($log_files as $log_file) {
        if (file_exists($log_file)) {
            $lines = file($log_file);
            $cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $filtered_lines = array_filter($lines, function($line) use ($cutoff_date) {
                preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches);
                if (isset($matches[1])) {
                    return $matches[1] >= $cutoff_date;
                }
                return true;
            });
            
            file_put_contents($log_file, implode('', $filtered_lines));
        }
    }
}

// Función para probar la conexión del bot
function testBotConnection() {
    global $bot_token;
    
    $url = "https://api.telegram.org/bot{$bot_token}/getMe";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => $http_code == 200,
        'data' => json_decode($result, true),
        'http_code' => $http_code
    ];
}
?>


