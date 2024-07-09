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

        $title = $_POST['title'];
        $content = $_POST['content'];
        $preview = $_POST['preview'];
        $tags = $_POST['tags'];

        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO posts (title, content, preview, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $title, $content, $preview, $user_id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $post_id = $stmt->insert_id;

        $tag_query = "SELECT id FROM tags WHERE name = ?";
        $tag_stmt = $conn->prepare($tag_query);
        if (!$tag_stmt) {
            throw new Exception($conn->error);
        }

        $insert_tag_stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
        if (!$insert_tag_stmt) {
            throw new Exception($conn->error);
        }

        foreach ($tags as $tag_name) {
            $tag_stmt->bind_param('s', $tag_name);
            $tag_stmt->execute();
            $tag_result = $tag_stmt->get_result();
            if ($tag_result->num_rows === 0) {
                throw new Exception("Tag not found: " . htmlspecialchars($tag_name));
            }
            $tag_id = $tag_result->fetch_assoc()['id'];

            $insert_tag_stmt->bind_param('ii', $post_id, $tag_id);
            if (!$insert_tag_stmt->execute()) {
                throw new Exception($insert_tag_stmt->error);
            }
        }

        $conn->commit();

        header('Location: blog.php');
        return;
    }

} catch (UnauthorizedAccessException $e) {
    header('Location: ../access_denied.php');
    return;
} catch (Exception $e) {
    $conn->rollback();
    error_log('An error occurred: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}
?>
