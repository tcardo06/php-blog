<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #4682b4;
            color: white;
            font-family: 'Lato', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }
    </style>
    <script>
        let countdown = 5;
        function updateCountdown() {
            if (countdown <= 0) {
                window.location.href = 'index.php';
            } else {
                document.getElementById('countdown').innerText = countdown;
                countdown--;
            }
        }
        setInterval(updateCountdown, 1000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Accès Refusé</h1>
        <p>Vous n'avez pas la permission d'accéder à cette page.</p>
        <p>Redirection vers la page d'accueil dans <span id="countdown">5</span> secondes.</p>
        <a href="index.php" class="btn btn-primary" style="color:#abdbe3;">Aller à la Page d'Accueil</a>
    </div>
</body>
</html>
