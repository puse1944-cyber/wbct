<?php
session_start();
extract($_POST);

if (isset($_SESSION["user_id"])) {
    header("Location: .");
    exit;
}

function generate_token()
{
    if (!isset($_SESSION["csrf_token"])) {
        $token = random_bytes(64);
        $_SESSION["csrf_token"] = $token;
    } else {
        $token = $_SESSION["csrf_token"];
    }
    return $token;
}

function alert($message, $success = false)
{
    $icon = "error";
    $url  = "/user/sign-up";

    if ($success == true) {
        $icon = "success";
        $url  = "/";
    }

    echo "<script>let timerInterval; Swal.fire({icon: '" . $icon . "', text: '" . $message . "', timer: 1800, timerProgressBar: true, didOpen: ()=>{Swal.showLoading(); const b=Swal.getHtmlContainer().querySelector('b'); timerInterval=setInterval(()=>{b.textContent=Swal.getTimerLeft();}, 100);}, willClose: ()=>{clearInterval(timerInterval);},}).then((result)=>{if (result.dismiss===Swal.DismissReason.timer){window.location='" . $url . "';}});</script>";
}

if (!isset($_SESSION["csrf_rnd"])) {
    $token_rnd = random_bytes(8);
    $_SESSION["csrf_rnd"] = $token_rnd;
} else {
    $token_rnd = $_SESSION["csrf_rnd"];
}

$final_token = base64_encode(generate_token()) . "." . base64_encode($token_rnd);

if (!isset($_SESSION["csrf_time"])) {
    $_SESSION["csrf_time"] = date("Y-n-j H:i:s");
}

