<?php

try {
    $connection = new PDO("mysql:host=db5018661323.hosting-data.io;dbname=dbs14784496", "dbu2919208", "Pelucas09.");
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}