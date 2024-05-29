<?php
require '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, is_approved) VALUES (?, ?, ?, FALSE)");
    $stmt->bind_param('iis', $post_id, $user_id, $content);

    if ($stmt->execute()) {
        header("Location: post.php?id=$post_id");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
