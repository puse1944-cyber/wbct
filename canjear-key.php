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

function alert($message, $success = false) {
    $icon = $success ? "success" : "error";
    echo "<script>Swal.fire({icon: '$icon', text: '$message', timer: 3000, showConfirmButton: false});</script>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['key'])) {
    $key = trim($_POST['key']);
    
    if (empty($key)) {
        alert("Por favor ingresa una key válida.");
    } else {
        // Buscar la key en la base de datos
        $check = $connection->prepare("SELECT * FROM breathe_keys WHERE number_key=:key AND active='1'");
        $check->bindParam("key", $key, PDO::PARAM_STR);
        $check->execute();
        $key_data = $check->fetch(PDO::FETCH_ASSOC);
        
        if (!$key_data) {
            alert("Key inválida o ya utilizada.");
        } else {
            // Verificar si la key ya fue usada por este usuario
            $used_check = $connection->prepare("SELECT * FROM breathe_keys_used WHERE key_id=:key_id AND user_id=:user_id");
            $used_check->bindParam("key_id", $key_data['id'], PDO::PARAM_INT);
            $used_check->bindParam("user_id", $_SESSION['user_id'], PDO::PARAM_INT);
            $used_check->execute();
            
            if ($used_check->rowCount() > 0) {
                alert("Esta key ya fue utilizada por ti.");
            } else {
                // Verificar si la key tiene usuario específico
                if (!empty($key_data['username']) && $key_data['username'] != $user['username']) {
                    alert("Esta key no es para tu usuario.");
                } else {
                    try {
                        $connection->beginTransaction();
                        
                        // Actualizar créditos del usuario
                        $new_credits = $user['creditos'] + $key_data['credits'];
                        $update_credits = $connection->prepare("UPDATE breathe_users SET creditos=:credits WHERE id=:user_id");
                        $update_credits->bindParam("credits", $new_credits, PDO::PARAM_INT);
                        $update_credits->bindParam("user_id", $_SESSION['user_id'], PDO::PARAM_INT);
                        $update_credits->execute();
                        
                        // Actualizar suscripción si es mayor
                        if ($key_data['suscripcion'] > $user['suscripcion']) {
                            $update_suscripcion = $connection->prepare("UPDATE breathe_users SET suscripcion=:suscripcion WHERE id=:user_id");
                            $update_suscripcion->bindParam("suscripcion", $key_data['suscripcion'], PDO::PARAM_INT);
                            $update_suscripcion->bindParam("user_id", $_SESSION['user_id'], PDO::PARAM_INT);
                            $update_suscripcion->execute();
                        }
                        
                        // Actualizar días de suscripción
                        if ($key_data['dias'] > 0) {
                            $fecha_fin = date('Y-m-d', strtotime('+' . $key_data['dias'] . ' days'));
                            $update_dias = $connection->prepare("UPDATE breathe_users SET suscripcion_fin=:fecha_fin WHERE id=:user_id");
                            $update_dias->bindParam("fecha_fin", $fecha_fin, PDO::PARAM_STR);
                            $update_dias->bindParam("user_id", $_SESSION['user_id'], PDO::PARAM_INT);
                            $update_dias->execute();
                        }
                        
                        // Marcar key como usada
                        $mark_used = $connection->prepare("INSERT INTO breathe_keys_used (key_id, user_id, fecha_uso) VALUES (:key_id, :user_id, :fecha)");
                        $mark_used->bindParam("key_id", $key_data['id'], PDO::PARAM_INT);
                        $mark_used->bindParam("user_id", $_SESSION['user_id'], PDO::PARAM_INT);
                        $mark_used->bindParam("fecha", date('Y-m-d H:i:s'), PDO::PARAM_STR);
                        $mark_used->execute();
                        
                        // Desactivar la key
                        $deactivate_key = $connection->prepare("UPDATE breathe_keys SET active='0' WHERE id=:key_id");
                        $deactivate_key->bindParam("key_id", $key_data['id'], PDO::PARAM_INT);
                        $deactivate_key->execute();
                        
                        $connection->commit();
                        
                        $message = "¡Key canjeada exitosamente! ";
                        $message .= "Créditos: +" . $key_data['credits'] . " ";
                        if ($key_data['dias'] > 0) {
                            $message .= "| Días: +" . $key_data['dias'] . " ";
                        }
                        if ($key_data['suscripcion'] > $user['suscripcion']) {
                            $message .= "| Suscripción mejorada!";
                        }
                        
                        alert($message, true);
                        
                        // Recargar la página para actualizar los datos del usuario
                        echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
                        
                    } catch (Exception $e) {
                        $connection->rollback();
                        alert("Error al procesar la key. Inténtalo de nuevo.");
                    }
                }
            }
        }
    }
}
?>

