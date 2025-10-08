<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'smartbite'; // Make sure this is correct

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

$conn->set_charset('utf8mb4');
?>