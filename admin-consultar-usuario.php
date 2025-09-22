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

$resultado = null;
$mensaje = null;

// Eliminar usuario si se envió el formulario de eliminación
if (isset($_POST['eliminar_usuario']) && isset($_POST['usuario_id'])) {
    $usuario_id = intval($_POST['usuario_id']);
    // No permitir que el admin se elimine a sí mismo
    if ($usuario_id == $_SESSION['user_id']) {
        $mensaje = ["type" => "error", "text" => "No puedes eliminar tu propio usuario (admin actual).",];
    } else {
        $del = $connection->prepare("DELETE FROM breathe_users WHERE id=:id");
        $del->bindParam("id", $usuario_id, PDO::PARAM_INT);
        if ($del->execute()) {
            $mensaje = ["type" => "success", "text" => "Usuario eliminado correctamente."];
        } else {
            $mensaje = ["type" => "error", "text" => "Error al eliminar el usuario."];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['busqueda'])) {
    $busqueda = trim($_POST['busqueda']);
    $stmt = $connection->prepare("SELECT * FROM breathe_users WHERE email = :busqueda OR username = :busqueda");
    $stmt->bindParam("busqueda", $busqueda, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-search"></i> Consultar Usuario <small>Panel de Administración</small></h1>
    </header>
    <?php if ($mensaje): ?>
        <script>Swal.fire({icon: '<?php echo $mensaje["type"]; ?>', text: '<?php echo $mensaje["text"]; ?>', timer: 2000, showConfirmButton: false});</script>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Buscar por Email o Usuario</label>
                            <input type="text" name="busqueda" class="form-control" required>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="zwicon-search"></i> Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if ($resultado): ?>
    <div class="row justify-content-center mt-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="mb-3"><i class="zwicon-user"></i> Datos del Usuario</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><b>ID:</b> <?php echo $resultado['id']; ?></li>
                        <li class="list-group-item"><b>Email:</b> <?php echo $resultado['email']; ?></li>
                        <li class="list-group-item"><b>Usuario:</b> <?php echo $resultado['username']; ?></li>
                        <li class="list-group-item"><b>Key:</b> <?php echo $resultado['key_breathe']; ?></li>
                        <li class="list-group-item"><b>Suscripción:</b> <?php echo $resultado['suscripcion']; ?></li>
                        <li class="list-group-item"><b>Créditos:</b> <?php echo $resultado['creditos']; ?></li>
                        <li class="list-group-item"><b>Fecha Registro:</b> <?php echo $resultado['fech_reg']; ?></li>
                        <li class="list-group-item"><b>Activo:</b> <?php echo $resultado['active'] ? 'Sí' : 'No'; ?></li>
                    </ul>
                    <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                        <input type="hidden" name="usuario_id" value="<?php echo $resultado['id']; ?>">
                        <button type="submit" name="eliminar_usuario" class="btn btn-danger btn-lg"><i class="zwicon-trash"></i> Eliminar usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['busqueda'])): ?>
    <div class="row justify-content-center mt-4">
        <div class="col-lg-6">
            <div class="alert alert-danger">No se encontró ningún usuario con ese dato.</div>
        </div>
    </div>
    <?php endif; ?>
</section> 