$savedDate   = $_SESSION["csrf_time"];
$currentDate = date("Y-n-j H:i:s");
$tiempo_transcurrido = (strtotime($currentDate) - strtotime($savedDate));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>✓ 𝙾𝙾𝙆𝚄𝚁𝙰𝙲𝙃𝙺 ✓</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <script src="//code.jquery.com/jquery-3.6.0.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/static/v4/vendors/zwicon/zwicon.min.css">
    <link rel="stylesheet" href="/static/v4/plugins/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css">
    <link rel="stylesheet" href="/static/v4/vendors/animate.css/animate.min.css">
    <link rel="stylesheet" href="/static/v4/css/app.min.css">
    <link rel="stylesheet" href="/static/v4/css/app-dark.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">

    <style>
      body {
        background: radial-gradient(circle at top, #0f172a, #020617);
        min-height: 100vh;
        display:flex;
        justify-content:center;
        align-items:center;
        font-family:'Orbitron', sans-serif;
      }
      .login-box {
        position: relative;
        z-index: 1;
        overflow: hidden;
        padding: 45px 35px;
        border-radius: 18px;
        background: rgba(17,24,39,0.75);
        backdrop-filter: blur(18px);
        width: 400px;
        text-align: center;
        box-shadow: 0 8px 32px rgba(0,0,0,0.7);
      }
      .login-box::before {
        content: "";
        position: absolute;
        top: -2px; left: -2px; right: -2px; bottom: -2px;
        background: linear-gradient(90deg,#06b6d4,#3b82f6,#8b5cf6,#06b6d4);
        background-size: 300% 300%;
        border-radius: 20px;
        z-index: -1;
        animation: borderGlow 6s linear infinite;
      }
      @keyframes borderGlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
      }
      .login-box h4 {
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: 700;
        background: linear-gradient(90deg,#06b6d4,#3b82f6,#8b5cf6);
        -webkit-background-clip:text;
        -webkit-text-fill-color: transparent;
      }
      .input-group {
        position: relative;
        margin-bottom: 18px;
      }
      .input-group input {
        width: 100%;
        padding: 14px 40px 14px 45px;
        border-radius: 10px;
        border: none;
        background: rgba(31,41,55,0.85);
        color: #fff;
        box-shadow: inset 0 0 8px rgba(59,130,246,0.4);
        outline: none;
        transition: 0.3s;
      }
      .input-group input:focus {
        box-shadow: 0 0 12px rgba(59,130,246,0.8), inset 0 0 8px rgba(59,130,246,0.4);
      }
      .input-group i {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #60a5fa;
        font-size: 18px;
      }
      .toggle-password {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #8b5cf6;
        font-size: 18px;
      }
      .login-box button {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
        background: linear-gradient(90deg,#06b6d4,#3b82f6,#8b5cf6);
        background-size: 300% 100%;
        box-shadow: 0 0 15px rgba(59,130,246,0.7);
        cursor: pointer;
        transition: 0.3s;
        margin-top: 8px;
      }
      .login-box button:hover {
        background-position: 100% 0;
        box-shadow: 0 0 20px rgba(139,92,246,0.8);
      }
      .login-box a {
        display:block;
        margin-top: 18px;
        color:#60a5fa;
        font-size:14px;
        text-decoration:none;
        transition:0.3s;
      }
      .login-box a:hover {
        color:#8b5cf6;
        text-shadow:0 0 6px rgba(139,92,246,0.7);
      }
    </style>
</head>
<body>
    <div class="login-box">
        <h4>[ <b>✓ 𝙾𝙾𝙆𝚄𝚁𝙰𝙲𝙃𝙺 ✓</b> ]</h4>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $final_token; ?>">

            <div class="input-group">
                <i class="zwicon-key"></i>
                <input type="text" name="key" placeholder="Key" required>
            </div>

            <div class="input-group">
                <i class="zwicon-user"></i>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
            </div>

            <div class="input-group">
                <i class="zwicon-mail"></i>
                <input type="text" name="email" placeholder="Correo OkuraChk" required>
            </div>

            <div class="input-group">
                <i class="zwicon-lock"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña OkuraChk" required>
                <i class="zwicon-eye toggle-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit"><i class="zwicon-sign-in"></i> OKURACHK REGISTRO</button>
            <a href="../sign-in.php">¿Ya tienes cuenta? Iniciar sesión</a>
        </form>
    </div>

    <script>
      function togglePassword() {
        const passField = document.getElementById("password");
        const icon = document.querySelector(".toggle-password");
        if (passField.type === "password") {
          passField.type = "text";
          icon.classList.remove("zwicon-eye");
          icon.classList.add("zwicon-eye-off");
        } else {
          passField.type = "password";
          icon.classList.remove("zwicon-eye-off");
          icon.classList.add("zwicon-eye");
        }
      }
    </script>

<?php
if (empty($csrf_token)) { exit; }

if ($tiempo_transcurrido >= 120 or $csrf_token != $final_token) {
    unset($csrf_token);
    unset($_SESSION["csrf_time"]);
    unset($_SESSION["csrf_rnd"]);
    unset($_SESSION["csrf_token"]);
    alert("Captcha error, recargando pagina");
    exit;
}

if ($csrf_token == $final_token) {
    require "./api/v1.1/core/brain.php";

    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $hoy = date("j/n/Y");
    $key_breathe = $_POST['key'];
    $username = $_POST['username'];

    $query = $connection->prepare("SELECT * FROM breathe_keys WHERE NUMBER_KEY=:key_breathe AND active=1");
    $query->bindParam("key_breathe", $key_breathe, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() > 0) {
        $query1 = $connection->prepare("SELECT * FROM breathe_users WHERE KEY_BREATHE=:key_breathe");
        $query1->bindParam("key_breathe", $key_breathe, PDO::PARAM_STR);
        $query1->execute();
        if ($query1->rowCount() > 0) {
            alert("El usuario ya existe");
        } else {
            $credits = $result['credits'];
            $query = $connection->prepare("INSERT INTO breathe_users(EMAIL,BREATHE_PASSWORD,USERNAME,KEY_BREATHE,SUSCRIPCION,CREDITOS,FECH_REG,ACTIVE) VALUES (:email,:password_hash,:username,:key_breathe,'1',:credits,:hoy,'1')");
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
            $query->bindParam("username", $username, PDO::PARAM_STR);
            $query->bindParam("key_breathe", $key_breathe, PDO::PARAM_STR);
            $query->bindParam("credits", $credits, PDO::PARAM_STR);
            $query->bindParam("hoy", $hoy, PDO::PARAM_STR);
            $query->execute();

            if ($result) {
                alert("Registro satisfactorio!", true);
                $updateKey = $connection->prepare("UPDATE breathe_keys SET username=:username WHERE number_key=:key_breathe");
                $updateKey->bindParam("username", $username, PDO::PARAM_STR);
                $updateKey->bindParam("key_breathe", $key_breathe, PDO::PARAM_STR);
                $updateKey->execute();
            } else {
                alert("Ha ocurrido un error!");
            }
        }
    }

    if ($query->rowCount() == 0) {
        alert("El usuario no existe");
    }
}
$_SESSION["csrf_time"] = date("Y-n-j H:i:s");
?>
</body>
</html>
