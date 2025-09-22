<?php
session_start();
error_reporting(0);
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";
include($path . "/static/v4/plugins/form/header.php");

// Obtener datos del usuario actual
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

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $key_breathe = $_POST['key_breathe'];
    $suscripcion = intval($_POST['suscripcion']);
    $creditos = intval($_POST['creditos']);
    $fecha = date("Y-m-d");
    $active = 1;
    $sus_status = 1;
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Verificar si el correo ya existe
    $check = $connection->prepare("SELECT * FROM breathe_users WHERE email=:email");
    $check->bindParam("email", $email, PDO::PARAM_STR);
    $check->execute();
    if ($check->rowCount() > 0) {
        alert("El correo ya está registrado.");
    } else {
        $insert = $connection->prepare("INSERT INTO breathe_users (email, breathe_password, username, key_breathe, suscripcion, creditos, fech_reg, sus_reg, active, sus_status) VALUES (:email, :password, :username, :key_breathe, :suscripcion, :creditos, :fecha, '', :active, :sus_status)");
        $insert->bindParam("email", $email, PDO::PARAM_STR);
        $insert->bindParam("password", $password_hash, PDO::PARAM_STR);
        $insert->bindParam("username", $username, PDO::PARAM_STR);
        $insert->bindParam("key_breathe", $key_breathe, PDO::PARAM_STR);
        $insert->bindParam("suscripcion", $suscripcion, PDO::PARAM_INT);
        $insert->bindParam("creditos", $creditos, PDO::PARAM_INT);
        $insert->bindParam("fecha", $fecha, PDO::PARAM_STR);
        $insert->bindParam("active", $active, PDO::PARAM_INT);
        $insert->bindParam("sus_status", $sus_status, PDO::PARAM_INT);
        if ($insert->execute()) {
            alert("Usuario creado exitosamente!", true);
        } else {
            alert("Error al crear el usuario.");
        }
    }
}
?>
<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-user-plus"></i> Agregar Usuario <small>Panel de Administración</small></h1>
    </header>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Usuario</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Key</label>
                            <input type="text" name="key_breathe" class="form-control" required>
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
                            <label>Créditos</label>
                            <input type="number" name="creditos" class="form-control" value="10" min="0" required>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg"><i class="zwicon-user-plus"></i> Crear Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section> 