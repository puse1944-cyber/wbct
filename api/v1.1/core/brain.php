<?php

try {
    $connection = new PDO("mysql:host=localhost;dbname=u986223642_Nebor_base", "u986223642_Nebor_user", "Nebor1.2");
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}