<?php
require '../db_connection.php';

$query = "SELECT id, username FROM users ORDER BY username";
$result = $conn->query($query);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
