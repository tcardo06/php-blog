<?php
require_once '../UnauthorizedAccessException.php';
require_once '../error_handler.php';
require '../db_connection.php';
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id === null) {
            throw new UnauthorizedAccessException('Unauthorized access');
        }

        $post_id = $_POST['post_id'];
        $content = $_POST['content'];

        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, is_approved) VALUES (?, ?, ?, FALSE)");
        $stmt->bind_param('iis', $post_id, $user_id, $content);

        if ($stmt->execute()) {
            header("Location: post.php?id=$post_id");
            return;
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
    }

} catch (UnauthorizedAccessException $e) {
    header('Location: ../access_denied.php');
    return;
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}
?>
