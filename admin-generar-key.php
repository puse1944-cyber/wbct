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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $number_key = $_POST['number_key'];
    $credits = intval($_POST['credits']);
    $dias = intval($_POST['dias']);
    $active = $_POST['active'];
    $username = $_POST['username'];
    $fecha_reg = date("Y-m-d");
    $fecha_inicio = date("Y-m-d");
    $suscripcion = $_POST['suscripcion'];

    // Verificar si la key ya existe
    $check = $connection->prepare("SELECT * FROM breathe_keys WHERE number_key=:number_key");
    $check->bindParam("number_key", $number_key, PDO::PARAM_STR);
    $check->execute();
    if ($check->rowCount() > 0) {
        alert("La key ya existe.");
    } else {
        $insert = $connection->prepare("INSERT INTO breathe_keys (number_key, credits, dias, active, username, fecha_reg, fecha_inicio, suscripcion) VALUES (:number_key, :credits, :dias, :active, :username, :fecha_reg, :fecha_inicio, :suscripcion)");
        $insert->bindParam("number_key", $number_key, PDO::PARAM_STR);
        $insert->bindParam("credits", $credits, PDO::PARAM_INT);
        $insert->bindParam("dias", $dias, PDO::PARAM_INT);
        $insert->bindParam("active", $active, PDO::PARAM_STR);
        $insert->bindParam("username", $username, PDO::PARAM_STR);
        $insert->bindParam("fecha_reg", $fecha_reg, PDO::PARAM_STR);
        $insert->bindParam("fecha_inicio", $fecha_inicio, PDO::PARAM_STR);
        $insert->bindParam("suscripcion", $suscripcion, PDO::PARAM_STR);
        if ($insert->execute()) {
            alert("Key generada exitosamente!", true);
        } else {
            alert("Error al generar la key.");
        }
    }
}

// Función para generar la key en PHP (por si quieres usarla en el backend)
function generarKeyCatchk() {
    return '☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂-' . strtoupper(substr(md5(uniqid()), 0, 8));
}
?>
<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-key"></i> Generar Key <small>Panel de Administración</small></h1>
    </header>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Key</label>
                            <input type="text" id="number_key" name="number_key" class="form-control" readonly required>
                        </div>
                        <div class="form-group">
                            <label>Créditos</label>
                            <input type="number" name="credits" class="form-control" value="10" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Días</label>
                            <input type="number" name="dias" class="form-control" value="30" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Usuario (opcional)</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Suscripción</label>
                            <select name="suscripcion" class="form-control" required>
                                <option value="1">Usuario Normal</option>
                                <option value="2">CC Storage</option>
                                <option value="3">Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="active" class="form-control" required>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-warning btn-lg"><i class="zwicon-key"></i> Generar Key</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
function generarKeyCatchk() {
    let random = Math.random().toString(36).substring(2, 10).toUpperCase();
    return '☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂-' + random;
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('number_key').value = generarKeyCatchk();
});
</script> 