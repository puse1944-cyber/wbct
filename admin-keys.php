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

function alert($message, $success = false) {
    $icon = $success ? "success" : "error";
    echo "<script>Swal.fire({icon: '$icon', text: '$message', timer: 2000, showConfirmButton: false});</script>";
}

// Procesar eliminación de key
if (isset($_POST['eliminar_key']) && isset($_POST['key_id'])) {
    $key_id = intval($_POST['key_id']);
    $del = $connection->prepare("DELETE FROM breathe_keys WHERE id=:id");
    $del->bindParam("id", $key_id, PDO::PARAM_INT);
    if ($del->execute()) {
        alert("Key eliminada correctamente.", true);
    } else {
        alert("Error al eliminar la key.");
    }
}

// Verificar si la tabla existe, si no, crearla
try {
    $create_table = $connection->prepare("
        CREATE TABLE IF NOT EXISTS breathe_keys (
            id int(11) NOT NULL AUTO_INCREMENT,
            number_key varchar(255) NOT NULL,
            credits int(11) NOT NULL DEFAULT 0,
            dias int(11) NOT NULL DEFAULT 30,
            active tinyint(1) NOT NULL DEFAULT 1,
            username varchar(255) DEFAULT NULL,
            fecha_reg date NOT NULL,
            fecha_inicio date NOT NULL,
            suscripcion varchar(50) NOT NULL DEFAULT '1',
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY number_key (number_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $create_table->execute();
} catch (Exception $e) {
    // Si hay error, mostrar mensaje
    echo "<script>Swal.fire({icon: 'error', text: 'Error al verificar tabla: " . $e->getMessage() . "', timer: 3000});</script>";
}

// Obtener todas las keys
try {
    $query = $connection->query("SELECT * FROM breathe_keys ORDER BY id DESC");
    $keys = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $keys = [];
    echo "<script>Swal.fire({icon: 'error', text: 'Error al cargar keys: " . $e->getMessage() . "', timer: 3000});</script>";
}
?>

<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-key"></i> Gestión de Keys</h1>
    </header>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Key</th>
                            <th>Créditos</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Fecha Expiración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keys as $key): ?>
                        <tr>
                            <td><?php echo $key['id']; ?></td>
                            <td><?php echo $key['number_key']; ?></td>
                            <td><?php echo $key['credits']; ?></td>
                            <td><?php echo $key['username']; ?></td>
                            <td>
                                <?php if ($key['active'] == 1): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $key['fecha_reg']; ?></td>
                            <td>
                                <?php
                                $fecha_base = !empty($key['fecha_inicio']) ? $key['fecha_inicio'] : $key['fecha_reg'];
                                $dias = intval($key['dias']);
                                $fecha_expiracion = date('Y-m-d', strtotime($fecha_base . "+$dias days"));
                                echo $fecha_expiracion;
                                ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="key_id" value="<?php echo $key['id']; ?>">
                                    <button type="submit" name="eliminar_key" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta key?')">
                                        <i class="zwicon-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include($path . "/static/v4/plugins/form/footer.php"); ?> 