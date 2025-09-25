<?php
/**
 * Configuración del Bot de Telegram para Monitoreo
 * DARK CT - Sistema de Seguridad
 */

// ===========================================
// CONFIGURACIÓN DEL BOT DE TELEGRAM
// ===========================================

// Token del bot (obtener de @BotFather)
define('TELEGRAM_BOT_TOKEN', '7554401496:AAFIlezZ4GcY6TfnH06Mh2fFlbmBptqPhsY');

// ID del chat donde recibir notificaciones (tu chat personal)
define('TELEGRAM_CHAT_ID', '5073251387');

// ===========================================
// INSTRUCCIONES DE CONFIGURACIÓN
// ===========================================

/*
PASO 1: Crear el Bot
1. Abre Telegram y busca @BotFather
2. Envía el comando /newbot
3. Elige un nombre para tu bot (ej: "DARK CT Security Monitor")
4. Elige un username (ej: "darkct_security_bot")
5. Copia el token que te da y reemplaza 'TU_BOT_TOKEN_AQUI'

PASO 2: Obtener tu Chat ID
1. Inicia una conversación con tu bot
2. Envía cualquier mensaje (ej: /start)
3. Abre esta URL en tu navegador:
   https://api.telegram.org/botTU_BOT_TOKEN_AQUI/getUpdates
4. Busca el "chat":{"id": y copia ese número
5. Reemplaza 'TU_CHAT_ID_AQUI' con ese número

PASO 3: Configurar el Bot
1. Edita este archivo con tus datos reales
2. El bot te enviará notificaciones de:
   - Inicios de sesión de usuarios
   - Actividad sospechosa
   - Múltiples IPs/ubicaciones
   - Accesos compartidos detectados

EJEMPLO DE NOTIFICACIÓN:
✅ NUEVO INICIO DE SESIÓN

👤 Usuario: admin
📧 Email: admin@darkct.com
🔑 Estado: Suscripción activa hasta: 15/12/2024
👑 Rol: Administrador

🌐 Información de Conexión:
📍 IP: 192.168.1.100
🌍 Ubicación: Madrid, Madrid, Spain
🌐 Navegador: Chrome
💻 Sistema: Windows
🕒 Fecha: 15/09/2024 14:30:25
🔗 Referer: Direct
🆔 Session ID: abc123def456

⚠️ ACTIVIDAD SOSPECHOSA:
Múltiples IPs en 24h: 192.168.1.100, 203.0.113.1
Múltiples ubicaciones: Madrid, Madrid, Spain, New York, NY, USA
*/

// ===========================================
// FUNCIONES AUXILIARES
// ===========================================

function getTelegramConfig() {
    return [
        'bot_token' => TELEGRAM_BOT_TOKEN,
        'chat_id' => TELEGRAM_CHAT_ID,
        'enabled' => (TELEGRAM_BOT_TOKEN !== '7554401496:AAFIlezZ4GcY6TfnH06Mh2fFlbmBptqPhsY' && TELEGRAM_CHAT_ID !== 'TU_CHAT_ID_AQUI')
    ];
}

function testTelegramBot() {
    $config = getTelegramConfig();
    
    if (!$config['enabled']) {
        return [
            'success' => false,
            'message' => 'Bot no configurado. Edita telegram_bot_config.php'
        ];
    }
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/sendMessage";
    $data = [
        'chat_id' => $config['chat_id'],
        'text' => '🤖 <b>DARK CT Security Monitor</b>\n\n✅ Bot configurado correctamente!\n🕒 ' . date('d/m/Y H:i:s'),
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result) {
        $response = json_decode($result, true);
        if ($response['ok']) {
            return [
                'success' => true,
                'message' => 'Bot funcionando correctamente!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error del bot: ' . $response['description']
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'Error de conexión con Telegram'
        ];
    }
}
?>
