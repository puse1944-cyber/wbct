<?php
/**
 * Configuración específica para IONOS Hosting
 * Plan Compartido - DARK CT
 */

// Configuración de base de datos para IONOS
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos'); // Cambiar por tu base de datos
define('DB_USER', 'tu_usuario'); // Cambiar por tu usuario
define('DB_PASS', 'tu_password'); // Cambiar por tu contraseña

// Configuración de dominio
define('SITE_URL', 'https://tu-dominio.com'); // Cambiar por tu dominio
define('SITE_NAME', '☂ DARK CT ☂');

// Configuración de sesiones
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Configuración de errores para producción
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Configuración de memoria y tiempo
ini_set('memory_limit', '128M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

// Configuración de subida de archivos
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

// Configuración de sesión
ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

// Crear directorio de logs si no existe
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Función para conectar a la base de datos
function getDatabaseConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        die("Error de conexión a la base de datos");
    }
}

// Función para obtener la URL base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host;
}

// Función para redirigir con HTTPS
function redirectToHttps() {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirectURL");
        exit();
    }
}

// Función para limpiar logs antiguos
function cleanOldLogs() {
    $logDir = __DIR__ . '/logs/';
    $files = glob($logDir . '*.log');
    $maxAge = 7 * 24 * 60 * 60; // 7 días
    
    foreach ($files as $file) {
        if (filemtime($file) < (time() - $maxAge)) {
            unlink($file);
        }
    }
}

// Limpiar logs antiguos (ejecutar una vez al día)
if (rand(1, 100) === 1) {
    cleanOldLogs();
}
?>
