<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO y Meta Tags -->
    <title><?php echo isset($site_page) ? $site_page . ' - ' : ''; ?>Dark CT - Sistema de Gates Premium</title>
    <meta name="description" content="Sistema avanzado de gates y herramientas de verificación de tarjetas con interfaz moderna y funcionalidades premium.">
    <meta name="keywords" content="gates, verificación, tarjetas, sistema, premium, dark, ct">
    <meta name="author" content="Dark CT Team">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($site_page) ? $site_page . ' - ' : ''; ?>Dark CT">
    <meta property="og:description" content="Sistema avanzado de gates y herramientas de verificación de tarjetas.">
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/static/v4/img/logo-dark-ct.png">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($site_page) ? $site_page . ' - ' : ''; ?>Dark CT">
    <meta property="twitter:description" content="Sistema avanzado de gates y herramientas de verificación de tarjetas.">
    <meta property="twitter:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/static/v4/img/logo-dark-ct.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/static/v4/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/static/v4/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/static/v4/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/static/v4/img/favicon-16x16.png">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="/static/v4/css/main.css" as="style">
    <link rel="preload" href="/static/v4/css/dark-theme.css" as="style">
    <link rel="preload" href="/static/v4/js/theme-optimizer.js" as="script">
    
    <!-- Critical CSS -->
    <style>
        /* Critical CSS inline para above-the-fold */
        :root {
            --primary-dark: #0a0a0a;
            --secondary-dark: #1a1a1a;
            --accent-cyan: #00ffc4;
            --accent-blue: #0099ff;
            --gradient-primary: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%);
            --gradient-accent: linear-gradient(45deg, #00ffc4, #0099ff);
            --gradient-text: linear-gradient(45deg, #00ffc4, #0099ff, #8b5cf6);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.4);
            --border-radius: 12px;
            --border-radius-lg: 20px;
            --transition-fast: 0.2s ease;
            --transition-normal: 0.3s ease;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html { scroll-behavior: smooth; font-size: 16px; }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gradient-primary);
            color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 196, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 153, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .text-gradient {
            background: var(--gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
            background: var(--gradient-accent);
            color: #000;
            box-shadow: 0 4px 15px rgba(0, 255, 196, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 255, 196, 0.6);
        }
        
        .card {
            background: linear-gradient(145deg, rgba(20, 20, 20, 0.95), rgba(10, 10, 10, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-card);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            transition: all var(--transition-normal);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
        }
        
        #global-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: opacity 0.5s ease;
        }
        
        .preloader-content {
            text-align: center;
        }
        
        .preloader-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid var(--accent-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        .preloader-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gradient-accent);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-cyan);
        }
    </style>
    
    <!-- Main CSS Files -->
    <link rel="stylesheet" href="/static/v4/css/main.css">
    <link rel="stylesheet" href="/static/v4/css/dark-theme.css">
    <link rel="stylesheet" href="/static/v4/css/components.css">
    <link rel="stylesheet" href="/static/v4/css/pages.css">
    <link rel="stylesheet" href="/static/v4/css/optimization.css">
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="/static/v4/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/v4/vendors/overlay-scrollbars/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="/static/v4/plugins/form/summernote/summernote.css">
    
    <!-- Custom Styles -->
    <style>
        /* Estilos específicos para la página actual */
        .dashboard-container {
            min-height: 100vh;
            background: var(--gradient-primary);
            padding: 2rem;
            position: relative;
        }
        
        .dashboard-header {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-accent);
        }
        
        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--gradient-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all var(--transition-normal);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transition: var(--transition-normal);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card);
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .action-card {
            background: var(--gradient-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            text-align: center;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transition: var(--transition-normal);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card);
        }
        
        .action-card:hover::before {
            transform: scaleX(1);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #000;
            margin: 0 auto 1rem;
        }
        
        .action-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .action-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .dashboard-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader Global -->
    <div id="global-preloader">
        <div class="preloader-content">
            <div class="preloader-spinner"></div>
            <div class="preloader-text">Cargando Dark CT...</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="dashboard-header animate-fadeInUp">
            <h1 class="dashboard-title">☂ DARK CT Dashboard</h1>
            <p class="dashboard-subtitle">Bienvenido de nuevo, <?php echo isset($user_info["username"]) ? htmlspecialchars($user_info["username"]) : 'Usuario'; ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="stat-number">
                    <?php 
                        if (!empty($user_info["fech_reg"])) {
                            $expiration_date = new DateTime($user_info["fech_reg"]);
                            $current_date = new DateTime();
                            if ($current_date <= $expiration_date) {
                                echo "ACTIVO";
                            } else {
                                echo isset($user_info["creditos"]) ? htmlspecialchars($user_info["creditos"]) : '0';
                            }
                        } else {
                            echo isset($user_info["creditos"]) ? htmlspecialchars($user_info["creditos"]) : '0';
                        }
                    ?>
                </div>
                <div class="stat-label">
                    <?php 
                        if (!empty($user_info["fech_reg"])) {
                            $expiration_date = new DateTime($user_info["fech_reg"]);
                            $current_date = new DateTime();
                            if ($current_date <= $expiration_date) {
                                echo "Suscripción Activa";
                            } else {
                                echo "Mis Créditos";
                            }
                        } else {
                            echo "Mis Créditos";
                        }
                    ?>
                </div>
            </div>

            <div class="stat-card animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="stat-number"><?php echo isset($user_info["fech_reg"]) ? htmlspecialchars($user_info["fech_reg"]) : 'N/A'; ?></div>
                <div class="stat-label">Fecha de Expiración</div>
            </div>

            <div class="stat-card animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="stat-number"><?php echo isset($user_info["suscripcion"]) ? htmlspecialchars($user_info["suscripcion"]) : '1'; ?></div>
                <div class="stat-label">Tipo de Suscripción</div>
            </div>

            <div class="stat-card animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="stat-number"><?php echo isset($user_info["username"]) ? htmlspecialchars($user_info["username"]) : 'Usuario'; ?></div>
                <div class="stat-label">Usuario</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="/gates/amazon" class="action-card animate-fadeInUp" style="animation-delay: 0.5s;">
                <div class="action-icon">🛒</div>
                <div class="action-title">Amazon Gate</div>
                <div class="action-description">Verificación de tarjetas Amazon</div>
            </a>

            <a href="/gates/chase" class="action-card animate-fadeInUp" style="animation-delay: 0.6s;">
                <div class="action-icon">💳</div>
                <div class="action-title">Chase Gate</div>
                <div class="action-description">Verificación de tarjetas Chase</div>
            </a>

            <a href="/gates/paypal" class="action-card animate-fadeInUp" style="animation-delay: 0.7s;">
                <div class="action-icon">💰</div>
                <div class="action-title">PayPal Gate</div>
                <div class="action-description">Verificación de cuentas PayPal</div>
            </a>

            <a href="/admin" class="action-card animate-fadeInUp" style="animation-delay: 0.8s;">
                <div class="action-icon">⚙️</div>
                <div class="action-title">Administración</div>
                <div class="action-description">Panel de administración</div>
            </a>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="/static/v4/vendors/jquery/jquery.min.js"></script>
    <script src="/static/v4/vendors/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/v4/vendors/overlay-scrollbars/jquery.overlayScrollbars.min.js"></script>
    <script src="/static/v4/plugins/form/summernote/summernote.min.js"></script>
    <script src="/static/v4/vendors/peity/peity.min.js"></script>
    <script src="/static/v4/js/app.min.js"></script>
    <script src="/static/v4/js/theme-optimizer.js"></script>

    <!-- Initialize Theme Optimizer -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar preloader cuando la página esté lista
            setTimeout(() => {
                const preloader = document.getElementById('global-preloader');
                if (preloader) {
                    preloader.classList.add('hidden');
                    setTimeout(() => {
                        preloader.remove();
                    }, 500);
                }
            }, 1000);
        });
    </script>
</body>
</html>
