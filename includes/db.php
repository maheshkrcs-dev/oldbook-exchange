<?php

$host = "localhost";
$user = "root";
$pass = "root"; 
$db   = "oldbook_exchange";

date_default_timezone_set('Asia/Kolkata');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");