<?php
$host = 'localhost';
$dbname = 'blogDB';
$user = 'root';
$pass = '';

// $host = 'eu-cluster-west-01.k8s.cleardb.net';
// $dbname = 'heroku_96c8bf64ca0644a';
// $user = 'bc89c02bdec69e';
// $pass = '2b493cfe';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}
?>
