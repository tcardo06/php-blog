<?php
require '../db_connection.php';
session_start();

$post_id = $_GET['id'];

// Set the username to "Invité" if not logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Invité";

// Check if the user is an admin
$is_admin = false;
if ($username !== "Invité") {
    $user_id = $_SESSION['user_id'];
    $admin_check_stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $admin_check_stmt->bind_param('i', $user_id);
    $admin_check_stmt->execute();
    $admin_check_stmt->bind_result($is_admin);
    $admin_check_stmt->fetch();
    $admin_check_stmt->close();
}

// Fetch the post
$stmt = $conn->prepare("SELECT p.title, p.content, p.created_at, p.updated_at, u.username, GROUP_CONCAT(t.name SEPARATOR ', ') AS tags
                        FROM posts p
                        JOIN users u ON p.user_id = u.id
                        LEFT JOIN post_tags pt ON p.id = pt.post_id
                        LEFT JOIN tags t ON pt.tag_id = t.id
                        WHERE p.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

// Fetch the comments
$comment_stmt = $conn->prepare("SELECT c.content, c.created_at, u.username
                                FROM comments c
                                JOIN users u ON c.user_id = u.id
                                WHERE c.post_id = ? AND c.is_approved = TRUE
                                ORDER BY c.created_at DESC");
$comment_stmt->bind_param('i', $post_id);
$comment_stmt->execute();
$comments = $comment_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/freelancer.min.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #4682b4;
            color: #000;
            font-family: 'Lato', sans-serif;
            padding-top: 70px;
            padding-bottom: 20px;
        }

        .post-content {
            background: #dae6f0;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .comments-section {
            background: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .comment {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .form-group label {
            color: black;
        }

        .back-button {
            margin-bottom: 20px;
            display: block;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        .edit-icon, .moderate-icon {
            margin-left: 10px;
            font-size: 20px;
            color: #fff;
            cursor: pointer;
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
                <a class="navbar-brand" href="../index.php">Bonjour, <?php echo htmlspecialchars($username); ?>!</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="page-scroll">
                        <a href="../index.php">À Propos</a>
                    </li>
                    <li class="page-scroll">
                        <a href="blog.php">Blog</a>
                    </li>
                    <?php if ($username !== "Invité"): ?>
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
        <h1 class="text-center" style="color:white;margin-top:90px;margin-bottom:40px;">
            <?php echo htmlspecialchars($post['title']); ?>
            <?php if ($is_admin): ?>
                <a href="edit_post.php?id=<?php echo $post_id; ?>" class="edit-icon">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="../admin/admin_approve_comments.php?post_id=<?php echo $post_id; ?>" class="moderate-icon">
                    <i class="fa fa-comments"></i>
                </a>
            <?php endif; ?>
        </h1>
        <div class="post-content">
            <p>
                <?php
                $is_updated = $post['updated_at'] && $post['updated_at'] !== $post['created_at'];
                $date = new DateTime($is_updated ? $post['updated_at'] : $post['created_at']);
                echo $is_updated ? '<strong>Mise à jour par:</strong> ' : '<strong>Posté par:</strong> ';
                echo htmlspecialchars($post['username']);
                echo ' le ' . $date->format('d/m/Y H:i');
                ?>
            </p>
            <p><strong>Tags:</strong> <?php echo htmlspecialchars($post['tags']); ?></p>
            <hr>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>
        <div class="comments-section">
            <h2>Commentaires</h2>
            <?php if ($comments->num_rows > 0): ?>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> le <?php echo (new DateTime($comment['created_at']))->format('d/m/Y H:i'); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun commentaire pour cet article.</p>
            <?php endif; ?>
        </div>
        <?php if ($username !== "Invité"): ?>
            <form id="commentForm" method="POST" action="submit_comment.php">
                <div class="form-group">
                    <label for="content">Ajouter un commentaire</label>
                    <textarea class="form-control" id="content" name="content" rows="3" placeholder="Votre commentaire" required></textarea>
                </div>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <button type="submit" class="btn btn-primary">Commenter</button>
            </form>
        <?php else: ?>
            <p>Veuillez vous <a href="../user/login.php">connecter</a> pour ajouter un commentaire.</p>
        <?php endif; ?>
        <button onclick="window.location.href='blog.php'" class="btn btn-secondary back-button">Retour à la liste des articles</button>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/freelancer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.full.min.js"></script>
</body>
</html>
