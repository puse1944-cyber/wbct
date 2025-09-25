<?php
// Script para configurar el webhook de Telegram
error_reporting(E_ALL);
ini_set('display_errors', 1);

$bot_token = '8369901800:AAGw0nW-YgDEEgP48DUmtpov8axTVQwOVno';
$webhook_url = 'https://dark-ct.com/api/v1.1/telegram/webhook.php'; // Cambiar por tu dominio

echo "<h2>Configuración del Webhook de Telegram</h2>";

// Función para hacer peticiones a la API de Telegram
function telegramRequest($method, $params = []) {
    global $bot_token;
    
    $url = "https://api.telegram.org/bot{$bot_token}/{$method}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'result' => json_decode($result, true),
        'http_code' => $http_code
    ];
}

// 1. Obtener información del bot
echo "<h3>1. Información del Bot</h3>";
$bot_info = telegramRequest('getMe');
if ($bot_info['http_code'] == 200) {
    $bot = $bot_info['result']['result'];
    echo "<p>✅ Bot: @{$bot['username']} ({$bot['first_name']})</p>";
    echo "<p>🆔 ID: {$bot['id']}</p>";
    echo "<p>🤖 Username: @{$bot['username']}</p>";
} else {
    echo "<p>❌ Error al obtener información del bot</p>";
    echo "<pre>" . print_r($bot_info, true) . "</pre>";
}

// 2. Eliminar webhook actual
echo "<h3>2. Eliminando webhook actual</h3>";
$delete_webhook = telegramRequest('deleteWebhook');
if ($delete_webhook['http_code'] == 200) {
    echo "<p>✅ Webhook eliminado correctamente</p>";
} else {
    echo "<p>⚠️ Error al eliminar webhook: " . print_r($delete_webhook, true) . "</p>";
}

// 3. Configurar nuevo webhook
echo "<h3>3. Configurando nuevo webhook</h3>";
echo "<p>URL del webhook: <strong>$webhook_url</strong></p>";

$webhook_params = [
    'url' => $webhook_url,
    'allowed_updates' => json_encode(['message', 'callback_query']),
    'drop_pending_updates' => true
];

$set_webhook = telegramRequest('setWebhook', $webhook_params);

if ($set_webhook['http_code'] == 200 && $set_webhook['result']['ok']) {
    echo "<p>✅ Webhook configurado correctamente</p>";
    echo "<p>📡 URL: $webhook_url</p>";
    echo "<p>🔄 Updates permitidos: message, callback_query</p>";
} else {
    echo "<p>❌ Error al configurar webhook</p>";
    echo "<pre>" . print_r($set_webhook, true) . "</pre>";
}

// 4. Verificar webhook
echo "<h3>4. Verificando webhook</h3>";
$webhook_info = telegramRequest('getWebhookInfo');
if ($webhook_info['http_code'] == 200) {
    $info = $webhook_info['result']['result'];
    echo "<p>📡 URL: {$info['url']}</p>";
    echo "<p>✅ Certificado: " . ($info['has_custom_certificate'] ? 'Sí' : 'No') . "</p>";
    echo "<p>⏰ Última actualización: " . ($info['last_error_date'] ? date('Y-m-d H:i:s', $info['last_error_date']) : 'Nunca') . "</p>";
    echo "<p>❌ Errores pendientes: {$info['pending_update_count']}</p>";
    
    if ($info['last_error_message']) {
        echo "<p>⚠️ Último error: {$info['last_error_message']}</p>";
    }
} else {
    echo "<p>❌ Error al verificar webhook</p>";
}

// 5. Enviar mensaje de prueba
echo "<h3>5. Enviando mensaje de prueba</h3>";
echo "<p>Para probar el webhook, envía un mensaje a tu bot en Telegram.</p>";
echo "<p>Comandos disponibles:</p>";
echo "<ul>";
echo "<li>/start - Mensaje de bienvenida</li>";
echo "<li>/help - Ayuda</li>";
echo "<li>/register - Registrar cuenta</li>";
echo "<li>/status - Estado de cuenta</li>";
echo "<li>/unregister - Desactivar notificaciones</li>";
echo "</ul>";

echo "<h3>6. Próximos pasos</h3>";
echo "<ol>";
echo "<li>Cambia la URL del webhook en este archivo por tu dominio real</li>";
echo "<li>Ejecuta este script desde tu servidor</li>";
echo "<li>Prueba enviando /start a tu bot</li>";
echo "<li>Verifica los logs en api/v1.1/telegram/webhook_log.txt</li>";
echo "</ol>";

echo "<h3>7. Logs del Webhook</h3>";
$log_file = __DIR__ . '/webhook_log.txt';
if (file_exists($log_file)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($log_file)) . "</pre>";
} else {
    echo "<p>No hay logs disponibles aún.</p>";
}
?>


