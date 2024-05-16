<?php
require '../db_connection.php';

header('Content-Type: application/json');

// Get the search term from the AJAX request
$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
$limit = 10; // Set a limit for default results

if ($searchTerm) {
    $query = "SELECT username AS id, username AS text FROM users WHERE username LIKE ? LIMIT ?";
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $searchTerm, $limit);
} else {
    $query = "SELECT username AS id, username AS text FROM users LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$authors = [];
while ($row = $result->fetch_assoc()) {
    $authors[] = $row;
}

echo json_encode($authors);
?>
