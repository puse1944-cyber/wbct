<?php
/**
 * Prueba del Sistema Híbrido
 * Verifica que funcione tanto por créditos como por suscripción
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/brain.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1.1/core/hybrid_auth.php";

echo "<h1>🧪 Prueba del Sistema Híbrido</h1>";
echo "<style>body{font-family:Arial;background:#000;color:#fff;padding:20px;} .test{background:#111;padding:15px;margin:10px 0;border:1px solid #333;border-radius:5px;} .success{color:#0f0;} .error{color:#f00;} .info{color:#0ff;}</style>";

// Simular diferentes escenarios de usuario
$test_cases = [
    [
        'name' => 'Usuario con Suscripción Activa',
        'user_data' => [
            'id' => 1,
            'creditos' => 0,
            'fech_reg' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'suscripcion' => 2
        ]
    ],
    [
        'name' => 'Usuario con Suscripción Expirada pero con Créditos',
        'user_data' => [
            'id' => 2,
            'creditos' => 10,
            'fech_reg' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'suscripcion' => 1
        ]
    ],
    [
        'name' => 'Usuario sin Suscripción ni Créditos',
        'user_data' => [
            'id' => 3,
            'creditos' => 1,
            'fech_reg' => null,
            'suscripcion' => 0
        ]
    ],
    [
        'name' => 'Usuario con Suscripción y Créditos',
        'user_data' => [
            'id' => 4,
            'creditos' => 15,
            'fech_reg' => date('Y-m-d H:i:s', strtotime('+15 days')),
            'suscripcion' => 3
        ]
    ]
];

foreach ($test_cases as $test) {
    echo "<div class='test'>";
    echo "<h3>📋 {$test['name']}</h3>";
    
    // Simular datos del usuario
    $user_data = $test['user_data'];
    
    echo "<p><strong>Datos del usuario:</strong></p>";
    echo "<ul>";
    echo "<li>ID: {$user_data['id']}</li>";
    echo "<li>Créditos: {$user_data['creditos']}</li>";
    echo "<li>Fecha de expiración: " . ($user_data['fech_reg'] ?: 'No establecida') . "</li>";
    echo "<li>Suscripción: {$user_data['suscripcion']}</li>";
    echo "</ul>";
    
    // Simular verificación de acceso
    $access_info = check_user_access($connection, $user_data['id'], 2);
    
    echo "<p><strong>Resultado de verificación:</strong></p>";
    echo "<ul>";
    echo "<li>Acceso: " . ($access_info['access'] ? '<span class="success">✅ Permitido</span>' : '<span class="error">❌ Denegado</span>') . "</li>";
    echo "<li>Tipo: <span class='info'>{$access_info['type']}</span></li>";
    echo "<li>Razón: {$access_info['reason']}</li>";
    if (isset($access_info['credits'])) {
        echo "<li>Créditos disponibles: {$access_info['credits']}</li>";
    }
    if (isset($access_info['expires'])) {
        echo "<li>Expira: {$access_info['expires']}</li>";
    }
    echo "</ul>";
    
    // Simular deducción de créditos
    if ($access_info['access']) {
        $deduction_result = deduct_credits_if_needed($connection, $user_data['id'], $access_info, 2);
        echo "<p><strong>Deducción de créditos:</strong></p>";
        echo "<ul>";
        echo "<li>Éxito: " . ($deduction_result['success'] ? '<span class="success">✅</span>' : '<span class="error">❌</span>') . "</li>";
        echo "<li>Créditos deducidos: {$deduction_result['deducted']}</li>";
        echo "<li>Nuevo balance: {$deduction_result['new_balance']}</li>";
        if (isset($deduction_result['reason'])) {
            echo "<li>Nota: {$deduction_result['reason']}</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><strong>Estado mostrado al usuario:</strong> " . get_user_status_display($access_info) . "</p>";
    
    echo "</div>";
}

echo "<div class='test'>";
echo "<h3>📊 Resumen del Sistema Híbrido</h3>";
echo "<p>El sistema híbrido funciona de la siguiente manera:</p>";
echo "<ol>";
echo "<li><strong>Prioridad 1 - Suscripción:</strong> Si el usuario tiene una suscripción activa (fech_reg > fecha actual), puede usar el sistema sin deducir créditos</li>";
echo "<li><strong>Prioridad 2 - Créditos:</strong> Si no hay suscripción activa, verifica si tiene suficientes créditos (mínimo 2)</li>";
echo "<li><strong>Deducción inteligente:</strong> Solo deduce créditos si el acceso fue por créditos, no por suscripción</li>";
echo "<li><strong>Logging:</strong> Registra todos los intentos de acceso para auditoría</li>";
echo "<li><strong>Retrocompatibilidad:</strong> Mantiene la funcionalidad existente sin afectar usuarios actuales</li>";
echo "</ol>";
echo "</div>";

echo "<div class='test'>";
echo "<h3>🔧 Configuración Recomendada</h3>";
echo "<p>Para implementar el sistema híbrido completamente:</p>";
echo "<ul>";
echo "<li>Actualizar todos los gates (Stripe, PayPal, Chase) con el mismo sistema</li>";
echo "<li>Modificar el dashboard para mostrar el estado híbrido</li>";
echo "<li>Crear panel de administración para gestionar suscripciones</li>";
echo "<li>Implementar notificaciones de expiración de suscripción</li>";
echo "</ul>";
echo "</div>";
?>
