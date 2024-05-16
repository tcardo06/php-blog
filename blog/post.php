<?php
require '../db_connection.php';

if (isset($_GET['id'])) {
    $postId = intval($_GET['id']);

    $stmt = $conn->prepare("
        SELECT p.title, p.content, p.created_at, u.username, GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tags
        FROM posts p
        INNER JOIN users u ON p.user_id = u.id
        LEFT JOIN post_tags pt ON p.id = pt.post_id
        LEFT JOIN tags t ON pt.tag_id = t.id
        WHERE p.id = ?
        GROUP BY p.id
    ");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        $post = null;
    }
} else {
    header("Location: ./blog.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post['title']) : 'Post not found'; ?></title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/freelancer.css" rel="stylesheet">
    <style>
        body {
            background-color: #4682b4;
            color: #000;
            font-family: 'Lato', sans-serif;
        }
        .card {
            background: #dae6f0;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background: #abdbe3;
            color: #000;
            font-size: 20px;
            padding-left: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px 10px;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .container {
            padding-top: 50px;
        }
        a {
            color: #3498DB;
        }
        .tags {
            font-size: 0.9em;
            color: #606060;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($post): ?>
            <div class="card">
                <div class="card-header">
                    <?php echo htmlspecialchars($post['title']); ?>
                </div>
                <div class="card-body">
                    <p class="text-muted">Posté par <?php echo htmlspecialchars($post['username']); ?> le <?php echo (new DateTime($post['created_at']))->format('d/m/Y H:i'); ?></p>
                    <p class="tags">Tags: <?php echo htmlspecialchars($post['tags']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Post not found.</p>
        <?php endif; ?>
        <div class="text-center">
            <a href="./blog.php" class="btn btn-primary">Retour au blog</a>
        </div>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
