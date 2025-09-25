<?php
session_start();
error_reporting(0);
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";
include($path . "/static/v4/plugins/form/header.php");

// Verificar permisos de administrador
$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $_SESSION["user_id"], PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);
if (!$user || $user["suscripcion"] != 3) {
    header("Location: /");
    exit;
}

// Obtener logs de inicio de sesión
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$query = $connection->prepare("
    SELECT lml.*, bu.email, bu.suscripcion, (bu.suscripcion = 3) as IS_ADMIN
    FROM login_monitor_logs lml
    LEFT JOIN breathe_users bu ON lml.user_id = bu.id
    ORDER BY lml.created_at DESC
    LIMIT :limit OFFSET :offset
");
$query->bindParam("limit", $limit, PDO::PARAM_INT);
$query->bindParam("offset", $offset, PDO::PARAM_INT);
$query->execute();
$logs = $query->fetchAll(PDO::FETCH_ASSOC);

// Contar total de logs
$count_query = $connection->query("SELECT COUNT(*) FROM login_monitor_logs");
$total_logs = $count_query->fetchColumn();
$total_pages = ceil($total_logs / $limit);

// Obtener estadísticas
$stats_query = $connection->query("
    SELECT 
        COUNT(*) as total_logins,
        COUNT(DISTINCT user_id) as unique_users,
        COUNT(DISTINCT ip) as unique_ips,
        COUNT(CASE WHEN suspicious_activity != '[]' THEN 1 END) as suspicious_logins
    FROM login_monitor_logs
");
$stats = $stats_query->fetch(PDO::FETCH_ASSOC);
?>

<style>
/* Estilos para el monitor de login */
.login-monitor-container {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%);
    min-height: 100vh;
    padding: 20px;
}

.card {
    background: rgba(20, 20, 20, 0.95) !important;
    border: 1px solid #333 !important;
    border-radius: 15px !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3) !important;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, #00ffc4, transparent);
    animation: scanLine 4s infinite;
    will-change: left;
}

.content__title h1 {
    color: #ffffff !important;
    font-size: 1.8rem !important;
    font-weight: 700 !important;
    text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
    display: flex;
    align-items: center;
    gap: 15px;
}

.content__title h1 i {
    color: #00ffc4;
    font-size: 2rem;
    text-shadow: 0 0 15px rgba(0, 255, 196, 0.8);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #00ffc4, #0099ff, #00ffc4);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #00ffc4;
    margin-bottom: 5px;
}

.stat-label {
    color: #cccccc;
    font-size: 0.9rem;
}

.logs-table {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    overflow: hidden;
}

.logs-table table {
    width: 100%;
    border-collapse: collapse;
}

.logs-table th {
    background: rgba(0, 255, 196, 0.1);
    color: #00ffc4;
    padding: 15px 10px;
    text-align: left;
    font-weight: 700;
    border-bottom: 1px solid #333;
}

.logs-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #333;
    color: #ffffff;
}

.logs-table tr:hover {
    background: rgba(0, 255, 196, 0.05);
}

.suspicious {
    background: rgba(255, 68, 68, 0.1) !important;
    border-left: 3px solid #ff4444;
}

.suspicious td {
    color: #ff6666;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
}

.status-normal {
    background: rgba(0, 255, 196, 0.2);
    color: #00ffc4;
}

.status-suspicious {
    background: rgba(255, 68, 68, 0.2);
    color: #ff4444;
}

.status-admin {
    background: rgba(255, 215, 0, 0.2);
    color: #ffd700;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.pagination a {
    padding: 8px 12px;
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.pagination a:hover {
    background: #00ffc4;
    color: #000;
}

.pagination .current {
    background: #00ffc4;
    color: #000;
}

@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Responsive */
@media (max-width: 768px) {
    .login-monitor-container {
        padding: 10px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .logs-table {
        overflow-x: auto;
    }
    
    .logs-table table {
        min-width: 800px;
    }
}
</style>

<section class="content">
    <div class="login-monitor-container">
        <header class="content__title">
            <h1><i class="zwicon-shield"></i> Monitor de Inicios de Sesión</h1>
        </header>
        
        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['total_logins']); ?></div>
                <div class="stat-label">Total de Inicios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['unique_users']); ?></div>
                <div class="stat-label">Usuarios Únicos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['unique_ips']); ?></div>
                <div class="stat-label">IPs Diferentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['suspicious_logins']); ?></div>
                <div class="stat-label">Actividad Sospechosa</div>
            </div>
        </div>
        
        <!-- Tabla de logs -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title" style="color: #00ffc4; margin-bottom: 20px;">
                    <i class="zwicon-list"></i> Historial de Inicios de Sesión
                </h4>
                
                <div class="logs-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>IP</th>
                                <th>Ubicación</th>
                                <th>Navegador</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <?php 
                                $suspicious = json_decode($log['suspicious_activity'], true);
                                $is_suspicious = !empty($suspicious);
                                $is_admin = $log['IS_ADMIN'];
                                ?>
                                <tr class="<?php echo $is_suspicious ? 'suspicious' : ''; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                        <?php if ($is_admin): ?>
                                            <span class="status-badge status-admin">ADMIN</span>
                                        <?php endif; ?>
                                        <br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($log['email']); ?></small>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($log['ip']); ?></code>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($log['location']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($log['browser']); ?>
                                        <br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($log['os']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($log['created_at'])); ?>
                                        <br>
                                        <small style="color: #666;"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($is_suspicious): ?>
                                            <span class="status-badge status-suspicious">⚠️ SOSPECHOSO</span>
                                        <?php else: ?>
                                            <span class="status-badge status-normal">✅ NORMAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm" onclick="showDetails(<?php echo $log['id']; ?>)">
                                            <i class="zwicon-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">&laquo; Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'current' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Siguiente &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal para detalles -->
<div id="detailsModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8);">
    <div class="modal-content" style="background: #1a1a1a; margin: 5% auto; padding: 20px; border: 1px solid #333; border-radius: 10px; width: 80%; max-width: 600px; color: #fff;">
        <span class="close" onclick="closeModal()" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: #00ffc4;">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

<script>
function showDetails(logId) {
    // Aquí puedes implementar la funcionalidad para mostrar detalles
    alert('Detalles del log ID: ' + logId);
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// Cerrar modal al hacer click fuera
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
