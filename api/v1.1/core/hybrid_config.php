<?php
/**
 * Configuración del Sistema Híbrido
 * Define parámetros y configuraciones para el sistema de créditos/suscripción
 */

// Configuración del sistema híbrido
define('HYBRID_CREDITS_REQUIRED', 2); // Créditos requeridos por operación
define('HYBRID_SUBSCRIPTION_PRIORITY', true); // Suscripción tiene prioridad sobre créditos
define('HYBRID_LOG_ACCESS', true); // Habilitar logging de accesos
define('HYBRID_LOG_FILE', __DIR__ . '/access_log.txt'); // Archivo de log

// Configuración de notificaciones
define('HYBRID_NOTIFY_EXPIRATION_DAYS', 7); // Días antes de expiración para notificar
define('HYBRID_NOTIFY_LOW_CREDITS', 5); // Créditos mínimos para notificar

// Configuración de suscripciones
define('HYBRID_SUBSCRIPTION_TYPES', [
    1 => 'Básica',
    2 => 'Premium', 
    3 => 'VIP'
]);

// Configuración de límites por tipo de suscripción
define('HYBRID_SUBSCRIPTION_LIMITS', [
    1 => ['daily_checks' => 50, 'monthly_checks' => 1000],
    2 => ['daily_checks' => 200, 'monthly_checks' => 5000],
    3 => ['daily_checks' => 500, 'monthly_checks' => 15000]
]);

// Función para obtener configuración
function get_hybrid_config($key = null) {
    $config = [
        'credits_required' => HYBRID_CREDITS_REQUIRED,
        'subscription_priority' => HYBRID_SUBSCRIPTION_PRIORITY,
        'log_access' => HYBRID_LOG_ACCESS,
        'log_file' => HYBRID_LOG_FILE,
        'notify_expiration_days' => HYBRID_NOTIFY_EXPIRATION_DAYS,
        'notify_low_credits' => HYBRID_NOTIFY_LOW_CREDITS,
        'subscription_types' => HYBRID_SUBSCRIPTION_TYPES,
        'subscription_limits' => HYBRID_SUBSCRIPTION_LIMITS
    ];
    
    return $key ? ($config[$key] ?? null) : $config;
}

// Función para verificar límites de suscripción
function check_subscription_limits($connection, $user_id, $subscription_type) {
    try {
        $limits = HYBRID_SUBSCRIPTION_LIMITS[$subscription_type] ?? HYBRID_SUBSCRIPTION_LIMITS[1];
        
        // Verificar límite diario
        $today = date('Y-m-d');
        $query = $connection->prepare("
            SELECT COUNT(*) FROM access_log 
            WHERE user_id = :user_id 
            AND DATE(timestamp) = :today 
            AND access_granted = 1
        ");
        $query->bindParam("user_id", $user_id, PDO::PARAM_STR);
        $query->bindParam("today", $today, PDO::PARAM_STR);
        $query->execute();
        $daily_checks = $query->fetchColumn();
        
        if ($daily_checks >= $limits['daily_checks']) {
            return [
                'allowed' => false,
                'reason' => 'Límite diario alcanzado',
                'current' => $daily_checks,
                'limit' => $limits['daily_checks']
            ];
        }
        
        return [
            'allowed' => true,
            'daily_used' => $daily_checks,
            'daily_limit' => $limits['daily_checks'],
            'monthly_limit' => $limits['monthly_checks']
        ];
        
    } catch (Exception $e) {
        error_log("Error en check_subscription_limits: " . $e->getMessage());
        return [
            'allowed' => true, // Permitir en caso de error
            'error' => 'Error al verificar límites'
        ];
    }
}

// Función para enviar notificaciones
function send_hybrid_notification($user_id, $type, $data = []) {
    // Aquí se puede implementar notificación por email, Telegram, etc.
    $message = "";
    
    switch ($type) {
        case 'subscription_expiring':
            $message = "Tu suscripción expira en {$data['days']} días. Renueva para mantener el acceso.";
            break;
        case 'subscription_expired':
            $message = "Tu suscripción ha expirado. Usa créditos o renueva tu suscripción.";
            break;
        case 'low_credits':
            $message = "Te quedan pocos créditos ({$data['credits']}). Considera renovar tu suscripción.";
            break;
        case 'daily_limit_reached':
            $message = "Has alcanzado tu límite diario de verificaciones.";
            break;
    }
    
    // Log de la notificación
    if (HYBRID_LOG_ACCESS) {
        file_put_contents(HYBRID_LOG_FILE, 
            date('Y-m-d H:i:s') . " - NOTIFICATION - User: $user_id, Type: $type, Message: $message\n", 
            FILE_APPEND
        );
    }
    
    return $message;
}
?>
