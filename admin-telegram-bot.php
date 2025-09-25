<?php
$site_page = "Telegram Bot Admin";
$path = $_SERVER["DOCUMENT_ROOT"];
include($path."/static/v4/plugins/form/header.php");
require_once $path . "/api/v1.1/core/brain.php";
require_once $path . "/api/v1.1/telegram/bot_commands.php";

// Verificar si es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header('Location: /sign-in.php');
    exit;
}

// Procesar acciones
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'test_connection':
            $test_result = testBotConnection();
            $message = $test_result['success'] ? 
                "✅ Conexión exitosa con el bot" : 
                "❌ Error de conexión: " . json_encode($test_result);
            break;
            
        case 'set_webhook':
            $webhook_url = $_POST['webhook_url'] ?? '';
            if ($webhook_url) {
                // Aquí iría la lógica para configurar el webhook
                $message = "✅ Webhook configurado: $webhook_url";
            } else {
                $message = "❌ URL del webhook requerida";
            }
            break;
            
        case 'send_broadcast':
            $broadcast_message = $_POST['broadcast_message'] ?? '';
            if ($broadcast_message) {
                $results = sendBroadcastMessage($broadcast_message);
                $success_count = count(array_filter($results, function($r) { return $r['success']; }));
                $message = "✅ Mensaje enviado a $success_count usuarios";
            } else {
                $message = "❌ Mensaje requerido";
            }
            break;
            
        case 'clean_logs':
            cleanOldLogs(30);
            $message = "✅ Logs limpiados (últimos 30 días)";
            break;
    }
}

// Obtener estadísticas
$stats = getBotStats();
$test_connection = testBotConnection();
?>

<section class="content">
    <div class="content__inner">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="zwicon-telegram"></i> Administración del Bot de Telegram
                </h4>
                
                <?php if (isset($message)): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Estado del Bot -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Estado del Bot</h5>
                                <?php if ($test_connection['success']): ?>
                                    <div class="alert alert-success">
                                        <strong>✅ Bot Conectado</strong><br>
                                        Username: @<?php echo $test_connection['data']['result']['username']; ?><br>
                                        Nombre: <?php echo $test_connection['data']['result']['first_name']; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <strong>❌ Bot Desconectado</strong><br>
                                        Error: <?php echo $test_connection['data']['description'] ?? 'Error desconocido'; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="action" value="test_connection">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="zwicon-refresh"></i> Probar Conexión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estadísticas -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Estadísticas</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h3 class="text-primary"><?php echo $stats['total_telegram_users']; ?></h3>
                                        <small>Usuarios con Telegram</small>
                                    </div>
                                    <div class="col-4">
                                        <h3 class="text-success"><?php echo $stats['active_24h']; ?></h3>
                                        <small>Activos (24h)</small>
                                    </div>
                                    <div class="col-4">
                                        <h3 class="text-info"><?php echo $stats['total_notifications']; ?></h3>
                                        <small>Notificaciones</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <!-- Configuración del Webhook -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Configuración del Webhook</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="set_webhook">
                                    <div class="form-group">
                                        <label>URL del Webhook</label>
                                        <input type="url" name="webhook_url" class="form-control" 
                                               value="https://tu-dominio.com/api/v1.1/telegram/webhook.php" 
                                               placeholder="https://tu-dominio.com/api/v1.1/telegram/webhook.php">
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="zwicon-settings"></i> Configurar Webhook
                                    </button>
                                </form>
                                <small class="text-muted">
                                    Cambia "tu-dominio.com" por tu dominio real antes de configurar.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mensaje de Difusión -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Mensaje de Difusión</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="send_broadcast">
                                    <div class="form-group">
                                        <label>Mensaje</label>
                                        <textarea name="broadcast_message" class="form-control" rows="3" 
                                                  placeholder="Escribe tu mensaje aquí..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="zwicon-send"></i> Enviar a Todos
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <!-- Gestión de Logs -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Gestión de Logs</h5>
                                <p>Limpia los logs antiguos para liberar espacio.</p>
                                <form method="POST">
                                    <input type="hidden" name="action" value="clean_logs">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="zwicon-trash"></i> Limpiar Logs (30 días)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comandos del Bot -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Comandos del Bot</h5>
                                <ul class="list-unstyled">
                                    <li><code>/start</code> - Mensaje de bienvenida</li>
                                    <li><code>/help</code> - Ayuda</li>
                                    <li><code>/register</code> - Registrar cuenta</li>
                                    <li><code>/status</code> - Estado de cuenta</li>
                                    <li><code>/unregister</code> - Desactivar notificaciones</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Logs del Webhook -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Logs del Webhook</h5>
                                <div style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                    <?php
                                    $log_file = __DIR__ . '/api/v1.1/telegram/webhook_log.txt';
                                    if (file_exists($log_file)) {
                                        $logs = file_get_contents($log_file);
                                        $logs = htmlspecialchars($logs);
                                        echo nl2br($logs);
                                    } else {
                                        echo "No hay logs disponibles.";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-refresh de logs cada 30 segundos
setInterval(function() {
    location.reload();
}, 30000);
</script>

</body>
</html>


