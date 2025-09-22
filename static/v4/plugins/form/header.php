<?php 
error_reporting(0); 
$path = $_SERVER["DOCUMENT_ROOT"]; 
include($path."/api/v1.1/core/functions.php"); 

$user_info = get_data(); 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <title><?php echo $site_page; ?> | ✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓</title> 
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"> 
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script src="/static/v4/vendors/popper.js/popper.min.js"></script> 
    <script src="/static/v4/vendors/bootstrap/js/bootstrap.min.js"></script> 
    <script src="/static/v4/vendors/overlay-scrollbars/jquery.overlayScrollbars.min.js"></script> 
    <script src="/static/v4/js/bootstrap-progressbar.min.js"></script> 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="/static/v4/plugins/form/summernote/summernote.min.js"></script> 
    <script src="/static/v4/js/app.min.js"></script> 
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script> 
    <link rel="stylesheet" href="/static/v4/vendors/zwicon/zwicon.min.css"> 
    <link rel="stylesheet" href="/static/v4/vendors/animate.css/animate.min.css"> 
    <link rel="stylesheet" href="/static/v4/vendors/overlay-scrollbars/OverlayScrollbars.min.css"> 
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous"> 
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css"> 
    <link rel="stylesheet" href="/static/v4/css/app.min.css"> 
    <link rel="stylesheet" href="/static/v4/css/shadow.css"> 
    <link rel="stylesheet" href="/static/v4/css/bootstrap-progressbar-3.3.4.min.css"> 
    <link rel="stylesheet" href="/static/v4/plugins/form/summernote/summernote.css"> 
    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Righteous&display=swap"> 
    <link rel="stylesheet" href="/static/v4/css/app-dark.css"> 
</head> 
<body data-sa-theme="10"> 
    <main class="main"> 
        <div class="page-loader"> 
            <div class="page-loader__spinner"> 
                <svg viewBox="25 25 50 50"> 
                    <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"> 
                </svg> 
            </div> 
        </div> 
        <header class="header"> 
            <div class="navigation-trigger d-xl-none" data-sa-action="aside-open" data-sa-target=".sidebar"> 
                <i class="zwicon-hamburger-menu"></i> 
            </div> 
            <div class="logo d-sm-inline-flex"> 
                <a href="/">[ <b>✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓</b> ]</a> 
            </div> 
            <div style="position: absolute; top: 10px; right: 20px; display: flex; align-items: center; z-index: 10;"> 
                <span style="color:#fff; font-size:14px; margin-right:10px;"><b><?php echo $user_info["creditos"]; ?></b> Créditos</span> 
            </div> 
        </header> 
        <aside class="sidebar"> 
            <div class="scrollbar"> 
                <div class="user"> 
                    <div class="user__info" data-toggle="dropdown"> 
                        <img class="user__img" src="/static/v4/img/okurachk.jpg"> 
                        <div class="user__name"> 
                            <?php echo $user_info["username"]; ?> 
                        </div> 
                    </div> 
                    <div class="dropdown-menu dropdown-menu--invert" style="padding: 10px; text-align: center;"> 
                        <a href="/logout" class="btn btn-info btn-sm" style="font-size:13px; padding:3px 10px; box-shadow:0 0 10px #00eaff99; display: inline-block; width: 90%;"> 
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión 
                        </a> 
                    </div> 
                </div> 
                <?php echo get_permissions(); ?> 
                <a href="/" class="navigation__link"> 
                    <i class="zwicon-home"></i> 
                </a> 

                <!-- ZStripe como botón directo --> 
               <a href="/stripe/stripe.php" class="navigation__link"> 
    <i class="zwicon-credit-card"></i> Stripe Nuevo 
</a>

                <hr style="margin:8px 0;"> 
                <a href="/canjear-key.php" class="navigation__link"> 
                    <i class="zwicon-key"></i> 
                </a> 

                <a href="/logout" class="btn btn-info btn-sm" style="font-size:13px; margin: 15px 0 10px 0; padding:3px 10px; box-shadow:0 0 10px #00eaff99; display: block; width: 90%; text-align: center; margin-left: auto; margin-right: auto;"> 
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión 
                </a> 
                <hr> 
                <span>&copy; 2021 ─ 2022</span> 
                <br> 
                <span>[ ✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓ ]</span> 
            </div> 
        </aside> 
    </main> 
</body> 
</html>
