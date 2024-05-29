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

if (!$is_admin) {
    // Redirect to access denied page if the user is not an admin
    header('Location: ../access_denied.php');
    exit;
}

// Approve comment
if (isset($_POST['approve_comment_id'])) {
    $comment_id = $_POST['approve_comment_id'];
    $approve_stmt = $conn->prepare("UPDATE comments SET is_approved = TRUE WHERE id = ?");
    $approve_stmt->bind_param('i', $comment_id);
    $approve_stmt->execute();
    $approve_stmt->close();
}

// Fetch all unapproved comments
$comments_stmt = $conn->prepare("SELECT c.id, c.content, c.created_at, u.username, p.title, p.id AS post_id
                                 FROM comments c
                                 JOIN users u ON c.user_id = u.id
                                 JOIN posts p ON c.post_id = p.id
                                 WHERE c.is_approved = FALSE
                                 ORDER BY c.created_at DESC");
$comments_stmt->execute();
$comments = $comments_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approuver les Commentaires</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/freelancer.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #4682b4;
            color: #000;
            font-family: 'Lato', sans-serif;
            padding-top: 70px;
        }

        .comment {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Basculer la navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="../index.php">Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?>!</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="page-scroll">
                        <a href="../index.php">À Propos</a>
                    </li>
                    <li class="page-scroll">
                        <a href="../blog/blog.php">Blog</a>
                    </li>
                    <?php if ($_SESSION['username'] !== "Invité"): ?>
                        <li class="page-scroll">
                            <a href="../user/logout.php">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="page-scroll">
                            <a href="../user/login.php">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1 class="text-center" style="color:white;margin-top:90px;">Approuver les Commentaires</h1>
        <?php if ($comments->num_rows > 0): ?>
            <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> sur <em><a href="../blog/post.php?id=<?php echo $comment['post_id']; ?>" target="_blank"><?php echo htmlspecialchars($comment['title']); ?></a></em> le <?php echo (new DateTime($comment['created_at']))->format('d/m/Y H:i'); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    <form method="POST" action="admin_approve_comments.php">
                        <input type="hidden" name="approve_comment_id" value="<?php echo $comment['id']; ?>">
                        <button type="submit" class="btn btn-success">Approuver</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucun commentaire à approuver.</p>
        <?php endif; ?>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
