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
        echo "<script>document.getElementById('iniciaSonido').play().catch(e => console.log('Audio no disponible:', e));</script>";
    }

    echo "<script>let timerInterval; Swal.fire({icon: '" . $icon . "', text: '" . $message . "', timer: 1800, timerProgressBar: true, didOpen: ()=>{Swal.showLoading(); const b=Swal.getHtmlContainer().querySelector('b'); timerInterval=setInterval(()=>{if(b) b.textContent=Swal.getTimerLeft();}, 100);}, willClose: ()=>{clearInterval(timerInterval);},}).then((result)=>{if (result.dismiss===Swal.DismissReason.timer){window.location='" . $url . "';}});</script>";
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
    <title>☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂</title>
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
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      
      body {
        background: #000000;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Orbitron', sans-serif;
        overflow: hidden;
        position: relative;
      }
      
      /* Fondo con partículas doradas */
      body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
          radial-gradient(circle at 20% 80%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
          radial-gradient(circle at 80% 20%, rgba(255, 215, 0, 0.08) 0%, transparent 50%),
          radial-gradient(circle at 40% 40%, rgba(255, 215, 0, 0.06) 0%, transparent 50%);
        animation: goldenGlow 8s ease-in-out infinite alternate;
        z-index: 1;
      }
      
      /* Partículas doradas flotantes */
      .golden-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 2;
      }
      
      .particle {
        position: absolute;
        width: 2px;
        height: 2px;
        background: #FFD700;
        border-radius: 50%;
        animation: float 6s infinite linear;
        box-shadow: 0 0 6px #FFD700;
      }
      
      .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 8s; }
      .particle:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 6s; }
      .particle:nth-child(3) { left: 30%; animation-delay: 2s; animation-duration: 7s; }
      .particle:nth-child(4) { left: 40%; animation-delay: 3s; animation-duration: 5s; }
      .particle:nth-child(5) { left: 50%; animation-delay: 4s; animation-duration: 9s; }
      .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 6s; }
      .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; animation-duration: 8s; }
      .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; animation-duration: 7s; }
      .particle:nth-child(9) { left: 90%; animation-delay: 2.5s; animation-duration: 5s; }
      .particle:nth-child(10) { left: 15%; animation-delay: 3.5s; animation-duration: 6s; }
      
      .login-box {
        position: relative;
        z-index: 10;
        overflow: hidden;
        padding: 50px 40px;
        border-radius: 20px;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(20px);
        width: 420px;
        text-align: center;
        box-shadow: 
          0 0 50px rgba(255, 215, 0, 0.3),
          inset 0 0 50px rgba(255, 215, 0, 0.1);
        animation: fadeInUp 1.2s ease;
        border: 2px solid transparent;
        background-clip: padding-box;
      }
      
      /* Contorno RGB animado */
      .login-box::before {
        content: "";
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(45deg, 
          #ff0000, #ff8000, #ffff00, #80ff00, 
          #00ff00, #00ff80, #00ffff, #0080ff, 
          #0000ff, #8000ff, #ff00ff, #ff0080, 
          #ff0000);
        background-size: 300% 300%;
        border-radius: 22px;
        z-index: -1;
        animation: rgbBorder 3s linear infinite;
      }
      
      .login-box::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.95);
        border-radius: 18px;
        z-index: -1;
      }
      
      .login-box img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 
          0 0 30px rgba(255, 215, 0, 0.8),
          0 0 60px rgba(255, 215, 0, 0.4);
        margin-bottom: 30px;
        border: 3px solid rgba(255, 215, 0, 0.6);
        animation: logoGlow 2s ease-in-out infinite alternate;
      }
      
      .login-box h4 {
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: 700;
        color: #FFD700;
        text-shadow: 
          0 0 10px rgba(255, 215, 0, 0.8),
          0 0 20px rgba(255, 215, 0, 0.4);
        animation: textGlow 2s ease-in-out infinite alternate;
      }
      
      .input-group {
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.8);
        border-radius: 15px;
        margin-bottom: 20px;
        padding-right: 15px;
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
        overflow: hidden;
      }
      
      .input-group::before {
        content: "";
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, 
          #ff0000, #00ff00, #0000ff, #ffff00, 
          #ff00ff, #00ffff, #ff0000);
        background-size: 400% 400%;
        border-radius: 17px;
        z-index: -1;
        animation: inputBorder 2s linear infinite;
      }
      
      .input-group i.zwicon-lock,
      .input-group i.zwicon-mail {
        margin-left: 15px;
        color: #FFD700;
        font-size: 20px;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
      }
      
      .input-group input {
        flex: 1;
        padding: 16px;
        border: none;
        background: transparent;
        color: #FFD700;
        outline: none;
        font-size: 16px;
        font-family: 'Orbitron', sans-serif;
      }
      
      .input-group input::placeholder {
        color: rgba(255, 215, 0, 0.6);
      }
      
      .toggle-password {
        margin-left: 12px;
        cursor: pointer;
        color: #FFD700;
        font-size: 20px;
        flex-shrink: 0;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
        transition: all 0.3s ease;
      }
      
      .toggle-password:hover {
        color: #FFA500;
        text-shadow: 0 0 15px rgba(255, 165, 0, 1);
      }
      
      .login-box button {
        width: 100%;
        padding: 16px;
        border: none;
        border-radius: 15px;
        font-weight: 700;
        font-size: 16px;
        color: #000;
        background: linear-gradient(45deg, #FFD700, #FFA500, #FFD700);
        background-size: 200% 200%;
        box-shadow: 
          0 0 20px rgba(255, 215, 0, 0.6),
          inset 0 0 20px rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
        font-family: 'Orbitron', sans-serif;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: buttonGlow 2s ease-in-out infinite alternate;
      }
      
      .login-box button:hover {
        background-position: 100% 0;
        box-shadow: 
          0 0 30px rgba(255, 215, 0, 0.8),
          inset 0 0 30px rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
      }
      
      .login-box a {
        display: block;
        margin-top: 20px;
        color: #FFD700;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
      }
      
      .login-box a:hover {
        color: #FFA500;
        text-shadow: 0 0 15px rgba(255, 165, 0, 1);
        transform: translateY(-1px);
      }
      
      /* Animaciones */
      @keyframes goldenGlow {
        0% { opacity: 0.3; }
        100% { opacity: 0.8; }
      }
      
      @keyframes float {
        0% {
          transform: translateY(100vh) rotate(0deg);
          opacity: 0;
        }
        10% {
          opacity: 1;
        }
        90% {
          opacity: 1;
        }
        100% {
          transform: translateY(-100px) rotate(360deg);
          opacity: 0;
        }
      }
      
      @keyframes rgbBorder {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
      }
      
      @keyframes inputBorder {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
      }
      
      @keyframes fadeInUp {
        0% { 
          opacity: 0; 
          transform: translateY(50px) scale(0.9); 
        }
        100% { 
          opacity: 1; 
          transform: translateY(0) scale(1); 
        }
      }
      
      @keyframes logoGlow {
        0% { 
          box-shadow: 
            0 0 30px rgba(255, 215, 0, 0.8),
            0 0 60px rgba(255, 215, 0, 0.4);
        }
        100% { 
          box-shadow: 
            0 0 40px rgba(255, 215, 0, 1),
            0 0 80px rgba(255, 215, 0, 0.6);
        }
      }
      
      @keyframes textGlow {
        0% { 
          text-shadow: 
            0 0 10px rgba(255, 215, 0, 0.8),
            0 0 20px rgba(255, 215, 0, 0.4);
        }
        100% { 
          text-shadow: 
            0 0 15px rgba(255, 215, 0, 1),
            0 0 30px rgba(255, 215, 0, 0.6);
        }
      }
      
      @keyframes buttonGlow {
        0% { 
          box-shadow: 
            0 0 20px rgba(255, 215, 0, 0.6),
            inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        100% { 
          box-shadow: 
            0 0 30px rgba(255, 215, 0, 0.8),
            inset 0 0 30px rgba(255, 255, 255, 0.3);
        }
      }
    </style>
</head>
<body>
    <audio id="iniciaSonido" preload="none">
        <source src="/static/v4/sounds/iniciar.wav" type="audio/wav">
        <source src="/static/v4/sounds/iniciar.mp3" type="audio/mpeg">
    </audio>

    <!-- Partículas doradas flotantes -->
    <div class="golden-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-box">
        <img src="/static/v4/img/photo_2025-07-09_13-53-04.jpg" alt="Logo Okura">
        <h4>[ <b>☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂</b> ]</h4>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $final_token; ?>">

            <div class="input-group">
                <i class="zwicon-mail"></i>
                <input type="text" name="email" placeholder="Correo" required autofocus>
            </div>

            <div class="input-group">
                <i class="zwicon-lock"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
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

    $query = $connection->prepare("SELECT * FROM breathe_users WHERE EMAIL=:username OR USERNAME=:username");
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
            
            // Integrar Login Monitor
            try {
                require_once "./api/v1.1/core/login_monitor_integration.php";
                registerUserLogin($result["id"], $result["username"]);
            } catch (Exception $e) {
                error_log("Error en Login Monitor: " . $e->getMessage());
            }
            
            alert("Bienvenido a ☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂", true);
        } else {
            alert("Contraseña incorrecta");
        }
    }
}
$_SESSION["csrf_time"] = date("Y-n-j H:i:s");
?>
</body>
</html>
