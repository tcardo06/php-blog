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

// Fetch all posts
$posts_stmt = $conn->prepare("SELECT id, title FROM posts ORDER BY created_at DESC");
$posts_stmt->execute();
$posts = $posts_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les Posts</title>
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
        <h1 class="text-center" style="color:white;margin-top:90px;">Modifier les Posts</h1>
        <?php if ($posts->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($post['title']); ?>
                        <a href="../blog/edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm pull-right">Modifier</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-center" style="color:white;">Aucun post trouvé.</p>
        <?php endif; ?>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/freelancer.min.js"></script>
    <script src="../vendor/clamp/clamp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.full.min.js"></script>
</body>
</html>
