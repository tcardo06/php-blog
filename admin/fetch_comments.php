<?php
require_once '../UnauthorizedAccessException.php';
require_once '../NormalTerminationException.php';
require_once '../error_handler.php';
require '../db_connection.php';
session_start();

try {
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id === null) {
        throw new UnauthorizedAccessException('Unauthorized access');
    }

    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($is_admin);
    $stmt->fetch();
    $stmt->close();

    if (!$is_admin) {
        throw new UnauthorizedAccessException('Access Denied: User is not an admin.');
    }

    $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;

    $query = "SELECT c.id, c.content, c.created_at, u.username, p.title, p.id AS post_id
              FROM comments c
              JOIN users u ON c.user_id = u.id
              JOIN posts p ON c.post_id = p.id
              WHERE c.is_approved = FALSE";

    $params = [];
    $types = '';

    if ($post_id) {
        $query .= " AND p.id = ?";
        $params[] = $post_id;
        $types .= 'i';
    }

    if ($user_id) {
        $query .= " AND u.id = ?";
        $params[] = $user_id;
        $types .= 'i';
    }

    if ($date) {
        $query .= " AND DATE(c.created_at) = ?";
        $params[] = $date;
        $types .= 's';
    }

    $query .= " ORDER BY c.created_at DESC";

    $comments_stmt = $conn->prepare($query);

    if (!empty($params)) {
        $comments_stmt->bind_param($types, ...$params);
    }

    $comments_stmt->execute();
    $comments = $comments_stmt->get_result();

    $result = [];

    while ($comment = $comments->fetch_assoc()) {
        $result[] = $comment;
    }

    throw new NormalTerminationException('Success', $result);

} catch (UnauthorizedAccessException $e) {
    throw $e;
} catch (NormalTerminationException $e) {
    echo json_encode($e->getData());
    return;
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}
?>
