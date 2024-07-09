<?php
$host = 'localhost';
$dbname = 'blogDB';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}
?>
