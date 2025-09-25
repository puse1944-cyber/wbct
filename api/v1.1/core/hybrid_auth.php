<?php
/**
 * Sistema de Autenticación Híbrido
 * Funciona tanto por créditos como por suscripción de días
 */

require_once __DIR__ . '/hybrid_config.php';

function check_user_access($connection, $user_id, $required_credits = null) {
    // Usar configuración por defecto si no se especifica
    if ($required_credits === null) {
        $required_credits = get_hybrid_config('credits_required');
    }
    try {
        // Obtener datos del usuario
        $query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
        $query->bindParam("id", $user_id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return [
                'access' => false,
                'reason' => 'Usuario no encontrado',
                'type' => 'error'
            ];
        }

        $credits = $result["creditos"];
        $fech_reg = $result["fech_reg"];
        $suscripcion = $result["suscripcion"] ?? 0;

        // Verificar suscripción por días (prioridad alta)
        if (!empty($fech_reg)) {
            $expiration_date = new DateTime($fech_reg);
            $current_date = new DateTime();
            
            if ($current_date <= $expiration_date) {
                return [
                    'access' => true,
                    'reason' => 'Suscripción activa',
                    'type' => 'subscription',
                    'expires' => $fech_reg,
                    'credits' => $credits
                ];
            }
        }

        // Verificar créditos (sistema de respaldo)
        if ($credits >= $required_credits) {
            return [
                'access' => true,
                'reason' => 'Créditos suficientes',
                'type' => 'credits',
                'credits' => $credits,
                'required' => $required_credits
            ];
        }

        // Sin acceso
        return [
            'access' => false,
            'reason' => 'Suscripción expirada y créditos insuficientes',
            'type' => 'denied',
            'credits' => $credits,
            'required' => $required_credits,
            'expires' => $fech_reg
        ];

    } catch (Exception $e) {
        error_log("Error en check_user_access: " . $e->getMessage());
        return [
            'access' => false,
            'reason' => 'Error del sistema',
            'type' => 'error'
        ];
    }
}

function deduct_credits_if_needed($connection, $user_id, $access_info, $required_credits = 2) {
    try {
        // Solo deducir créditos si el acceso fue por créditos, no por suscripción
        if ($access_info['type'] === 'credits') {
            $new_balance = $access_info['credits'] - $required_credits;
            
            $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
            $query->bindParam("id", $user_id, PDO::PARAM_STR);
            $query->bindParam("creditos", $new_balance, PDO::PARAM_STR);
            $query->execute();
            
            return [
                'success' => true,
                'new_balance' => $new_balance,
                'deducted' => $required_credits
            ];
        }
        
        return [
            'success' => true,
            'new_balance' => $access_info['credits'],
            'deducted' => 0,
            'reason' => 'No se deducen créditos por suscripción activa'
        ];

    } catch (Exception $e) {
        error_log("Error en deduct_credits_if_needed: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error al actualizar créditos'
        ];
    }
}

function get_user_status_display($access_info) {
    if ($access_info['access']) {
        if ($access_info['type'] === 'subscription') {
            return "Suscripción activa hasta: " . $access_info['expires'];
        } else {
            return "Créditos disponibles: " . $access_info['credits'];
        }
    } else {
        if ($access_info['type'] === 'denied') {
            return "Suscripción expirada y créditos insuficientes (" . $access_info['credits'] . "/" . $access_info['required'] . ")";
        } else {
            return $access_info['reason'];
        }
    }
}

function log_access_attempt($user_id, $access_info, $gate_name = 'unknown') {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $user_id,
        'gate' => $gate_name,
        'access_granted' => $access_info['access'],
        'access_type' => $access_info['type'],
        'reason' => $access_info['reason']
    ];
    
    file_put_contents(__DIR__ . '/access_log.txt', 
        json_encode($log_data) . "\n", 
        FILE_APPEND
    );
}
?>
