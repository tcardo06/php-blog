<?php
require '../db_connection.php';

header('Content-Type: application/json');

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
$limit = 10;

if ($searchTerm) {
    $query = "SELECT name AS id, name AS text FROM tags WHERE name LIKE ? LIMIT ?";
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $searchTerm, $limit);
} else {
    $query = "SELECT name AS id, name AS text FROM tags LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$tags = [];
while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

echo json_encode($tags);
?>
