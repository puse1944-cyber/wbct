<?php
/**
 * Sistema de Monitoreo de Inicios de Sesión para Telegram
 * Detecta accesos compartidos y accesos sospechosos
 */

require_once __DIR__ . '/../../core/brain.php';

// Configuración del bot de Telegram
define('TELEGRAM_BOT_TOKEN', 'TU_BOT_TOKEN_AQUI'); // Cambiar por tu token
define('TELEGRAM_CHAT_ID', 'TU_CHAT_ID_AQUI'); // Cambiar por tu chat ID

class LoginMonitor {
    private $connection;
    private $bot_token;
    private $chat_id;
    
    public function __construct($connection, $bot_token, $chat_id) {
        $this->connection = $connection;
        $this->bot_token = $bot_token;
        $this->chat_id = $chat_id;
    }
    
    /**
     * Registrar inicio de sesión y enviar notificación
     */
    public function logLogin($user_id, $username) {
        try {
            // Obtener información del usuario
            $user_info = $this->getUserInfo($user_id);
            if (!$user_info) {
                return false;
            }
            
            // Obtener información de la sesión
            $session_info = $this->getSessionInfo();
            
            // Verificar si es un acceso sospechoso
            $suspicious = $this->checkSuspiciousActivity($user_id, $session_info);
            
            // Registrar en base de datos
            $this->saveLoginLog($user_id, $username, $session_info, $suspicious);
            
            // Enviar notificación a Telegram
            $this->sendTelegramNotification($user_info, $session_info, $suspicious);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error en LoginMonitor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener información del usuario
     */
    private function getUserInfo($user_id) {
        $query = $this->connection->prepare("
            SELECT id, username, email, suscripcion, creditos, fech_reg, 
                   suscripcion_fin, active, IS_ADMIN
            FROM breathe_users 
            WHERE id = :user_id
        ");
        $query->bindParam("user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener información de la sesión actual
     */
    private function getSessionInfo() {
        return [
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'timestamp' => date('Y-m-d H:i:s'),
            'date' => date('d/m/Y'),
            'time' => date('H:i:s'),
            'location' => $this->getLocationFromIP($this->getClientIP()),
            'browser' => $this->getBrowserInfo(),
            'os' => $this->getOSInfo(),
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'Direct',
            'session_id' => session_id()
        ];
    }
    
    /**
     * Obtener IP real del cliente
     */
    private function getClientIP() {
        $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    /**
     * Obtener ubicación aproximada por IP
     */
    private function getLocationFromIP($ip) {
        if ($ip === 'Unknown' || $ip === '127.0.0.1' || strpos($ip, '192.168.') === 0) {
            return 'Local/Private';
        }
        
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,timezone");
            if ($response) {
                $data = json_decode($response, true);
                if ($data['status'] === 'success') {
                    return $data['city'] . ', ' . $data['regionName'] . ', ' . $data['country'];
                }
            }
        } catch (Exception $e) {
            // Ignorar errores de geolocalización
        }
        
        return 'Unknown Location';
    }
    
    /**
     * Obtener información del navegador
     */
    private function getBrowserInfo() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
        if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
        if (strpos($user_agent, 'Safari') !== false) return 'Safari';
        if (strpos($user_agent, 'Edge') !== false) return 'Edge';
        if (strpos($user_agent, 'Opera') !== false) return 'Opera';
        
        return 'Unknown Browser';
    }
    
    /**
     * Obtener información del sistema operativo
     */
    private function getOSInfo() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($user_agent, 'Windows') !== false) return 'Windows';
        if (strpos($user_agent, 'Mac') !== false) return 'macOS';
        if (strpos($user_agent, 'Linux') !== false) return 'Linux';
        if (strpos($user_agent, 'Android') !== false) return 'Android';
        if (strpos($user_agent, 'iOS') !== false) return 'iOS';
        
        return 'Unknown OS';
    }
    
    /**
     * Verificar actividad sospechosa
     */
    private function checkSuspiciousActivity($user_id, $session_info) {
        $suspicious = [];
        
        // Verificar IPs diferentes en las últimas 24 horas
        $recent_logins = $this->getRecentLogins($user_id, 24);
        $unique_ips = array_unique(array_column($recent_logins, 'ip'));
        
        if (count($unique_ips) > 3) {
            $suspicious[] = "Múltiples IPs en 24h: " . implode(', ', $unique_ips);
        }
        
        // Verificar ubicaciones diferentes
        $unique_locations = array_unique(array_column($recent_logins, 'location'));
        if (count($unique_locations) > 2) {
            $suspicious[] = "Múltiples ubicaciones: " . implode(', ', $unique_locations);
        }
        
        // Verificar navegadores diferentes
        $unique_browsers = array_unique(array_column($recent_logins, 'browser'));
        if (count($unique_browsers) > 2) {
            $suspicious[] = "Múltiples navegadores: " . implode(', ', $unique_browsers);
        }
        
        return $suspicious;
    }
    
    /**
     * Obtener inicios de sesión recientes
     */
    private function getRecentLogins($user_id, $hours = 24) {
        $query = $this->connection->prepare("
            SELECT ip, location, browser, created_at
            FROM login_monitor_logs 
            WHERE user_id = :user_id 
            AND created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
            ORDER BY created_at DESC
        ");
        $query->bindParam("user_id", $user_id, PDO::PARAM_INT);
        $query->bindParam("hours", $hours, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Guardar log en base de datos
     */
    private function saveLoginLog($user_id, $username, $session_info, $suspicious) {
        $query = $this->connection->prepare("
            INSERT INTO login_monitor_logs 
            (user_id, username, ip, user_agent, location, browser, os, 
             referer, session_id, suspicious_activity, created_at) 
            VALUES (:user_id, :username, :ip, :user_agent, :location, :browser, :os, 
                    :referer, :session_id, :suspicious, NOW())
        ");
        
        $query->bindParam("user_id", $user_id, PDO::PARAM_INT);
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->bindParam("ip", $session_info['ip'], PDO::PARAM_STR);
        $query->bindParam("user_agent", $session_info['user_agent'], PDO::PARAM_STR);
        $query->bindParam("location", $session_info['location'], PDO::PARAM_STR);
        $query->bindParam("browser", $session_info['browser'], PDO::PARAM_STR);
        $query->bindParam("os", $session_info['os'], PDO::PARAM_STR);
        $query->bindParam("referer", $session_info['referer'], PDO::PARAM_STR);
        $query->bindParam("session_id", $session_info['session_id'], PDO::PARAM_STR);
        $query->bindParam("suspicious", json_encode($suspicious), PDO::PARAM_STR);
        
        return $query->execute();
    }
    
    /**
     * Enviar notificación a Telegram
     */
    private function sendTelegramNotification($user_info, $session_info, $suspicious) {
        $message = $this->formatTelegramMessage($user_info, $session_info, $suspicious);
        
        $url = "https://api.telegram.org/bot{$this->bot_token}/sendMessage";
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        @file_get_contents($url, false, $context);
    }
    
    /**
     * Formatear mensaje para Telegram
     */
    private function formatTelegramMessage($user_info, $session_info, $suspicious) {
        $status_icon = !empty($suspicious) ? "🚨" : "✅";
        $suspicious_text = !empty($suspicious) ? "\n\n⚠️ <b>ACTIVIDAD SOSPECHOSA:</b>\n" . implode("\n", $suspicious) : "";
        
        $subscription_status = "Créditos: {$user_info['creditos']}";
        if (!empty($user_info['fech_reg'])) {
            $exp_date = new DateTime($user_info['fech_reg']);
            $now = new DateTime();
            if ($now <= $exp_date) {
                $subscription_status = "Suscripción activa hasta: " . $exp_date->format('d/m/Y');
            }
        }
        
        return "{$status_icon} <b>NUEVO INICIO DE SESIÓN</b>\n\n" .
               "👤 <b>Usuario:</b> {$user_info['username']}\n" .
               "📧 <b>Email:</b> {$user_info['email']}\n" .
               "🔑 <b>Estado:</b> {$subscription_status}\n" .
               "👑 <b>Rol:</b> " . ($user_info['IS_ADMIN'] ? 'Administrador' : 'Usuario') . "\n\n" .
               "🌐 <b>Información de Conexión:</b>\n" .
               "📍 <b>IP:</b> {$session_info['ip']}\n" .
               "🌍 <b>Ubicación:</b> {$session_info['location']}\n" .
               "🌐 <b>Navegador:</b> {$session_info['browser']}\n" .
               "💻 <b>Sistema:</b> {$session_info['os']}\n" .
               "🕒 <b>Fecha:</b> {$session_info['date']} {$session_info['time']}\n" .
               "🔗 <b>Referer:</b> {$session_info['referer']}\n" .
               "🆔 <b>Session ID:</b> {$session_info['session_id']}" .
               $suspicious_text;
    }
}

// Función para usar desde otros archivos
function monitorUserLogin($user_id, $username) {
    $monitor = new LoginMonitor($connection, TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID);
    return $monitor->logLogin($user_id, $username);
}
?>
