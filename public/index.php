<?php
session_start();
require '../db_connection.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Invité";

// Check if the user is an admin
$is_admin = false;
if ($username !== "Invité") {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($is_admin);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Cardoso Thomas - Développeur Professionnel</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/freelancer.min.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <style>
        #contact {
            color: white;
        }

        #contact .star-primary {
            border-color: white;
        }

        #contact .form-group label {
            color: white;
        }

        #contact .form-control {
            color: white;
        }

        #contact ::placeholder {
            color: white;
            opacity: 1; /* Firefox */
        }

        #contact :-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: white;
        }

        #contact ::-ms-input-placeholder { /* Microsoft Edge */
            color: white;
        }

        #contact .btn-lg {
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body id="page-top" class="index">
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Basculer la navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#page-top">Bonjour, <?php echo htmlspecialchars($username); ?>!</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="page-scroll">
                        <a href=index.php>À Propos</a>
                    </li>
                    <li class="page-scroll">
                        <a href="../blog/blog.php">Blog</a>
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
    <header>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <img class="img-responsive" src="../img/profile.png" alt="Votre Photo">
                    <div class="intro-text">
                        <span class="name">Cardoso Thomas</span>
                        <hr class="star-light">
                        <span class="skills">Trouvez la solution avec Thomas Cardoso, le développeur de confiance!</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>À Propos de Moi</h2>
                    <hr class="star-light">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <p style="text-align: center !important;">
                        Ma passion pour l’informatique et les jeux vidéo m’a permis d’intégrer la Coding Factory. J’apprends à coder en plusieurs langages et à travailler avec la méthode Scrum. J’aime travailler en équipe, je suis curieux et organisé.
                        <br>
                        <a href="../pdf/THOMAS_CARDOSO_DEVELOPPEUR_WEB.pdf" download>Téléchargez mon CV ici</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" style="background: #4682B4;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Contactez-Moi</h2>
                    <hr class="star-primary">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <form name="sentMessage" id="contactForm" style="color: white;" novalidate>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Nom</label>
                                <input type="text" class="form-control" placeholder="Nom" id="name" required data-validation-required-message="Veuillez entrer votre nom.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Adresse Email</label>
                                <input type="email" class="form-control" placeholder="Adresse Email" id="email" required data-validation-required-message="Veuillez entrer votre adresse email.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Message</label>
                                <textarea rows="5" class="form-control" placeholder="Message" id="message" required data-validation-required-message="Veuillez entrer un message."></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div id="success"></div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-primary btn-lg">Envoyer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center">
        <div class="footer-below">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="list-inline">
                            <li>
                                <a href="https://fr.linkedin.com/in/thomas-cardoso" class="btn-social btn-outline" target="_blank"><i class="fa fa-fw fa-linkedin"></i></a>
                            </li>
                            <li>
                                <a href="https://github.com/tcardo06" class="btn-social btn-outline" target="_blank"><i class="fa fa-fw fa-github"></i></a>
                            </li>
                            <?php if ($is_admin): ?>
                                <li>
                                    <a href="../admin/dashboard.php" class="btn-social btn-outline"><i class="fa fa-fw fa-cogs"></i></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="assets/jquery/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/freelancer.min.js"></script>
</body>
</html>
