<?php
require '../db_connection.php';

$query = "SELECT id, title FROM posts ORDER BY title";
$result = $conn->query($query);

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode($posts);
?>
