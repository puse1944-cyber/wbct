<?php
// Script para probar la conexión al hosting y verificar la tabla breathe_keys
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Prueba de Conexión al Hosting</h2>";

try {
    // Usar las mismas credenciales que en brain.php
    $connection = new PDO("mysql:host=db5018661323.hosting-data.io;dbname=dbs14784496", "dbu2919208", "Pelucas09.");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conexión exitosa al hosting</p>";
    
    // Verificar si la tabla breathe_keys existe
    $stmt = $connection->query("SHOW TABLES LIKE 'breathe_keys'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<p style='color: green;'>✅ La tabla breathe_keys existe</p>";
        
        // Mostrar estructura de la tabla
        echo "<h3>Estructura de la tabla breathe_keys:</h3>";
        $stmt = $connection->query("DESCRIBE breathe_keys");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar registros
        $stmt = $connection->query("SELECT COUNT(*) as total FROM breathe_keys");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>Total de keys en la tabla:</strong> " . $count['total'] . "</p>";
        
        // Mostrar las últimas 5 keys
        echo "<h3>Últimas 5 keys:</h3>";
        $stmt = $connection->query("SELECT * FROM breathe_keys ORDER BY id DESC LIMIT 5");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Key</th><th>Créditos</th><th>Días</th><th>Usuario</th><th>Activa</th><th>Fecha Reg</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['number_key']) . "</td>";
            echo "<td>" . $row['credits'] . "</td>";
            echo "<td>" . $row['dias'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . ($row['active'] ? 'Sí' : 'No') . "</td>";
            echo "<td>" . $row['fecha_reg'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>❌ La tabla breathe_keys NO existe</p>";
        echo "<p>Necesitas ejecutar el script SQL para crear la tabla.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
}
?>
