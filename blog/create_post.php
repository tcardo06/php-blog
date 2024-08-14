<?php
session_start();
require_once '../UnauthorizedAccessException.php';
require_once '../NormalTerminationException.php';
require_once '../error_handler.php';
require '../db_connection.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Invité";

try {
    if ($username === "Invité") {
        throw new NormalTerminationException('Redirect', ['url' => '../access_denied.php']);
    }

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

} catch (UnauthorizedAccessException $e) {
    header('Location: ../access_denied.php');
    return;
} catch (NormalTerminationException $e) {
    header('Location: ' . $e->getData()['url']);
    return;
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Nouveau Post</title>
    <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/freelancer.min.css" rel="stylesheet">
    <link href="../public/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
        <h1 class="text-center" style="color:white;margin-top:90px;margin-bottom:50px;">Créer un Nouveau Post</h1>
        <form id="createPostForm" method="POST" action="submit_post.php">
            <div class="form-group">
                <label for="title">Titre du Post</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Titre du Post" required>
            </div>
            <div class="form-group">
                <label for="preview">Aperçu</label>
                <textarea class="form-control" id="preview" name="preview" rows="2" placeholder="Aperçu" maxlength="120" required></textarea>
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="5" placeholder="Contenu" required></textarea>
            </div>
            <div class="form-group">
                <label for="tags">Tags</label>
                <select class="form-control" id="tags" name="tags[]" multiple="multiple"></select>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>
    <script src="../public/assets/jquery/jquery.min.js"></script>
    <script src="../public/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="..public//js/freelancer.min.js"></script>
    <script src="../public/assets/clamp/clamp.min.js"></script>
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
