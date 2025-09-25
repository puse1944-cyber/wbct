<?php
/**
 * Integración del Login Monitor con el sistema principal
 * Este archivo debe ser incluido en los archivos de login
 */

require_once __DIR__ . '/../telegram/login_monitor.php';

/**
 * Función para registrar inicio de sesión
 * Debe ser llamada después de un login exitoso
 */
function registerUserLogin($user_id, $username) {
    try {
        return monitorUserLogin($user_id, $username);
    } catch (Exception $e) {
        error_log("Error en registerUserLogin: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para verificar si el login monitor está configurado
 */
function isLoginMonitorConfigured() {
    global $connection;
    try {
        $query = $connection->prepare("SELECT COUNT(*) FROM breathe_telegram_config WHERE is_active = 1 AND bot_token != '' AND admin_chat_id != ''");
        $query->execute();
        return $query->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función para obtener estadísticas del login monitor
 */
function getLoginMonitorStats() {
    global $connection;
    try {
        $query = $connection->query("
            SELECT 
                COUNT(*) as total_logins,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT ip) as unique_ips,
                COUNT(CASE WHEN suspicious_activity != '[]' AND suspicious_activity IS NOT NULL THEN 1 END) as suspicious_logins
            FROM login_monitor_logs
        ");
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [
            'total_logins' => 0,
            'unique_users' => 0,
            'unique_ips' => 0,
            'suspicious_logins' => 0
        ];
    }
}
?>
