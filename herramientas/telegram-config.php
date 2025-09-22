<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$path = $_SERVER["DOCUMENT_ROOT"];
include($path."/static/v4/plugins/form/header.php");
require_once $path . "/api/v1.1/core/brain.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: /sign-in.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$mensaje = '';
$mensaje_tipo = '';

// Obtener el ID de Telegram actual del usuario
$stmt = $connection->prepare("SELECT telegram_chat_id FROM breathe_users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$telegram_actual = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telegram_id = trim($_POST['telegram_id']);
    try {
        $stmt = $connection->prepare("UPDATE breathe_users SET telegram_chat_id = :telegram_id WHERE id = :user_id");
        $stmt->bindParam(':telegram_id', $telegram_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $mensaje = 'ID de Telegram agregado correctamente.';
            $mensaje_tipo = 'success';
        } else {
            $mensaje = 'Error al agregar el ID.';
            $mensaje_tipo = 'error';
        }
    } catch (PDOException $e) {
        $mensaje = 'Error de base de datos: ' . $e->getMessage();
        $mensaje_tipo = 'error';
    }
}
?>
<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-telegram" style="color:#229ED9;"></i> Agregar ID de Telegram</h1>
    </header>
    <?php if ($telegram_actual): ?>
    <div class="alert alert-info" style="font-size:16px;">Ya tienes un ID de Telegram agregado: <b><?php echo htmlspecialchars($telegram_actual); ?></b></div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form method="POST" autocomplete="off">
                        <div class="form-group">
                            <label for="telegram_id">ID de Telegram</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:10px;top:8px;color:#229ED9;font-size:20px;">
                                    <i class="zwicon-telegram"></i>
                                </span>
                                <input type="text" class="form-control" id="telegram_id" name="telegram_id" required style="padding-left:38px;" placeholder="Ingresa tu ID de Telegram">
                        </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-info btn-lg" style="background:#229ED9;border:none;font-size:16px;">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section> 
<?php if ($mensaje): ?>
<script>
Swal.fire({
    icon: '<?php echo $mensaje_tipo; ?>',
    text: '<?php echo $mensaje; ?>',
    timer: 4000,
    showConfirmButton: true
});
</script>
<?php endif; ?> 