<?php
require '../db_connection.php';
session_start();

// Check if the user is an admin
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

class AccessDeniedException extends Exception {
    public function errorMessage() {
        return "Error: " . $this->getMessage();
    }
}

try {
    if (!$is_admin) {
        // Throw an exception if the user is not an admin
        throw new AccessDeniedException('Access Denied: User is not an admin.');
    }
} catch (AccessDeniedException $e) {
    error_log($e->errorMessage());

    // Redirect to access denied page
    header('Location: ../access_denied.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $preview = $_POST['preview'];
    $tags = $_POST['tags'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, preview = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('sssi', $title, $content, $preview, $post_id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $delete_tags_stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $delete_tags_stmt->bind_param('i', $post_id);

        if (!$delete_tags_stmt->execute()) {
            throw new Exception($delete_tags_stmt->error);
        }

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
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
