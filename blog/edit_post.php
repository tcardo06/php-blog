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

if (!isset($_GET['id'])) {
    // Redirect to the admin edit page if no post ID is provided
    header('Location: admin_edit.php');
    exit;
}

$post_id = $_GET['id'];

// Fetch the post details
$stmt = $conn->prepare("SELECT title, content, preview FROM posts WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $preview);
$stmt->fetch();
$stmt->close();

// Fetch the post's tags
$tags_stmt = $conn->prepare("SELECT t.name FROM tags t JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?");
$tags_stmt->bind_param('i', $post_id);
$tags_stmt->execute();
$tags_result = $tags_stmt->get_result();
$tags = [];
while ($row = $tags_result->fetch_assoc()) {
    $tags[] = $row['name'];
}
$tags_stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Post</title>
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

        .form-group label {
            color: white;
        }
    </style>
</head>
<body>
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" classnavbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
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
                        <a href="blog.php">Blog</a>
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
        <h1 class="text-center" style="color:white;margin-top:90px;margin-bottom:50px;">Modifier le Post</h1>
        <form id="editPostForm" method="POST" action="update_post.php">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <div class="form-group">
                <label for="title">Titre du Post</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="form-group">
                <label for="preview">Aperçu</label>
                <textarea class="form-control" id="preview" name="preview" rows="2" required><?php echo htmlspecialchars($preview); ?></textarea>
            </div>
            <div class="form-group">
                <label for="tags">Tags</label>
                <select class="form-control" id="tags" name="tags[]" multiple="multiple">
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?php echo htmlspecialchars($tag); ?>" selected><?php echo htmlspecialchars($tag); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <br>
    <?php include '../footer.php'; ?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/freelancer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.full.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#tags').select2({
            placeholder: 'Sélectionner des tags',
            ajax: {
                url: 'search_tags.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });
    });
    </script>
</body>
</html>
