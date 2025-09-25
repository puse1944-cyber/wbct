<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de entrada
file_put_contents(__DIR__ . '/webhook_log.txt', "[" . date('Y-m-d H:i:s') . "] Webhook recibido\n", FILE_APPEND);

// Obtener el token del bot desde la configuración
$bot_token = '8369901800:AAGw0nW-YgDEEgP48DUmtpov8axTVQwOVno';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener el contenido JSON
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Log del update recibido
file_put_contents(__DIR__ . '/webhook_log.txt', "[" . date('Y-m-d H:i:s') . "] Update: " . $input . "\n", FILE_APPEND);

// Verificar que el JSON sea válido
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

// Verificar que sea un update válido de Telegram
if (!isset($update['update_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Update inválido']);
    exit;
}

// Procesar el update
try {
    processUpdate($update, $bot_token);
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/webhook_log.txt', "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}

function processUpdate($update, $bot_token) {
    // Verificar si es un mensaje
    if (isset($update['message'])) {
        $message = $update['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $user_id = $message['from']['id'] ?? null;
        $username = $message['from']['username'] ?? 'Usuario';
        
        // Log del mensaje
        file_put_contents(__DIR__ . '/webhook_log.txt', 
            "[" . date('Y-m-d H:i:s') . "] Mensaje de @$username ($user_id): $text\n", 
            FILE_APPEND
        );
        
        // Procesar comandos
        if (strpos($text, '/') === 0) {
            processCommand($chat_id, $text, $user_id, $username, $bot_token);
        } else {
            // Mensaje normal - mostrar ayuda
            sendMessage($chat_id, "Hola! Usa /start para comenzar o /help para ver los comandos disponibles.", $bot_token);
        }
    }
    
    // Verificar si es una callback query (botones inline)
    if (isset($update['callback_query'])) {
        $callback = $update['callback_query'];
        $chat_id = $callback['message']['chat']['id'];
        $data = $callback['data'];
        $user_id = $callback['from']['id'];
        
        processCallbackQuery($chat_id, $data, $user_id, $bot_token);
    }
}

function processCommand($chat_id, $command, $user_id, $username, $bot_token) {
    switch ($command) {
        case '/start':
            $message = "🤖 *Bienvenido al Bot de DARK CT*\n\n";
            $message .= "Este bot te notificará sobre las tarjetas live detectadas en el sistema.\n\n";
            $message .= "📋 *Comandos disponibles:*\n";
            $message .= "/start - Mostrar este mensaje\n";
            $message .= "/help - Ayuda detallada\n";
            $message .= "/register - Registrar tu chat ID\n";
            $message .= "/status - Ver estado de tu cuenta\n";
            $message .= "/unregister - Desactivar notificaciones\n\n";
            $message .= "💡 *Para recibir notificaciones:*\n";
            $message .= "1. Usa /register para vincular tu cuenta\n";
            $message .= "2. Activa las notificaciones en el panel web\n";
            $message .= "3. ¡Recibirás notificaciones automáticamente!";
            
            sendMessage($chat_id, $message, $bot_token);
            break;
            
        case '/help':
            $message = "🆘 *Ayuda del Bot DARK CT*\n\n";
            $message .= "🔧 *Funcionalidades:*\n";
            $message .= "• Notificaciones de tarjetas live\n";
            $message .= "• Estado de tu cuenta\n";
            $message .= "• Gestión de notificaciones\n\n";
            $message .= "📱 *Comandos:*\n";
            $message .= "/start - Inicio y bienvenida\n";
            $message .= "/register - Vincular cuenta\n";
            $message .= "/status - Estado de cuenta\n";
            $message .= "/unregister - Desactivar notificaciones\n";
            $message .= "/help - Esta ayuda\n\n";
            $message .= "❓ *¿Necesitas ayuda?*\n";
            $message .= "Contacta al administrador del sistema.";
            
            sendMessage($chat_id, $message, $bot_token);
            break;
            
        case '/register':
            registerUser($chat_id, $user_id, $username, $bot_token);
            break;
            
        case '/status':
            checkUserStatus($chat_id, $user_id, $bot_token);
            break;
            
        case '/unregister':
            unregisterUser($chat_id, $user_id, $bot_token);
            break;
            
        default:
            sendMessage($chat_id, "❌ Comando no reconocido. Usa /help para ver los comandos disponibles.", $bot_token);
    }
}

function registerUser($chat_id, $user_id, $username, $bot_token) {
    // Conectar a la base de datos
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";
    
    try {
        // Buscar usuario por telegram_user_id
        $stmt = $connection->prepare("SELECT id, username, telegram_chat_id FROM breathe_users WHERE telegram_user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Actualizar chat_id si es diferente
            if ($user['telegram_chat_id'] != $chat_id) {
                $update_stmt = $connection->prepare("UPDATE breathe_users SET telegram_chat_id = :chat_id WHERE id = :id");
                $update_stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
                $update_stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $update_stmt->execute();
            }
            
            $message = "✅ *Registro exitoso*\n\n";
            $message .= "👤 Usuario: " . $user['username'] . "\n";
            $message .= "🆔 Chat ID: " . $chat_id . "\n";
            $message .= "📱 Username: @" . $username . "\n\n";
            $message .= "🎉 ¡Ahora recibirás notificaciones de tarjetas live!";
        } else {
            $message = "❌ *Usuario no encontrado*\n\n";
            $message .= "No se encontró una cuenta vinculada a tu ID de Telegram.\n\n";
            $message .= "💡 *Solución:*\n";
            $message .= "1. Asegúrate de estar registrado en el sistema web\n";
            $message .= "2. Contacta al administrador para vincular tu cuenta\n";
            $message .= "3. O usa el comando /register desde el panel web";
        }
        
        sendMessage($chat_id, $message, $bot_token);
        
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/webhook_log.txt', 
            "[" . date('Y-m-d H:i:s') . "] Error en register: " . $e->getMessage() . "\n", 
            FILE_APPEND
        );
        
        sendMessage($chat_id, "❌ Error interno. Intenta más tarde.", $bot_token);
    }
}

function checkUserStatus($chat_id, $user_id, $bot_token) {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";
    
    try {
        $stmt = $connection->prepare("SELECT username, creditos, suscripcion, telegram_chat_id FROM breathe_users WHERE telegram_user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $message = "📊 *Estado de tu cuenta*\n\n";
            $message .= "👤 Usuario: " . $user['username'] . "\n";
            $message .= "💰 Créditos: " . $user['creditos'] . "\n";
            $message .= "🎫 Suscripción: " . ($user['suscripcion'] ? "Activa" : "Inactiva") . "\n";
            $message .= "📱 Notificaciones: " . ($user['telegram_chat_id'] ? "✅ Activadas" : "❌ Desactivadas") . "\n\n";
            
            if ($user['telegram_chat_id'] != $chat_id) {
                $message .= "⚠️ *Aviso:* Tu chat ID no coincide. Usa /register para actualizarlo.";
            }
        } else {
            $message = "❌ *Usuario no encontrado*\n\n";
            $message .= "No tienes una cuenta vinculada. Usa /register para vincular tu cuenta.";
        }
        
        sendMessage($chat_id, $message, $bot_token);
        
    } catch (Exception $e) {
        sendMessage($chat_id, "❌ Error al consultar el estado. Intenta más tarde.", $bot_token);
    }
}

function unregisterUser($chat_id, $user_id, $bot_token) {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";
    
    try {
        $stmt = $connection->prepare("UPDATE breathe_users SET telegram_chat_id = NULL WHERE telegram_user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $message = "✅ *Desregistro exitoso*\n\n";
        $message .= "Ya no recibirás notificaciones de tarjetas live.\n\n";
        $message .= "💡 Para reactivar las notificaciones, usa /register";
        
        sendMessage($chat_id, $message, $bot_token);
        
    } catch (Exception $e) {
        sendMessage($chat_id, "❌ Error al desregistrar. Intenta más tarde.", $bot_token);
    }
}

function processCallbackQuery($chat_id, $data, $user_id, $bot_token) {
    // Procesar respuestas de botones inline si los hay
    $message = "Botón presionado: " . $data;
    sendMessage($chat_id, $message, $bot_token);
}

function sendMessage($chat_id, $text, $bot_token) {
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
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
    
    // Log de la respuesta
    file_put_contents(__DIR__ . '/webhook_log.txt', 
        "[" . date('Y-m-d H:i:s') . "] Respuesta enviada a $chat_id: " . substr($result, 0, 100) . "\n", 
        FILE_APPEND
    );
    
    return $result;
}
?>


