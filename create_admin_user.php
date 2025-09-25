<?php
/**
 * Script para crear usuario administrador
 * Ejecutar una sola vez después de crear la base de datos
 */

// Configuración de la base de datos
// Detectar si estamos en localhost o hosting
if (php_sapi_name() === 'cli') {
    // Si se ejecuta desde línea de comandos, usar configuración local
    $host = 'localhost';
    $dbname = 'darkct';
    $username = 'root';
    $password = '';
} else {
    // Si se ejecuta desde navegador, detectar entorno
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
        // Configuración para XAMPP local
        $host = 'localhost';
        $dbname = 'darkct';
        $username = 'root';
        $password = '';
    } else {
        // Configuración para hosting externo
        $host = 'db5018661323.hosting-data.io';
        $dbname = 'dbs14784496';
        $username = 'dbu2919208';
        $password = 'Pelucas09.';
    }
}

// Datos del administrador (CAMBIAR ESTOS VALORES)
$admin_username = 'admin';           // Cambiar por tu username
$admin_email = 'admin@darkct.com';   // Cambiar por tu email
$admin_password = 'admin123';        // Cambiar por tu password
$admin_credits = 1000;               // Créditos iniciales
$subscription_days = 365;            // Días de suscripción

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si el usuario ya existe
    $check_query = $pdo->prepare("SELECT id FROM breathe_users WHERE username = ? OR email = ?");
    $check_query->execute([$admin_username, $admin_email]);
    
    if ($check_query->fetch()) {
        echo "❌ Error: El usuario '$admin_username' o email '$admin_email' ya existe.\n";
        exit;
    }
    
    // Hashear la contraseña
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Calcular fecha de expiración
    $expiration_date = date('Y-m-d', strtotime("+$subscription_days days"));
    
    // Insertar usuario administrador
    $insert_query = $pdo->prepare("
        INSERT INTO breathe_users (
            username, 
            email, 
            breathe_password, 
            creditos, 
            suscripcion, 
            fech_reg, 
            active,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $insert_query->execute([
        $admin_username,
        $admin_email,
        $hashed_password,
        $admin_credits,
        3, // Suscripción admin
        $expiration_date,
        1  // Activo
    ]);
    
    if ($result) {
        $user_id = $pdo->lastInsertId();
        
        echo "✅ Usuario administrador creado exitosamente!\n\n";
        echo "📋 Detalles del usuario:\n";
        echo "   ID: $user_id\n";
        echo "   Username: $admin_username\n";
        echo "   Email: $admin_email\n";
        echo "   Password: $admin_password\n";
        echo "   Créditos: $admin_credits\n";
        echo "   Suscripción: Administrador (3)\n";
        echo "   Expira: $expiration_date\n";
        echo "   Estado: Activo\n\n";
        
        echo "🔐 Credenciales de acceso:\n";
        echo "   URL: https://tu-dominio.com/user/sign-in\n";
        echo "   Username/Email: $admin_username\n";
        echo "   Password: $admin_password\n\n";
        
        echo "⚠️  IMPORTANTE: Cambia la contraseña después del primer login!\n";
        echo "⚠️  ELIMINA este archivo después de usarlo por seguridad!\n";
        
    } else {
        echo "❌ Error al crear el usuario administrador.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
