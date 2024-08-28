<?php
require_once '../UnauthorizedAccessException.php';
require_once '../NormalTerminationException.php';
require_once '../error_handler.php';
require '../db_connection.php';

session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Invité";

try {
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    $query = "
        SELECT p.id, p.title, p.preview, p.created_at, p.updated_at, u.username, GROUP_CONCAT(t.name SEPARATOR ', ') AS tags
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN post_tags pt ON p.id = pt.post_id
        LEFT JOIN tags t ON pt.tag_id = t.id
        WHERE ? = '' OR p.title LIKE ? OR u.username LIKE ? OR t.name LIKE ?
        GROUP BY p.id, p.title, p.preview, p.created_at, p.updated_at, u.username
        ORDER BY p.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $searchTerm = '%' . $search . '%';

    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    if (!$stmt->bind_param('ssss', $search, $searchTerm, $searchTerm, $searchTerm)) {
        throw new Exception("Error binding parameters: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    throw new NormalTerminationException('Success', $result);

} catch (NormalTerminationException $e) {
    $result = $e->getData();
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
    <title>Blog</title>
    <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/freelancer.min.css" rel="stylesheet">
    <link href="../public/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>
<style>
  body {
      background-color: #4682b4;
      color: #000;
      font-family: 'Lato', sans-serif;
      padding-top: 70px;
    }
  .row {
    display: flex;
    flex-wrap: wrap;
    }
  .card {
    background: #dae6f0;
    color: black;
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    flex: 1 0 30%;
    display: flex;
    flex-direction: column;
    min-height: 350px;
    position: relative;
    }
  .card:hover {
    transform: scale(1.03);
    transition: transform 0.3s ease-in-out;
    cursor: pointer;
    }
  .card-header {
      background: #abdbe3;
      color: #000;
      font-size: 20px;
      padding-left: 10px;
      padding: 5px 10px;
      font-weight: bold;
    }
  .card-body {
      padding: 20px;
      line-height: 1.6;
      flex-grow: 1;
    }
    .card-body p {
      flex-grow: 1;
    }
  .tags {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 90%;
      display: block;
    }
  a {
      color: #3498DB;
    }
  .card a, .card a.card-link {
      color: #000;
      text-decoration: none;
    }
  .card a:hover, .card a:focus, .card a.card-link:hover, .card a.card-link:focus {
      color: #000;
      text-decoration: none;
    }
  .card a.btn-primary, .card a.btn-primary:hover, .card a.btn-primary:focus {
      position: absolute;
      bottom: 15px;
      right: 15px;
      color: #fff;
    }
  .select2-container {
      width: 100% !important;
    }
</style>
<body id="page-top" class="index">
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Basculer la navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="blog.php">Bonjour, <?php echo htmlspecialchars($username); ?>!</a>
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
        <h1 class="text-center" style="color:white;margin-top:90px;margin-bottom:30px;">Blog Posts</h1>
        <div class="row mb-4" style="margin-bottom:10px;">
            <div class="col-md-4">
                <input type="text" id="search-title" style="margin-top:-2px;margin-bottom:5px;" class="form-control" placeholder="Recherche par titre">
            </div>
            <div class="col-md-4">
                <select id="search-author" class="form-control"></select>
            </div>
            <div class="col-md-4">
                <select id="search-tag" class="form-control"></select>
            </div>
        </div>
        <div class="row" id="posts-container">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($post = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="card-header-link" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </div>
                        <div class="card-body">
                          <p class="text-muted">
                              <?php
                              $is_updated = $post['updated_at'] && $post['updated_at'] !== $post['created_at'];
                              $date = new DateTime($is_updated ? $post['updated_at'] : $post['created_at']);
                              echo $is_updated ? 'Mise à jour par ' : 'Posté par ';
                              echo htmlspecialchars($post['username']);
                              echo ' le ' . $date->format('d/m/Y H:i');
                              ?>
                          </p>
                            <p class="tags" style="font-size: 1.0em; color: #777;">
                                <?php
                                $tags = htmlspecialchars($post['tags']);
                                if (strlen($tags) > 50) { // Adjust the length limit as needed
                                    echo substr($tags, 0, 47) . '...';
                                } else {
                                    echo $tags;
                                }
                                ?>
                            </p>
                            <p>
                                <?php
                                $contentPreview = htmlspecialchars($post['preview']);
                                if (strlen($contentPreview) > 120) {
                                    echo substr($contentPreview, 0, 90) . '...';
                                } else {
                                    echo $contentPreview;
                                }
                                ?>
                            </p>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">En savoir plus</a>
                        </div>
                    </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="../public/assets/jquery/jquery.min.js"></script>
    <script src="../public/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../public/js/freelancer.min.js"></script>
    <script src="../public/assets/clamp/clamp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.full.min.js"></script>
    <script>
      window.addEventListener('load', function() {
          var elements = document.querySelectorAll('.card-header');
          Array.from(elements).forEach(function(element) {
              $clamp(element, {clamp: 2});
          });
      });

      document.addEventListener("DOMContentLoaded", function() {
      var cards = document.querySelectorAll('.card');
      cards.forEach(function(card) {
          card.addEventListener('click', function(event) {
              if (!event.target.closest('a')) {
                  window.location.href = card.querySelector('a.card-header-link').href;
              }
          });
      });
    });

    $(document).ready(function() {
        // Initialize Select2 for authors
        $('#search-author').select2({
            placeholder: 'Recherche par auteur',
            ajax: {
                url: 'search_authors.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 0 // Show results on click without typing
        });

        // Initialize Select2 for tags
        $('#search-tag').select2({
            placeholder: 'Recherche par tags',
            ajax: {
                url: 'search_tags.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 0 // Show results on click without typing
        });

        function fetchPosts() {
            var title = $('#search-title').val();
            var author = $('#search-author').val();
            var tag = $('#search-tag').val();

            $.ajax({
                url: 'fetch_posts.php',
                type: 'GET',
                data: {
                    title: title,
                    author: author,
                    tag: tag
                },
                success: function(data) {
                    $('#posts-container').html(data);
                    applyClamp(); // Reapply clamp after content is loaded
                }
            });
        }

        function applyClamp() {
            var elements = document.querySelectorAll('.card-header');
            Array.from(elements).forEach(function(element) {
                $clamp(element, {clamp: 2});
            });
        }

        // Apply clamp on initial load
        applyClamp();

        $('#search-title').on('input', fetchPosts);
        $('#search-author').on('change', fetchPosts);
        $('#search-tag').on('change', fetchPosts);
    });
    </script>

</body>
</html>
