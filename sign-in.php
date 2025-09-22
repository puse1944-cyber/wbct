<?php
error_reporting(0);
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
    $url  = "/user/sign-in";

    if ($success == true) {
        $icon = "success";
        $url  = "/";
        echo "<script>document.getElementById('iniciaSonido').play();</script>";
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
        animation: fadeInUp 1s ease;
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
      @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
      }
      .login-box img {
        width:120px; height:120px;
        object-fit:cover;
        border-radius:50%;
        box-shadow:0 0 20px rgba(139,92,246,0.7);
        margin-bottom:25px;
      }
      .login-box h4 {
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
      }
      .input-group {
        display: flex;
        align-items: center;
        background: rgba(31,41,55,0.85);
        border-radius: 10px;
        box-shadow: inset 0 0 8px rgba(59,130,246,0.4);
        margin-bottom: 18px;
        padding-right: 10px;
      }
      .input-group i.zwicon-lock,
      .input-group i.zwicon-mail {
        margin-left: 12px;
        color: #60a5fa;
        font-size: 18px;
      }
      .input-group input {
        flex: 1;
        padding: 14px;
        border: none;
        background: transparent;
        color: #fff;
        outline: none;
      }
      .toggle-password {
        margin-left: 10px;
        cursor: pointer;
        color: #8b5cf6;
        font-size: 18px;
        flex-shrink: 0;
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
        font-weight:600;
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
    <audio id="iniciaSonido">
        <source src="/static/v4/sounds/iniciar.mp3" type="audio/mpeg">
    </audio>

    <div class="login-box">
        <img src="/static/v4/img/photo_2025-07-09_13-53-04.jpg" alt="Logo Okura">
        <h4>[ <b>✓ 𝙾𝙾𝙆𝚄𝚁𝙰𝙲𝙃𝙺 ✓</b> ]</h4>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $final_token; ?>">

            <div class="input-group">
                <i class="zwicon-mail"></i>
                <input type="text" name="email" placeholder="Correo OkuraChk" required autofocus>
            </div>

            <div class="input-group">
                <i class="zwicon-lock"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña OkuraChk" required>
                <i class="zwicon-eye toggle-password" onclick="togglePassword(this)"></i>
            </div>

            <button type="submit"><i class="zwicon-sign-in"></i> Iniciar Sesión</button>
            <a href="../sign-up.php">¿No tienes cuenta? Regístrate</a>
        </form>
    </div>

    <script>
      function togglePassword(el) {
        const passField = document.getElementById("password");
        if (passField.type === "password") {
          passField.type = "text";
          el.style.color = "#06b6d4";
        } else {
          passField.type = "password";
          el.style.color = "#8b5cf6";
        }
      }
    </script>

<?php
if (empty($csrf_token)) {
    exit;
}

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

    $username = $_POST["email"];
    $password = $_POST["password"];

    $query = $connection->prepare("SELECT * FROM breathe_users WHERE EMAIL=:username");
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        alert("El usuario no existe");
    } else {
        if ($result["creditos"] <= 1) {
            alert("Creditos Insuficientes");
        } else if (password_verify($password, $result["breathe_password"])) {
            $_SESSION["user_id"] = $result["id"];
            alert("Bienvenido a ✓ 𝙾𝙾𝙆𝚄𝚁𝙰𝙲𝙃𝙺 ✓", true);
        } else {
            alert("Contraseña incorrecta");
        }
    }
}
$_SESSION["csrf_time"] = date("Y-n-j H:i:s");
?>
</body>
</html>
