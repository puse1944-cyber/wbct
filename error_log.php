<?php
// Archivo para mostrar los logs de errores (solo para administradores)
session_start();

// Verificar si es administrador (ajusta según tu sistema de permisos)
$isAdmin = isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1; // Ajusta la condición según tu sistema

if (!$isAdmin) {
    header("Location: /user/sign-in");
    exit;
}

$logFile = $_SERVER['DOCUMENT_ROOT'] . '/php_errors.log';
$errors = [];

if (file_exists($logFile)) {
    $errors = file($logFile, FILE_IGNORE_NEW_LINES);
    $errors = array_reverse($errors); // Mostrar los más recientes primero
    $errors = array_slice($errors, 0, 50); // Solo los últimos 50 errores
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log de Errores - Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { border-bottom: 1px solid #dee2e6; padding-bottom: 20px; margin-bottom: 20px; }
        .error-item { background: #f8f9fa; padding: 10px; margin: 5px 0; border-left: 4px solid #dc3545; border-radius: 4px; }
        .error-time { color: #6c757d; font-size: 12px; }
        .error-message { color: #212529; margin-top: 5px; }
        .no-errors { text-align: center; color: #6c757d; padding: 40px; }
        .btn { display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Log de Errores del Sistema</h1>
            <p>Últimos errores registrados en el sistema</p>
            <a href="/" class="btn">← Volver al Dashboard</a>
            <a href="?clear=1" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres limpiar el log?')">🗑️ Limpiar Log</a>
        </div>
        
        <?php if (isset($_GET['clear']) && $_GET['clear'] == 1): ?>
            <?php
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
                echo '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">✅ Log limpiado exitosamente</div>';
            }
            ?>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="no-errors">
                <h3>🎉 ¡No hay errores!</h3>
                <p>El sistema está funcionando correctamente sin errores registrados.</p>
            </div>
        <?php else: ?>
            <div>
                <h3>📊 Errores Recientes (<?php echo count($errors); ?> encontrados)</h3>
                <?php foreach ($errors as $error): ?>
                    <div class="error-item">
                        <div class="error-time"><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
