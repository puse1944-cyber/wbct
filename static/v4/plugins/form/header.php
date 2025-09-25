<?php 
error_reporting(0); 
$path = $_SERVER["DOCUMENT_ROOT"]; 
include($path."/api/v1.1/core/functions.php"); 

$user_info = get_data(); 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <title><?php echo $site_page; ?> | ☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂</title> 
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"> 
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script src="/static/v4/vendors/popper.js/popper.min.js?v=<?php echo time(); ?>"></script> 
    <script src="/static/v4/vendors/bootstrap/js/bootstrap.min.js?v=<?php echo time(); ?>"></script> 
    <script src="/static/v4/vendors/overlay-scrollbars/jquery.overlayScrollbars.min.js?v=<?php echo time(); ?>"></script> 
    <script src="/static/v4/js/bootstrap-progressbar.min.js?v=<?php echo time(); ?>"></script> 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="/static/v4/plugins/form/summernote/summernote.min.js?v=<?php echo time(); ?>"></script> 
    <script src="/static/v4/js/app.min.js?v=<?php echo time(); ?>"></script> 
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script> 
    <link rel="stylesheet" href="/static/v4/vendors/zwicon/zwicon.min.css"> 
    <link rel="stylesheet" href="/static/v4/vendors/animate.css/animate.min.css"> 
    <link rel="stylesheet" href="/static/v4/vendors/overlay-scrollbars/OverlayScrollbars.min.css"> 
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous"> 
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css"> 
    <link rel="stylesheet" href="/static/v4/css/app.min.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="/static/v4/css/shadow.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="/static/v4/css/bootstrap-progressbar-3.3.4.min.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="/static/v4/plugins/form/summernote/summernote.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Righteous&display=swap"> 
    <link rel="stylesheet" href="/static/v4/css/app-dark.css?v=<?php echo time(); ?>"> 
    
    <!-- Estilos optimizados para el dashboard - Esquema Negro -->
    <style>
        body {
            background: #000000 !important;
            font-family: 'Orbitron', sans-serif !important;
            color: #ffffff !important;
        }
        
        /* Header optimizado */
        .header {
            background: rgba(0, 0, 0, 0.95) !important;
            border-bottom: 2px solid #333333;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        .logo a {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 18px;
        }
        
        /* Sidebar rediseñado - Estática */
        .sidebar {
            background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%) !important;
            border-right: 2px solid #00ffc4;
            box-shadow: 2px 0 20px rgba(0, 255, 196, 0.3);
            position: fixed !important;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            z-index: 1000;
            overflow: hidden;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(0, 255, 196, 0.05) 50%, transparent 70%);
            pointer-events: none;
        }
        
        .scrollbar {
            position: relative;
            z-index: 2;
        }
        
        /* Usuario rediseñado */
        .user {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #333;
            border-radius: 15px;
            margin: 15px;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .user::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffc4, transparent);
            animation: scanLine 3s infinite;
        }
        
        .user__img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #00ffc4;
            box-shadow: 0 0 20px rgba(0, 255, 196, 0.5);
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .user__img:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(0, 255, 196, 0.8);
        }
        
        .user__name {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.1rem;
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
        }
        
        /* Navegación rediseñada */
        .navigation__link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            margin: 8px 15px;
            color: #cccccc !important;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid transparent;
        }
        
        .navigation__link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 196, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .navigation__link:hover {
            color: #00ffc4 !important;
            background: rgba(0, 255, 196, 0.1);
            border-color: #00ffc4;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 255, 196, 0.3);
        }
        
        .navigation__link:hover::before {
            left: 100%;
        }
        
        .navigation__link i {
            font-size: 1.2rem;
            margin-right: 12px;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .navigation__link:hover i {
            transform: scale(1.2);
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
        }
        
        /* Separadores rediseñados */
        .sidebar hr {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, #00ffc4, transparent);
            margin: 20px 15px;
            position: relative;
        }
        
        .sidebar hr::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: #00ffc4;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
        }
        
        /* Botones rediseñados */
        .btn-info {
            background: linear-gradient(45deg, #00ffc4, #0099ff) !important;
            border: none !important;
            color: #000 !important;
            font-weight: 700;
            transition: all 0.3s ease;
            border-radius: 25px !important;
            box-shadow: 0 5px 15px rgba(0, 255, 196, 0.3);
        }
        
        .btn-info:hover {
            background: linear-gradient(45deg, #0099ff, #00ffc4) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 255, 196, 0.5);
        }
        
        /* Secciones de navegación */
        .nav-section {
            margin: 10px 0;
        }
        
        /* Footer rediseñado */
        .sidebar-footer {
            text-align: center;
            color: #666;
            font-size: 0.8rem;
            margin: 20px 15px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            border: 1px solid #333;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #00ffc4, transparent);
            animation: scanLine 4s infinite;
        }
        
        .sidebar-footer span:first-child {
            color: #999;
            font-weight: 300;
        }
        
        .sidebar-footer span:last-child {
            color: #00ffc4;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
        }
        
        /* Efectos de scroll personalizados */
        .scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }
        
        .scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #00ffc4, #0099ff);
            border-radius: 3px;
        }
        
        .scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #0099ff, #00ffc4);
        }
        
        /* Efectos adicionales */
        .navigation__link span {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navigation__link:hover span {
            color: #00ffc4;
            text-shadow: 0 0 5px rgba(0, 255, 196, 0.5);
        }
        
        /* Indicador de página activa */
        .navigation__link.active {
            background: rgba(0, 255, 196, 0.15) !important;
            border-color: #00ffc4 !important;
            color: #00ffc4 !important;
        }
        
        .navigation__link.active::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: #00ffc4;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
        }
        
        /* Animaciones de entrada */
        .navigation__link {
            animation: slideInLeft 0.5s ease forwards;
            opacity: 0;
            transform: translateX(-20px);
        }
        
        .navigation__link:nth-child(1) { animation-delay: 0.1s; }
        .navigation__link:nth-child(2) { animation-delay: 0.2s; }
        .navigation__link:nth-child(3) { animation-delay: 0.3s; }
        .navigation__link:nth-child(4) { animation-delay: 0.4s; }
        .navigation__link:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Responsive mejorado */
        @media (max-width: 768px) {
            .sidebar {
                width: 100% !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .navigation__link {
                padding: 12px 15px;
                margin: 5px 10px;
            }
            
            .user {
                margin: 10px;
                padding: 15px;
            }
            
            .user__img {
                width: 50px;
                height: 50px;
            }
        }
        
        /* Contenido principal */
        .main {
            background: transparent !important;
            margin-left: 280px;
        }
        
        .content {
            background: transparent !important;
        }
        
        /* Header rediseñado */
        .header {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%) !important;
            border-bottom: 2px solid #00ffc4;
            box-shadow: 0 2px 20px rgba(0, 255, 196, 0.3);
            position: relative;
            overflow: hidden;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(0, 255, 196, 0.05) 50%, transparent 70%);
            pointer-events: none;
        }
        
        .header .logo {
            position: relative;
            z-index: 2;
        }
        
        .header .logo a {
            color: #ffffff !important;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(45deg, #00ffc4, #0099ff, #ff00ff);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
            text-shadow: 0 0 30px rgba(0, 255, 196, 0.5);
            transition: all 0.3s ease;
        }
        
        .header .logo a:hover {
            transform: scale(1.05);
            text-shadow: 0 0 40px rgba(0, 255, 196, 0.8);
        }
        
        .header .credits-display {
            position: relative;
            z-index: 2;
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #00ffc4;
            border-radius: 25px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 255, 196, 0.3);
            transition: all 0.3s ease;
        }
        
        .header .credits-display:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 255, 196, 0.5);
            border-color: #0099ff;
        }
        
        .header .credits-display .credits-icon {
            color: #00ffc4;
            font-size: 1.2rem;
            margin-right: 8px;
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
        }
        
        .header .credits-display .credits-text {
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            text-shadow: 0 0 5px rgba(0, 255, 196, 0.5);
        }
        
        .header .credits-display .credits-number {
            color: #00ffc4;
            font-size: 1.1rem;
            font-weight: 900;
            margin-left: 5px;
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
            animation: numberPulse 2s ease-in-out infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        @keyframes pulseGlow {
            0%, 100% { 
                box-shadow: 0 5px 15px rgba(0, 255, 196, 0.3);
                border-color: #00ffc4;
            }
            50% { 
                box-shadow: 0 8px 25px rgba(0, 255, 196, 0.6);
                border-color: #0099ff;
            }
        }
        
        @keyframes numberPulse {
            0%, 100% { 
                text-shadow: 0 0 10px rgba(0, 255, 196, 0.8);
                transform: scale(1);
            }
            50% { 
                text-shadow: 0 0 20px rgba(0, 255, 196, 1);
                transform: scale(1.05);
            }
        }
        
        .header .credits-display {
            animation: pulseGlow 4s ease-in-out infinite;
        }
        
        /* Responsive para header */
        @media (max-width: 768px) {
            .header {
                padding: 10px 15px;
                flex-direction: column;
                gap: 10px;
            }
            
            .header .logo a {
                font-size: 1.2rem;
            }
            
            .header .credits-display {
                padding: 8px 15px;
            }
            
            .header .credits-display .credits-text {
                font-size: 0.9rem;
            }
        }
        
        .content__title h1 {
            color: #ffffff !important;
            font-weight: 700;
        }
        
        .content__title small {
            color: #999999 !important;
        }
        
        /* Tarjetas de estadísticas optimizadas */
        .quick-stats__item {
            background: rgba(20, 20, 20, 0.9) !important;
            border: 1px solid #333333;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .quick-stats__item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.7);
            border-color: #555555;
        }
        
        .quick-stats__info h2 {
            color: #ffffff !important;
            font-weight: 700;
        }
        
        .quick-stats__info small {
            color: #999999 !important;
        }
        
        /* Tarjetas de contenido optimizadas */
        .card {
            background: rgba(20, 20, 20, 0.9) !important;
            border: 1px solid #333333;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }
        
        .card-title {
            color: #ffffff !important;
            font-weight: 700;
        }
        
        .card-subtitle {
            color: #999999 !important;
        }
        
        /* Listas optimizadas */
        .listview__heading {
            color: #ffffff !important;
        }
        
        .listview__attrs span {
            color: #999999 !important;
        }
        
        .listview__item {
            background: rgba(30, 30, 30, 0.5) !important;
            border-bottom: 1px solid #333333 !important;
        }
        
        .listview__item:hover {
            background: rgba(40, 40, 40, 0.7) !important;
        }
        
        /* Botones optimizados */
        .btn-info {
            background: #333333 !important;
            border: 1px solid #555555 !important;
            color: #ffffff !important;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        
        .btn-info:hover {
            background: #555555 !important;
            transform: translateY(-1px);
        }
        
        /* Efectos de créditos en header */
        .header span {
            color: #ffffff !important;
            font-weight: 600;
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #000000;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #333333;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555555;
        }
        
        /* Efectos de texto y enlaces */
        a {
            color: #cccccc !important;
        }
        
        a:hover {
            color: #ffffff !important;
        }
        
        /* Efectos de hover para tarjetas */
        .card:hover {
            border-color: #555555;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.7);
        }
        
        /* Optimización de rendimiento */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Efectos de transición suaves */
        .quick-stats__item,
        .card,
        .navigation__link,
        .btn-info {
            transition: all 0.3s ease;
        }
        
        /* Estilos Cyberpunk para el Panel Holográfico */
        .cyberpunk-panel {
            position: relative;
            padding: 20px;
            margin: 15px 0;
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid #00ffff;
            border-radius: 10px;
            box-shadow: 
                0 0 20px rgba(0, 255, 255, 0.5),
                inset 0 0 20px rgba(0, 255, 255, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .holographic-display {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .neon-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #00ff00;
            box-shadow: 
                0 0 10px #00ff00,
                0 0 20px #00ff00,
                0 0 30px #00ff00;
            animation: pulse 2s infinite;
        }
        
        .status-text {
            font-family: 'Orbitron', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #00ffff;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 
                0 0 5px #00ffff,
                0 0 10px #00ffff,
                0 0 15px #00ffff;
            animation: textGlow 3s ease-in-out infinite alternate;
        }
        
        .glitch-effect {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(90deg, transparent 98%, #ff0080 100%),
                linear-gradient(90deg, transparent 70%, #00ffff 100%);
            opacity: 0.1;
            animation: glitch 4s infinite;
        }
        
        .scan-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 255, 0.1) 2px,
                rgba(0, 255, 255, 0.1) 4px
            );
            pointer-events: none;
            animation: scanLines 2s linear infinite;
        }
        
        /* Animaciones Cyberpunk */
        @keyframes pulse {
            0% { 
                transform: scale(1);
                box-shadow: 
                    0 0 10px #00ff00,
                    0 0 20px #00ff00,
                    0 0 30px #00ff00;
            }
            50% { 
                transform: scale(1.1);
                box-shadow: 
                    0 0 15px #00ff00,
                    0 0 30px #00ff00,
                    0 0 45px #00ff00;
            }
            100% { 
                transform: scale(1);
                box-shadow: 
                    0 0 10px #00ff00,
                    0 0 20px #00ff00,
                    0 0 30px #00ff00;
            }
        }
        
        @keyframes textGlow {
            0% { 
                text-shadow: 
                    0 0 5px #00ffff,
                    0 0 10px #00ffff,
                    0 0 15px #00ffff;
            }
            100% { 
                text-shadow: 
                    0 0 10px #00ffff,
                    0 0 20px #00ffff,
                    0 0 30px #00ffff,
                    0 0 40px #00ffff;
            }
        }
        
        @keyframes glitch {
            0% { transform: translateX(0); }
            20% { transform: translateX(-2px) skewX(0.5deg); }
            40% { transform: translateX(2px) skewX(-0.5deg); }
            60% { transform: translateX(-1px) skewX(0.2deg); }
            80% { transform: translateX(1px) skewX(-0.2deg); }
            100% { transform: translateX(0); }
        }
        
        @keyframes scanLines {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        
        /* Efectos hover para interactividad */
        .cyberpunk-panel:hover {
            box-shadow: 
                0 0 30px rgba(0, 255, 255, 0.7),
                inset 0 0 30px rgba(0, 255, 255, 0.2);
            border-color: #ff0080;
        }
        
        .cyberpunk-panel:hover .neon-circle {
            background: #ff0080;
            box-shadow: 
                0 0 15px #ff0080,
                0 0 30px #ff0080,
                0 0 45px #ff0080;
        }
        
        .cyberpunk-panel:hover .status-text {
            color: #ff0080;
            text-shadow: 
                0 0 5px #ff0080,
                0 0 10px #ff0080,
                0 0 15px #ff0080;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .cyberpunk-panel {
                padding: 15px;
                margin: 10px 0;
            }
            
            .status-text {
                font-size: 14px;
                letter-spacing: 1px;
            }
            
            .neon-circle {
                width: 15px;
                height: 15px;
            }
        }
    </style>
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
                <a href="/">[ <b>☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂</b> ]</a> 
            </div> 
            <div class="credits-display"> 
                <i class="zwicon-diamond credits-icon"></i>
                <span class="credits-text">Créditos</span>
                <span class="credits-number"><?php echo $user_info["creditos"]; ?></span>
            </div> 
        </header> 
        <aside class="sidebar"> 
            <div class="scrollbar" style="max-height: 100vh; overflow-y: auto;"> 
                <!-- Sección de Usuario -->
                <div class="user"> 
                    <div class="user__info" data-toggle="dropdown"> 
                        <img class="user__img" src="/static/v4/img/okurachk.jpg" alt="Usuario"> 
                        <div class="user__name"> 
                            <?php echo $user_info["username"]; ?> 
                        </div> 
                    </div> 
                    <div class="dropdown-menu dropdown-menu--invert" style="padding: 10px; text-align: center;"> 
                        <a href="/logout" class="btn btn-info btn-sm"> 
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión 
                        </a> 
                    </div> 
                </div> 

                <!-- Navegación Principal -->
                <div class="nav-section">
                    <a href="/" class="navigation__link"> 
                        <i class="zwicon-home"></i> 
                        <span>Dashboard</span>
                    </a> 

                    <!-- Amazon Gate -->
                    <a href="/gates/amazon.php" class="navigation__link"> 
                        <i class="zwicon-amazon"></i> 
                        <span>Amazon Gate</span>
                    </a>
                </div>

                <!-- Separador -->
                <hr> 

                <!-- Herramientas y Permisos -->
                <div class="nav-section">
                    <?php echo get_permissions(); ?> 
                    <a href="/canjear-key.php" class="navigation__link"> 
                        <i class="zwicon-key"></i> 
                        <span>Canjear Key</span>
                    </a>
                </div>

                <!-- Botón de Cerrar Sesión -->
                <div class="nav-section">
                    <a href="/logout" class="btn btn-info btn-sm" style="display: block; width: 90%; text-align: center; margin: 15px auto;"> 
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión 
                </a> 
                </div>

                <!-- Footer -->
                <hr> 
                <div class="sidebar-footer">
                    <span>&copy; 2021 ─ 2024</span> 
                <br> 
                    <span>[ ☂ 𝙳𝙰𝚁𝙺 𝙲𝚃 ☂ ]</span> 
                </div>
            </div> 
        </aside> 
    </main> 
    
    <script>
    // Mejorar la interactividad de la sidebar estática
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.navigation__link');
        
        // Efecto de hover mejorado para los enlaces
        navLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(8px) scale(1.02)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0) scale(1)';
            });
        });
        
        // Efecto de click en los enlaces
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Remover clase active de todos los enlaces
                navLinks.forEach(l => l.classList.remove('active'));
                // Agregar clase active al enlace clickeado
                this.classList.add('active');
            });
        });
        
        // Efecto de scroll suave
        const scrollbar = document.querySelector('.scrollbar');
        if (scrollbar) {
            scrollbar.addEventListener('scroll', function() {
                const scrollTop = this.scrollTop;
                const scrollHeight = this.scrollHeight;
                const clientHeight = this.clientHeight;
                const scrollPercent = (scrollTop / (scrollHeight - clientHeight)) * 100;
                
                // Efecto de desvanecimiento en el footer
                const footer = document.querySelector('.sidebar-footer');
                if (footer) {
                    const opacity = Math.max(0.3, 1 - (scrollPercent / 100));
                    footer.style.opacity = opacity;
                }
            });
        }
        
        // Efecto de typing en el nombre de usuario
        const userName = document.querySelector('.user__name');
        if (userName) {
            const text = userName.textContent;
            userName.textContent = '';
            let i = 0;
            
            function typeWriter() {
                if (i < text.length) {
                    userName.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 100);
                }
            }
            
            setTimeout(typeWriter, 1000);
        }
        
        // Efectos adicionales para el header
        const creditsDisplay = document.querySelector('.credits-display');
        const logo = document.querySelector('.header .logo a');
        
        if (creditsDisplay) {
            creditsDisplay.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.05)';
                this.style.borderColor = '#ff00ff';
            });
            
            creditsDisplay.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.borderColor = '#00ffc4';
            });
        }
        
        if (logo) {
            logo.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });
            
            logo.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        }
    });
    </script>
</body> 
</html>
