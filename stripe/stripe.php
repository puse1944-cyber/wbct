<?php
// public_html/gates/stripe.php
// Página visual / placeholder para ZStripe — NO ejecuta procesamiento de tarjetas.

error_reporting(0);
$path = $_SERVER["DOCUMENT_ROOT"];
include($path."/api/v1.1/core/functions.php");
$user_info = get_data();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $site_page ?? 'Panel'; ?> | ✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Mantengo los mismos recursos que usas en tu dashboard -->
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <link rel="stylesheet" href="/static/v4/css/app.min.css">
    <link rel="stylesheet" href="/static/v4/css/app-dark.css">
    <link rel="stylesheet" href="/static/v4/css/shadow.css">
    <link rel="stylesheet" href="/static/v4/vendors/zwicon/zwicon.min.css">
    <style>
        /* Pequeños ajustes para esta vista */
        .content-wrapper { padding: 24px; }
        .stripe-card { background: linear-gradient(180deg,#0f1724 0%, #071023 100%); border-radius:12px; padding:22px; color:#e6eef8; box-shadow: 0 8px 30px rgba(0,0,0,0.6); }
        .stripe-cta { margin-top:16px; }
        .btn-safe { display:inline-block; padding:10px 16px; border-radius:8px; background:#00bcd4; color:#022; text-decoration:none; font-weight:600; box-shadow:0 6px 18px rgba(0,188,212,0.18); }
        .btn-secondary { display:inline-block; padding:8px 14px; border-radius:8px; background:transparent; color:#bcd; border:1px solid rgba(255,255,255,0.06); text-decoration:none; }
        .muted { color: #9fb0c9; font-size:13px; }
    </style>
</head>
<body data-sa-theme="10">
    <main class="main">
        <!-- Mantengo el header / sidebar que ya usas -->
        <header class="header">
            <div class="navigation-trigger d-xl-none" data-sa-action="aside-open" data-sa-target=".sidebar">
                <i class="zwicon-hamburger-menu"></i>
            </div>
            <div class="logo d-sm-inline-flex">
                <a href="/">[ <b>✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓</b> ]</a>
            </div>
            <div style="position: absolute; top: 10px; right: 20px; display:flex; align-items:center; z-index:10;">
                <span style="color:#fff; font-size:14px; margin-right:10px;"><b><?php echo htmlspecialchars($user_info["creditos"] ?? '0'); ?></b> Créditos</span>
            </div>
        </header>

        <aside class="sidebar">
            <div class="scrollbar">
                <div class="user">
                    <div class="user__info" data-toggle="dropdown">
                        <img class="user__img" src="/static/v4/img/okurachk.jpg">
                        <div class="user__name"><?php echo htmlspecialchars($user_info["username"] ?? 'Usuario'); ?></div>
                    </div>
                    <div class="dropdown-menu dropdown-menu--invert" style="padding: 10px; text-align:center;">
                        <a href="/logout" class="btn btn-info btn-sm" style="font-size:13px; padding:3px 10px; width:90%;">Cerrar Sesión</a>
                    </div>
                </div>
                <?php echo get_permissions(); ?>
                <a href="/" class="navigation__link"><i class="zwicon-home"></i></a>
                <a href="/gates/stripe.php" class="navigation__link active"><i class="zwicon-credit-card"></i> ZStripe</a>
                <hr style="margin:8px 0;">
                <a href="/canjear-key.php" class="navigation__link"><i class="zwicon-key"></i></a>
                <a href="/logout" class="btn btn-info btn-sm" style="font-size:13px; margin:15px 0; padding:3px 10px; width:90%;">Cerrar Sesión</a>
                <hr>
                <span>&copy; 2021 ─ 2022</span><br><span>[ ✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓ ]</span>
            </div>
        </aside>

        <!-- Contenido principal -->
        <section class="content-wrapper" style="margin-left: 280px; padding-top: 30px;">
            <div style="max-width:980px; margin: 0 auto;">
                <div class="stripe-card">
                    <h2 style="margin:0 0 8px 0;">ZStripe</h2>
                    <p class="muted">Interfaz visual para ZStripe. Aquí puedes integrar la pasarela de pago de forma segura (Stripe oficial) o bien mantener una vista de administración.</p>

                    <div style="display:flex; gap:12px; align-items:center; margin-top:18px;">
                        <!-- Botones seguros / placeholders -->
                        <a class="btn-safe" href="#" onclick="alert('Integración segura: implementa Stripe.js y PaymentIntents con claves de prueba.'); return false;">Configurar Stripe (legal)</a>
                        <a class="btn-secondary" href="#" onclick="alert('Vista previa. No procesa tarjetas.'); return false;">Vista previa</a>
                    </div>

                    <div class="stripe-cta" style="margin-top:18px;">
                        <div class="muted">Estado: <strong style="color:#9ff">Sólo interfaz — No procesamiento</strong></div>
                        <p style="margin-top:10px;">Si quieres, puedo generar aquí un formulario seguro usando <strong>Stripe Elements</strong> (modo pruebas), que no enviará ni almacenará tarjetas en tu servidor.</p>
                    </div>
                </div>

                <!-- Área de logs / información adicional -->
                <div style="margin-top:18px; color:#bcd">
                    <small class="muted">Información técnica: Este archivo está en <code>/gates/stripe.php</code> y actúa como interfaz gráfica. El código de procesamiento no está incluido por seguridad.</small>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
