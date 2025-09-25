<?php 
error_reporting(0);
header('Content-Type: text/html; charset=utf-8');

// Usar la conexión local de PDO
try {
    $connection = new PDO("mysql:host=db5018661323.hosting-data.io;dbname=dbs14784496", "dbu2919208", "Pelucas09.");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $mensaje = '<tr><td><div style="text-align: center; padding: 20px; color: #e74c3c;"><i class="fa fa-exclamation-circle fa-2x"></i><p>Error de conexión a la base de datos</p></div></td></tr>';
    echo $mensaje;
    exit;
}

$bin = $_POST['buscar'] ?? '';

if(empty($bin)) {
    $mensaje = '<tr><td><div style="text-align: center; padding: 20px; color: #999;"><i class="fa fa-search fa-2x"></i><p>Ingresa un BIN de 6 dígitos para buscar</p></div></td></tr>';
} else {
    // Obtener información del BIN desde binlist.net
    $bin_info = null;
    if (strlen($bin) == 6) {
        $url = "https://lookup.binlist.net/".$bin;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code == 200 && $resp) {
            $bin_info = json_decode($resp, true);
        }
    }
    
    // Buscar en la base de datos local
    try {
        $query = $connection->prepare("SELECT * FROM breathe_dump WHERE CC LIKE :bin LIMIT 100");
        $query->bindValue(':bin', '%' . $bin . '%', PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $mensaje = "";
        $found_count = 0;
        
        foreach($results as $row) {
            $cc_full = $row['CC'] ?? '';
            $string = substr($cc_full, 0, 6);
            
            if($string == $bin) {
                $found_count++;
                $cc = str_replace($string, "", $cc_full);
                
                // Información del BIN
                $scheme = $bin_info['scheme'] ?? 'Unknown';
                $type = $bin_info['type'] ?? 'Unknown';
                $brand = $bin_info['brand'] ?? 'Unknown';
                $bank_name = $bin_info['bank']['name'] ?? 'Unknown Bank';
                $country_name = $bin_info['country']['name'] ?? 'Unknown Country';
                
                $mensaje .= '<div class="result-card">';
                $mensaje .= '<div class="card-header">';
                $mensaje .= '<div class="card-badge">☂ DARK CT ☂</div>';
                $mensaje .= '<div class="card-type">CC Storage</div>';
                $mensaje .= '</div>';
                $mensaje .= '<div class="card-number">' . $string . $cc . '</div>';
                $mensaje .= '<div class="card-info">';
                $mensaje .= '<div class="info-item"><div class="info-label">Esquema</div><div class="info-value">' . $scheme . '</div></div>';
                $mensaje .= '<div class="info-item"><div class="info-label">Tipo</div><div class="info-value">' . $type . '</div></div>';
                $mensaje .= '<div class="info-item"><div class="info-label">Marca</div><div class="info-value">' . $brand . '</div></div>';
                $mensaje .= '<div class="info-item"><div class="info-label">Banco</div><div class="info-value">' . $bank_name . '</div></div>';
                $mensaje .= '<div class="info-item"><div class="info-label">País</div><div class="info-value">' . $country_name . '</div></div>';
                $mensaje .= '</div>';
                $mensaje .= '</div>';
            }
        }
        
        if ($found_count == 0) {
            $mensaje = '<div class="empty-state"><div class="empty-icon"><i class="fa fa-exclamation-triangle"></i></div><div class="empty-text">No se encontraron resultados para el BIN: ' . htmlspecialchars($bin) . '</div></div>';
        } else {
            $mensaje = '<div class="stats-container"><div class="stats-text">🎯 Encontradas ' . $found_count . ' tarjetas para el BIN: ' . htmlspecialchars($bin) . '</div></div>' . $mensaje;
        }
        
    } catch (PDOException $e) {
        $mensaje = '<div class="empty-state"><div class="empty-icon"><i class="fa fa-exclamation-circle"></i></div><div class="empty-text">Error al consultar la base de datos</div></div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: #1a237e; /* Azul aurora */
        }
    </style>
</head>
<body>
    <table>
        <?php
            echo $mensaje;
        ?>
    </table>
</body>
</html>
