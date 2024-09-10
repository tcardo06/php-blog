<?php
require_once '../UnauthorizedAccessException.php';
require_once '../NormalTerminationException.php';
require_once '../error_handler.php';
require '../db_connection.php';
session_start();

try {
    // Check if the user is an admin
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

    // Fetch all posts
    $posts_stmt = $conn->prepare("SELECT id, title FROM posts ORDER BY created_at DESC");
    $posts_stmt->execute();
    $posts = $posts_stmt->get_result();

    // Handle delete action via AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
        $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $delete_stmt->bind_param('i', $post_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        echo json_encode(['success' => true]);  // Return success as JSON response for AJAX
        exit();
    }

} catch (UnauthorizedAccessException $e) {
    throw $e;
} catch (NormalTerminationException $e) {
    throw $e;
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred.']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Article</title>
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

        .modal {
            z-index: 1050;
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
        <h1 class="text-center" style="color:white;margin-top:90px;">Supprimer un Article</h1>
        <ul class="list-group" id="post-list">
            <?php if ($posts->num_rows > 0): ?>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <li class="list-group-item" id="post-<?php echo $post['id']; ?>">
                        <?php echo htmlspecialchars($post['title']); ?>
                        <button class="btn btn-danger btn-sm pull-right delete-post" data-post-id="<?php echo $post['id']; ?>">Supprimer</button>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center" style="color:white;">Aucun article à supprimer.</p>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Bootstrap Modal for confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la Suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet article ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/assets/jquery/jquery.min.js"></script>
    <script src="../public/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../public/js/freelancer.min.js"></script>

    <script>
        $(document).ready(function() {
            let postIdToDelete = null;
            let postElementToDelete = null;

            // Open the Bootstrap modal when delete is clicked
            $('.delete-post').on('click', function() {
                postIdToDelete = $(this).data('post-id');
                postElementToDelete = $('#post-' + postIdToDelete);
                $('#deleteModal').modal('show');
            });

            // Confirm deletion
            $('#confirmDelete').on('click', function() {
                if (postIdToDelete !== null) {
                    $.ajax({
                        url: 'admin_remove.php',
                        type: 'POST',
                        data: {
                            post_id: postIdToDelete
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.success) {
                                // Remove the post from the list
                                postElementToDelete.remove();
                                $('#deleteModal').modal('hide');
                            } else {
                                alert('Une erreur est survenue lors de la suppression.');
                            }
                        },
                        error: function() {
                            alert('Une erreur est survenue.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
