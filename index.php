<?php
// Configuración de manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Función para manejar errores fatales
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Sistema</title>
            <style>
                body { font-family: Arial, sans-serif; background: #000; margin: 0; padding: 20px; color: #fff; }
                .error-container { max-width: 600px; margin: 50px auto; background: #111; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(255,255,255,0.1); border: 1px solid #333; }
                .error-title { color: #ff4444; font-size: 24px; margin-bottom: 20px; }
                .error-message { color: #ccc; line-height: 1.6; }
                .btn { display: inline-block; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; border: 1px solid #555; }
                .btn:hover { background: #555; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1 class="error-title">⚠️ Error del Sistema</h1>
                <div class="error-message">
                    <p>Ha ocurrido un error interno. Por favor, inténtalo de nuevo más tarde.</p>
                    <p>Si el problema persiste, contacta al administrador del sistema.</p>
                </div>
                <a href="/user/sign-in" class="btn">Volver al Inicio</a>
            </div>
        </body>
        </html>';
        exit;
    }
}

// Registrar el manejador de errores fatales
register_shutdown_function('handleFatalError');

try {
    $site_page = "Main";
    $path = $_SERVER["DOCUMENT_ROOT"];
    
    // Verificar que el archivo header moderno existe
    $headerFile = $path . "/static/v4/plugins/form/header-modern.php";
    if (!file_exists($headerFile)) {
        // Fallback al header original si no existe el moderno
        $headerFile = $path . "/static/v4/plugins/form/header.php";
        if (!file_exists($headerFile)) {
            throw new Exception("Archivo header no encontrado: " . $headerFile);
        }
    }
    
    // Incluir el header con manejo de errores
    include($headerFile);
    
} catch (Exception $e) {
    // Log del error
    error_log("Error en index.php: " . $e->getMessage());
    
    // Mostrar página de error amigable
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Sistema</title>
        <style>
            body { font-family: Arial, sans-serif; background: #000; margin: 0; padding: 20px; color: #fff; }
            .error-container { max-width: 600px; margin: 50px auto; background: #111; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(255,255,255,0.1); border: 1px solid #333; }
            .error-title { color: #ff4444; font-size: 24px; margin-bottom: 20px; }
            .error-message { color: #ccc; line-height: 1.6; }
            .btn { display: inline-block; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; border: 1px solid #555; }
            .btn:hover { background: #555; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">⚠️ Error del Sistema</h1>
            <div class="error-message">
                <p>Ha ocurrido un error al cargar la página. Por favor, inténtalo de nuevo.</p>
                <p>Si el problema persiste, contacta al administrador del sistema.</p>
            </div>
            <a href="/user/sign-in" class="btn">Volver al Inicio</a>
        </div>
    </body>
    </html>';
    exit;
}
?>
        <section class="content">
            <header class="content__title">
                <h1>☂ DARK CT Dashboard <small>Bienvenido de nuevo, <?php echo isset($user_info["username"]) ? htmlspecialchars($user_info["username"]) : 'Usuario'; ?>!</small></h1>
            </header>
            <div class="row quick-stats">
                <div class="col-sm-6 col-md-3">
                    <div class="quick-stats__item">
                        <div class="quick-stats__info">
                            <h2><?php 
                                // Mostrar información híbrida
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
                            ?></h2>
                            <small><?php 
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
                            ?></small>
                        </div>
                        <div class="quick-stats__chart peity-bar" style="display: none;">6,4,8,6,5,6,7,8,3,5,9</div>
                        <svg class="peity" height="36" width="65">
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="0.8981818181818183" y="12" width="2.694545454545455" height="24"></rect>
                            <rect data-value="4" fill="rgba(255,255,255,0.85)" x="5.389090909090909" y="20" width="2.6945454545454552" height="16"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="9.88" y="4" width="2.6945454545454535" height="32"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="14.370909090909093" y="12" width="2.6945454545454535" height="24"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="18.86181818181818" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="23.35272727272727" y="12" width="2.6945454545454552" height="24"></rect>
                            <rect data-value="7" fill="rgba(255,255,255,0.85)" x="27.84363636363636" y="8" width="2.6945454545454552" height="28"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="32.334545454545456" y="4" width="2.6945454545454552" height="32"></rect>
                            <rect data-value="3" fill="rgba(255,255,255,0.85)" x="36.82545454545454" y="24" width="2.6945454545454623" height="12"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="41.31636363636363" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="9" fill="rgba(255,255,255,0.85)" x="45.807272727272725" y="0" width="2.6945454545454552" height="36"></rect>
                        </svg>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="quick-stats__item">
                        <div class="quick-stats__info">
                            <h2><?php echo isset($user_info["fech_reg"]) ? htmlspecialchars($user_info["fech_reg"]) : 'N/A'; ?></h2>
                            <small>Fecha de Expiración</small>
                        </div>
                        <div class="quick-stats__chart peity-bar" style="display: none;">6,4,8,6,5,6,7,8,3,5,9</div>
                        <svg class="peity" height="36" width="65">
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="0.8981818181818183" y="12" width="2.694545454545455" height="24"></rect>
                            <rect data-value="4" fill="rgba(255,255,255,0.85)" x="5.389090909090909" y="20" width="2.6945454545454552" height="16"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="9.88" y="4" width="2.6945454545454535" height="32"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="14.370909090909093" y="12" width="2.6945454545454535" height="24"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="18.86181818181818" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="23.35272727272727" y="12" width="2.6945454545454552" height="24"></rect>
                            <rect data-value="7" fill="rgba(255,255,255,0.85)" x="27.84363636363636" y="8" width="2.6945454545454552" height="28"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="32.334545454545456" y="4" width="2.6945454545454552" height="32"></rect>
                            <rect data-value="3" fill="rgba(255,255,255,0.85)" x="36.82545454545454" y="24" width="2.6945454545454623" height="12"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="41.31636363636363" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="9" fill="rgba(255,255,255,0.85)" x="45.807272727272725" y="0" width="2.6945454545454552" height="36"></rect>
                        </svg>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="quick-stats__item">
                        <div class="quick-stats__info">
                            <h2><?php 
                                try {
                                    echo function_exists('get_lives') ? get_lives() : '0';
                                } catch (Exception $e) {
                                    echo '0';
                                    error_log("Error en get_lives(): " . $e->getMessage());
                                }
                            ?></h2>
                            <small>Lives Totales</small>
                        </div>
                        <div class="quick-stats__chart peity-bar" style="display: none;">6,4,8,6,5,6,7,8,3,5,9</div>
                        <svg class="peity" height="36" width="65">
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="0.8981818181818183" y="12" width="2.694545454545455" height="24"></rect>
                            <rect data-value="4" fill="rgba(255,255,255,0.85)" x="5.389090909090909" y="20" width="2.6945454545454552" height="16"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="9.88" y="4" width="2.6945454545454535" height="32"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="14.370909090909093" y="12" width="2.6945454545454535" height="24"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="18.86181818181818" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="23.35272727272727" y="12" width="2.6945454545454552" height="24"></rect>
                            <rect data-value="7" fill="rgba(255,255,255,0.85)" x="27.84363636363636" y="8" width="2.6945454545454552" height="28"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="32.334545454545456" y="4" width="2.6945454545454552" height="32"></rect>
                            <rect data-value="3" fill="rgba(255,255,255,0.85)" x="36.82545454545454" y="24" width="2.6945454545454623" height="12"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="41.31636363636363" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="9" fill="rgba(255,255,255,0.85)" x="45.807272727272725" y="0" width="2.6945454545454552" height="36"></rect>
                        </svg>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="quick-stats__item">
                        <div class="quick-stats__info">
                            <h2><?php 
                                try {
                                    echo function_exists('get_users') ? get_users(true) : '0';
                                } catch (Exception $e) {
                                    echo '0';
                                    error_log("Error en get_users(): " . $e->getMessage());
                                }
                            ?></h2>
                            <small>Usuarios Activos</small>
                        </div>
                        <div class="quick-stats__chart peity-bar" style="display: none;">6,4,8,6,5,6,7,8,3,5,9</div>
                        <svg class="peity" height="36" width="65">
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="0.8981818181818183" y="12" width="2.694545454545455" height="24"></rect>
                            <rect data-value="4" fill="rgba(255,255,255,0.85)" x="5.389090909090909" y="20" width="2.6945454545454552" height="16"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="9.88" y="4" width="2.6945454545454535" height="32"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="14.370909090909093" y="12" width="2.6945454545454535" height="24"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="18.86181818181818" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="6" fill="rgba(255,255,255,0.85)" x="23.35272727272727" y="12" width="2.6945454545454552" height="24"></rect>
                            <rect data-value="7" fill="rgba(255,255,255,0.85)" x="27.84363636363636" y="8" width="2.6945454545454552" height="28"></rect>
                            <rect data-value="8" fill="rgba(255,255,255,0.85)" x="32.334545454545456" y="4" width="2.6945454545454552" height="32"></rect>
                            <rect data-value="3" fill="rgba(255,255,255,0.85)" x="36.82545454545454" y="24" width="2.6945454545454623" height="12"></rect>
                            <rect data-value="5" fill="rgba(255,255,255,0.85)" x="41.31636363636363" y="16" width="2.6945454545454552" height="20"></rect>
                            <rect data-value="9" fill="rgba(255,255,255,0.85)" x="45.807272727272725" y="0" width="2.6945454545454552" height="36"></rect>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card widget-visitors">
                        <div class="card-body">
                            <h4 class="card-title">Updates / News</h4>
                            <h6 class="card-subtitle">Actualizaciones, anuncios, comunicados...</h6>
                        </div>
                        <?php 
                            try {
                                if (function_exists('get_news')) {
                                    echo get_news();
                                } else {
                                    echo '<div class="listview listview--bordered"><div class="listview__item"><div class="listview__content"><div class="listview__heading">No hay noticias disponibles</div></div></div></div>';
                                }
                            } catch (Exception $e) {
                                echo '<div class="listview listview--bordered"><div class="listview__item"><div class="listview__content"><div class="listview__heading">Error al cargar noticias</div></div></div></div>';
                                error_log("Error en get_news(): " . $e->getMessage());
                            }
                        ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card widget-past-days">
                        <div class="card-body">
                            <h4 class="card-title">Últimos Miembros</h4>
                        </div>
                        <?php 
                            try {
                                if (function_exists('get_users')) {
                                    echo get_users();
                                } else {
                                    echo '<div class="listview listview--striped"><div class="checkbox-char todo__item"><label>N</label><div class="listview__content"><span class="listview__heading">No hay usuarios disponibles</span></div></div></div>';
                                }
                            } catch (Exception $e) {
                                echo '<div class="listview listview--striped"><div class="checkbox-char todo__item"><label>E</label><div class="listview__content"><span class="listview__heading">Error al cargar usuarios</span></div></div></div>';
                                error_log("Error en get_users(): " . $e->getMessage());
                            }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>