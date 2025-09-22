<?php
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";

// Obtener todas las keys activas
$query = $connection->query("SELECT * FROM breathe_keys WHERE active = 1");
$keys = $query->fetchAll(PDO::FETCH_ASSOC);

$today = new DateTime();
$expired_keys = [];

foreach ($keys as $key) {
    // Calcular la fecha de expiración
    $start_date = new DateTime($key['fecha_inicio']);
    $expiration_date = $start_date->modify('+' . $key['dias'] . ' days');
    
    // Si la fecha actual es mayor que la fecha de expiración
    if ($today > $expiration_date) {
        // Marcar la key como inactiva
        $update = $connection->prepare("UPDATE breathe_keys SET active = 0 WHERE id = :id");
        $update->bindParam("id", $key['id'], PDO::PARAM_INT);
        $update->execute();
        
        // Si la key está asociada a un usuario, actualizar su estado
        if (!empty($key['username'])) {
            $update_user = $connection->prepare("UPDATE breathe_users SET active = 0, sus_status = 0 WHERE key_breathe = :key");
            $update_user->bindParam("key", $key['number_key'], PDO::PARAM_STR);
            $update_user->execute();
        }
        
        $expired_keys[] = $key['number_key'];
    }
}

// Registrar las keys expiradas en un archivo de log
if (!empty($expired_keys)) {
    $log_message = date('Y-m-d H:i:s') . " - Keys expiradas: " . implode(', ', $expired_keys) . "\n";
    file_put_contents($path . '/subscription_log.txt', $log_message, FILE_APPEND);
}
?> 