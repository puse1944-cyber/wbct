<?php 
error_reporting(0);
$enlace = mysqli_connect("localhost", "u986223642_Nebor_user", "Nebor1.2", "u986223642_Nebor_base");

if (!$enlace) {
    $mensaje = "Error: No se pudo conectar a MySQL.";
} else {
    $bin = $_POST['buscar'];
    if(empty($bin)) {
        $mensaje = '<tr><td><span style="background-color: #5b6b60ff!important;" class="bg-primary text-highlight"><strong>[✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓]</strong></span> <strong style="color: rgb(255, 255, 255);"> NO SE HAN ENCONTRADO RESULTADOS</strong></td><tr>';
    } else {
        $url = "https://lookup.binlist.net/".$bin;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $resp = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($resp, TRUE);
        $query = "SELECT * FROM `breathe_dump` WHERE `CC` LIKE '%{$bin}%' LIMIT 0,100";
        $result = mysqli_query($enlace, $query); 
        $mensaje = "";
        while($row = mysqli_fetch_array($result)) {
            $string= substr($row[1], 0, 6);
            if($string == $bin) {
                $cc = str_replace($string, "", $row[1]);
                $mensaje .= '<tr><td><span style="background-color: #23b850ff!important;" class="bg-primary text-highlight"><strong>[✓ 𝙾𝙾𝙺𝚄𝚁𝙰𝙲𝙷𝙺 ✓]</strong></span> <strong style="color: rgb(255, 255, 255);"> CC Storage</strong> |<strong style="color: rgb(0, 255, 196);">'.$string.'</strong>'.$cc.' ['.$obj['scheme'].' | '.$obj['type'].' | '.$obj['brand'].' | '.$obj['bank']['name'].' | '.$obj['country']['name'].']</td><tr>';
            }
        }
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
