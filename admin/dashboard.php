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

} catch (UnauthorizedAccessException $e) {
    throw $e;
} catch (NormalTerminationException $e) {
    throw $e;
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
    <title>Tableau de Bord Admin</title>
    <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/freelancer.min.css" rel="stylesheet">
    <link href="../public/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #4682b4;
            color: #000;
            font-family: 'Lato', sans-serif;
            padding-top: 70px;
        }

        .admin-option {
            margin-bottom: 30px;
            text-align: center;
        }

        .admin-option a {
            display: inline-block;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            color: #000;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }

        .admin-option a:hover {
            transform: scale(1.05);
        }

        .admin-option i {
            margin-right: 10px;
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
        <div class="row">
            <div class="col-lg-12 text-center" style="margin-top:50px;">
                <h1 class="section-heading">Tableau de Bord Admin</h1>
                <hr class="primary">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 admin-option">
                <a href="../admin/admin_approve_comments.php">
                    <i class="fa fa-comments fa-2x"></i>
                    <span>Modérer les Commentaires</span>
                </a>
            </div>
            <div class="col-md-4 admin-option">
                <a href="../admin/admin_edit.php">
                    <i class="fa fa-edit fa-2x"></i>
                    <span>Éditer un Article</span>
                </a>
            </div>
            <div class="col-md-4 admin-option">
                <a href="../blog/create_post.php">
                    <i class="fa fa-plus fa-2x"></i>
                    <span>Créer un Article</span>
                </a>
            </div>
            <div class="col-md-4 admin-option">
              <a href="../admin/admin_remove.php">
                  <i class="fa fa-trash fa-2x"></i>
                  <span>Supprimer un Article</span>
              </a>
          </div>
        </div>
    </div>
    <script src="../public/assets/jquery/jquery.min.js"></script>
    <script src="../public/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../public/js/freelancer.min.js"></script>
</body>
</html>
