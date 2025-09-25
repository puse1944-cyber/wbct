<?php
session_start();
error_reporting(0);
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";
include($path . "/static/v4/plugins/form/header.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: /sign-in.php");
    exit;
}

// Obtener información del usuario
$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $_SESSION["user_id"], PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: /sign-in.php");
    exit;
}

// Obtener las lives del usuario
$lives_query = $connection->prepare("SELECT * FROM breathe_lives WHERE user_id=:user_id ORDER BY id DESC");
$lives_query->bindParam("user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$lives_query->execute();
$user_lives = $lives_query->fetchAll(PDO::FETCH_ASSOC);

// Contar total de lives del usuario
$count_query = $connection->prepare("SELECT COUNT(*) FROM breathe_lives WHERE user_id=:user_id");
$count_query->bindParam("user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$count_query->execute();
$total_lives = $count_query->fetchColumn();
?>

<style>
/* Estilos personalizados para Mis Lives */
.mis-lives-container {
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
    transition: transform 0.2s ease, box-shadow 0.2s ease;
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

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4) !important;
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

.stats-container {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    text-align: center;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #00ffc4;
    text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
    margin-bottom: 10px;
}

.stats-label {
    color: #cccccc;
    font-size: 1.1rem;
    font-weight: 500;
}

.live-card {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    position: relative;
    transition: all 0.3s ease;
}

.live-card:hover {
    border-color: #00ffc4;
    box-shadow: 0 0 15px rgba(0, 255, 196, 0.2);
    transform: translateY(-2px);
}

.live-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #00ffc4, #0099ff, #00ffc4);
    border-radius: 10px 10px 0 0;
}

.live-number {
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
    font-weight: 700;
    color: #00ffc4;
    margin-bottom: 10px;
    word-break: break-all;
}

.live-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.info-item {
    background: rgba(0, 255, 196, 0.1);
    border: 1px solid rgba(0, 255, 196, 0.3);
    border-radius: 8px;
    padding: 10px;
    text-align: center;
}

.info-label {
    color: #cccccc;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 5px;
}

.info-value {
    color: #ffffff;
    font-size: 1rem;
    font-weight: 700;
}

.live-date {
    color: #666;
    font-size: 0.9rem;
    margin-top: 10px;
    text-align: right;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-icon {
    font-size: 4rem;
    color: #333;
    margin-bottom: 20px;
}

.empty-text {
    font-size: 1.2rem;
    color: #cccccc;
    margin-bottom: 10px;
}

.empty-subtext {
    color: #666;
    font-size: 0.9rem;
}

.btn-refresh {
    background: linear-gradient(45deg, #00ffc4, #0099ff);
    border: none;
    color: #000;
    font-weight: 700;
    border-radius: 25px;
    padding: 12px 25px;
    transition: all 0.3s ease;
    box-shadow: 0 3px 8px rgba(0, 255, 196, 0.2);
}

.btn-refresh:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0, 255, 196, 0.3);
    color: #000;
}

@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Responsive */
@media (max-width: 768px) {
    .mis-lives-container {
        padding: 10px;
    }
    
    .content__title h1 {
        font-size: 1.4rem !important;
        flex-direction: column;
        text-align: center;
    }
    
    .live-info {
        grid-template-columns: 1fr;
    }
    
    .stats-number {
        font-size: 2rem;
    }
}
</style>

<section class="content">
    <div class="mis-lives-container">
        <header class="content__title">
            <h1><i class="zwicon-eye"></i> Mis Lives</h1>
        </header>
        
        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stats-number"><?php echo number_format($total_lives); ?></div>
            <div class="stats-label">Lives Totales Guardadas</div>
        </div>
        
        <!-- Botón de actualizar -->
        <div class="text-center mb-4">
            <button class="btn btn-refresh" onclick="location.reload()">
                <i class="zwicon-refresh"></i> Actualizar
            </button>
        </div>
        
        <!-- Lista de Lives -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($user_lives)): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="zwicon-eye"></i>
                                </div>
                                <div class="empty-text">No tienes lives guardadas</div>
                                <div class="empty-subtext">Las lives aparecerán aquí cuando uses el Amazon Gate</div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($user_lives as $live): ?>
                        <div class="live-card">
                            <div class="live-number">
                                <?php echo htmlspecialchars($live['live']); ?>
                            </div>
                            
                            <div class="live-info">
                                <div class="info-item">
                                    <div class="info-label">ID</div>
                                    <div class="info-value">#<?php echo $live['id']; ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Key</div>
                                    <div class="info-value"><?php echo htmlspecialchars($live['number_key']); ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Estado</div>
                                    <div class="info-value" style="color: #00ffc4;">✅ VIVA</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Tipo</div>
                                    <div class="info-value">CC</div>
                                </div>
                            </div>
                            
                            <div class="live-date">
                                Guardada el: <?php echo date('d/m/Y H:i', strtotime($live['created_at'] ?? 'now')); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Efecto de carga suave
document.addEventListener('DOMContentLoaded', function() {
    const liveCards = document.querySelectorAll('.live-card');
    
    liveCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Función para copiar live al portapapeles
function copyLive(liveText) {
    navigator.clipboard.writeText(liveText).then(function() {
        // Mostrar notificación de éxito
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00ffc4;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 700;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = 'Live copiada al portapapeles';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    });
}

// Hacer las lives clickeables para copiar
document.addEventListener('DOMContentLoaded', function() {
    const liveNumbers = document.querySelectorAll('.live-number');
    
    liveNumbers.forEach(liveNumber => {
        liveNumber.style.cursor = 'pointer';
        liveNumber.title = 'Click para copiar';
        
        liveNumber.addEventListener('click', function() {
            copyLive(this.textContent);
        });
    });
});

// CSS para la animación de notificación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
