<?php
require_once '../UnauthorizedAccessException.php';
require_once '../error_handler.php';
require '../db_connection.php';
session_start();

try {
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id === null) {
        throw new UnauthorizedAccessException('Unauthorized access');
    }

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

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Error retrieving results: " . $conn->error);
    }

    $tags = [];
    while ($row = $result->fetch_assoc()) {
        $tags[] = $row;
    }

    echo json_encode($tags);

} catch (UnauthorizedAccessException $e) {
    header('Location: ../access_denied.php');
    return;
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo json_encode(['error' => 'An error occurred. Please try again later.']);
}
?>
