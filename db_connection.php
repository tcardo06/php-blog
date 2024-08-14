<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$host = $url["host"];
$dbname = substr($url["path"], 1);
$user = $url["user"];
$pass = $url["pass"];

$conn = new mysqli($host, $user, $pass, $dbname);

$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}

?>
