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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approuver les Commentaires</title>
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

        .comment {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .filter-section {
            background: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .filter-label {
            color: black;
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
        <div class="filter-section">
            <div class="row">
                <div class="col-md-4">
                    <label class="filter-label" for="filter-post">Filtrer par article</label>
                    <select class="form-control" id="filter-post"></select>
                </div>
                <div class="col-md-4">
                    <label class="filter-label" for="filter-user">Filtrer par utilisateur</label>
                    <select class="form-control" id="filter-user"></select>
                </div>
                <div class="col-md-4">
                    <label class="filter-label" for="filter-date">Filtrer par date</label>
                    <input type="date" class="form-control" id="filter-date">
                </div>
            </div>
        </div>
        <div id="comments-container">
            <p>Chargement des commentaires...</p>
        </div>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/freelancer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#filter-post').select2({
                placeholder: 'Sélectionner un article',
                ajax: {
                    url: 'fetch_posts.php',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data.map(function(post) {
                                return { id: post.id, text: post.title };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            $('#filter-user').select2({
                placeholder: 'Sélectionner un utilisateur',
                ajax: {
                    url: 'fetch_users.php',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data.map(function(user) {
                                return { id: user.id, text: user.username };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            function fetchComments() {
                var post_id = $('#filter-post').val();
                var user_id = $('#filter-user').val();
                var date = $('#filter-date').val();

                $.ajax({
                    url: 'fetch_comments.php',
                    type: 'GET',
                    data: {
                        post_id: post_id,
                        user_id: user_id,
                        date: date
                    },
                    success: function(data) {
                        var comments = JSON.parse(data);
                        var commentsHtml = '';

                        if (comments.length > 0) {
                            comments.forEach(function(comment) {
                                commentsHtml += '<div class="comment">' +
                                    '<p><strong>' + comment.username + '</strong> sur <em><a href="../blog/post.php?id=' + comment.post_id + '" target="_blank">' + comment.title + '</a></em> le ' + new Date(comment.created_at).toLocaleDateString('fr-FR') + '</p>' +
                                    '<p>' + comment.content.replace(/\n/g, '<br>') + '</p>' +
                                    '<form method="POST" action="admin_approve_comments.php">' +
                                    '<input type="hidden" name="approve_comment_id" value="' + comment.id + '">' +
                                    '<button type="submit" class="btn btn-success">Approuver</button>' +
                                    '</form>' +
                                    '</div>';
                            });
                        } else {
                            commentsHtml = '<p>Aucun commentaire à approuver.</p>';
                        }

                        $('#comments-container').html(commentsHtml);
                    }
                });
            }

            $('#filter-post, #filter-user, #filter-date').on('change', fetchComments);

            // Initial fetch
            fetchComments();
        });
    </script>
</body>
</html>