<style>
/* Estilos personalizados para Canjear Key */
.canjear-key-container {
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

.form-group label {
    color: #00ffc4 !important;
    font-weight: 600 !important;
    margin-bottom: 8px !important;
}

.form-control {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid #333 !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    padding: 12px 15px !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.form-control:focus {
    background: rgba(0, 0, 0, 0.9) !important;
    border-color: #00ffc4 !important;
    box-shadow: 0 0 8px rgba(0, 255, 196, 0.2) !important;
    color: #ffffff !important;
}

.form-control::placeholder {
    color: #666 !important;
}

.btn-theme {
    background: #00ffc4 !important;
    border: none !important;
    color: #000 !important;
    font-weight: 700 !important;
    border-radius: 25px !important;
    padding: 12px 30px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    box-shadow: 0 3px 8px rgba(0, 255, 196, 0.2) !important;
}

.btn-theme:hover {
    background: #0099ff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 5px 12px rgba(0, 255, 196, 0.3) !important;
}

.user-info {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}

.user-info h3 {
    color: #00ffc4;
    font-size: 1.3rem;
    margin-bottom: 15px;
    text-shadow: 0 0 5px rgba(0, 255, 196, 0.5);
}

.info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #333;
}

.info-label {
    color: #cccccc;
    font-weight: 500;
}

.info-value {
    color: #ffffff;
    font-weight: 700;
}

.info-value.credits {
    color: #00ffc4;
}

.info-value.subscription {
    color: #0099ff;
}

@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Responsive */
@media (max-width: 768px) {
    .canjear-key-container {
        padding: 10px;
    }
    
    .content__title h1 {
        font-size: 1.4rem !important;
        flex-direction: column;
        text-align: center;
    }
    
    .btn-theme {
        padding: 10px 25px !important;
        font-size: 0.9rem !important;
    }
}
</style>

<section class="content">
    <div class="canjear-key-container">
        <header class="content__title">
            <h1><i class="zwicon-key"></i> Canjear Key</h1>
        </header>
        
        <!-- Información del usuario -->
        <div class="user-info">
            <h3><i class="zwicon-user"></i> Tu Información</h3>
            <div class="info-item">
                <span class="info-label">Usuario:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Créditos:</span>
                <span class="info-value credits"><?php echo number_format($user['creditos']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Suscripción:</span>
                <span class="info-value subscription">
                    <?php 
                    $suscripciones = [1 => 'Usuario Normal', 2 => 'CC Storage', 3 => 'Administrador'];
                    echo $suscripciones[$user['suscripcion']] ?? 'Desconocida';
                    ?>
                </span>
            </div>
            <?php if (!empty($user['suscripcion_fin'])): ?>
            <div class="info-item">
                <span class="info-label">Suscripción hasta:</span>
                <span class="info-value"><?php echo date('d/m/Y', strtotime($user['suscripcion_fin'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="key"><i class="zwicon-key"></i> Código de Key</label>
                                <input type="text" 
                                       id="key" 
                                       name="key" 
                                       class="form-control" 
                                       placeholder="Ingresa tu key aquí (ej: ☂ DARK CT ☂-ABC12345)"
                                       required
                                       autocomplete="off">
                                <small class="form-text text-muted">
                                    Ingresa la key completa que recibiste
                                </small>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-theme btn-lg">
                                    <i class="zwicon-checkmark-circle"></i> Canjear Key
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Información sobre las keys -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="text-center mb-3" style="color: #00ffc4;">
                            <i class="zwicon-info-circle"></i> ¿Qué son las Keys?
                        </h5>
                        <div style="color: #cccccc; font-size: 0.9rem;">
                            <p><strong>Las Keys son códigos especiales que contienen:</strong></p>
                            <ul style="padding-left: 20px;">
                                <li><strong>Créditos:</strong> Se suman a tu cuenta actual</li>
                                <li><strong>Días de suscripción:</strong> Extienden tu acceso premium</li>
                                <li><strong>Nivel de suscripción:</strong> Pueden mejorar tu rango</li>
                            </ul>
                            <p class="mt-3"><strong>Nota:</strong> Cada key solo puede ser utilizada una vez.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Efecto de focus en el input
document.addEventListener('DOMContentLoaded', function() {
    const keyInput = document.getElementById('key');
    
    keyInput.addEventListener('focus', function() {
        this.style.borderColor = '#00ffc4';
        this.style.boxShadow = '0 0 10px rgba(0, 255, 196, 0.3)';
    });
    
    keyInput.addEventListener('blur', function() {
        this.style.borderColor = '#333';
        this.style.boxShadow = 'none';
    });
    
    // Auto-focus en el input
    keyInput.focus();
});
</script>
