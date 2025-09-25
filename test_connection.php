<?php
/**
 * Script de diagnóstico de conexión a base de datos
 * Verifica si la conexión funciona correctamente
 */

echo "🔍 DIAGNÓSTICO DE CONEXIÓN A BASE DE DATOS\n";
echo "==========================================\n\n";

// Detectar entorno
$is_localhost = (strpos($_SERVER['HTTP_HOST'] ?? 'localhost', 'localhost') !== false || 
                 strpos($_SERVER['HTTP_HOST'] ?? 'localhost', '127.0.0.1') !== false);

echo "🌐 Entorno detectado: " . ($is_localhost ? "LOCALHOST" : "HOSTING EXTERNO") . "\n";
echo "📍 Host: " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n\n";

// Configuraciones a probar
$configs = [];

if ($is_localhost) {
    $configs[] = [
        'name' => 'XAMPP Local',
        'host' => 'localhost',
        'dbname' => 'darkct',
        'username' => 'root',
        'password' => ''
    ];
} else {
    $configs[] = [
        'name' => 'Hosting Externo',
        'host' => 'db5018661323.hosting-data.io',
        'dbname' => 'dbs14784496',
        'username' => 'dbu2919208',
        'password' => 'Pelucas09.'
    ];
}

// Probar cada configuración
foreach ($configs as $config) {
    echo "🔧 Probando: {$config['name']}\n";
    echo "   Host: {$config['host']}\n";
    echo "   DB: {$config['dbname']}\n";
    echo "   User: {$config['username']}\n";
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        echo "   ✅ CONEXIÓN EXITOSA!\n";
        
        // Verificar si las tablas existen
        $tables_query = $pdo->query("SHOW TABLES");
        $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
        
        echo "   📊 Tablas encontradas: " . count($tables) . "\n";
        
        if (in_array('breathe_users', $tables)) {
            echo "   ✅ Tabla 'breathe_users' existe\n";
            
            // Verificar usuarios existentes
            $users_query = $pdo->query("SELECT COUNT(*) as total FROM breathe_users");
            $user_count = $users_query->fetch()['total'];
            echo "   👥 Usuarios existentes: $user_count\n";
            
            // Verificar si ya existe un admin
            $admin_query = $pdo->query("SELECT COUNT(*) as total FROM breathe_users WHERE suscripcion = 3");
            $admin_count = $admin_query->fetch()['total'];
            echo "   👑 Administradores: $admin_count\n";
            
        } else {
            echo "   ❌ Tabla 'breathe_users' NO existe\n";
            echo "   💡 Necesitas ejecutar database_hosting_compatible.sql primero\n";
        }
        
    } catch (PDOException $e) {
        echo "   ❌ ERROR: " . $e->getMessage() . "\n";
        
        // Diagnóstico específico del error
        if (strpos($e->getMessage(), 'No such file or directory') !== false) {
            echo "   💡 MySQL no está ejecutándose o el socket no se encuentra\n";
        } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "   💡 Credenciales incorrectas\n";
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "   💡 La base de datos no existe\n";
        } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
            echo "   💡 No se puede conectar al servidor MySQL\n";
        }
    }
    
    echo "\n";
}

echo "📋 RECOMENDACIONES:\n";
echo "==================\n";

if ($is_localhost) {
    echo "1. Asegúrate de que XAMPP esté ejecutándose\n";
    echo "2. Verifica que MySQL esté activo en XAMPP\n";
    echo "3. Crea la base de datos 'darkct' en phpMyAdmin\n";
    echo "4. Ejecuta database_hosting_compatible.sql\n";
    echo "5. Luego ejecuta setup_admin.php\n";
} else {
    echo "1. Verifica que la base de datos esté creada en el hosting\n";
    echo "2. Ejecuta database_hosting_compatible.sql en el hosting\n";
    echo "3. Luego ejecuta setup_admin.php\n";
}

echo "\n🔗 Archivos necesarios:\n";
echo "- database_hosting_compatible.sql (crear tablas)\n";
echo "- setup_admin.php (crear usuario admin)\n";
echo "- test_connection.php (este archivo de diagnóstico)\n";
?>
