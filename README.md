# Projet Blog PHP

## Résumé

Ce projet est un blog en PHP utilisant MySQL comme base de données. Ce README vous guidera à travers deux méthodes de déploiement :

1. Installation locale avec XAMPP
2. Déploiement sur Heroku

Choisissez la méthode qui convient le mieux à vos besoins de développement ou de production.

## Table des matières

- [Installation locale avec XAMPP](#installation-locale-avec-xampp)
  - [Prérequis](#prérequis-local)
  - [Étapes d'installation](#étapes-dinstallation-local)
  - [Configuration de la base de données](#configuration-de-la-base-de-données)
  - [Lancement du projet](#lancement-du-projet)
- [Déploiement sur Heroku](#déploiement-sur-heroku)
  - [Prérequis](#prérequis-heroku)
  - [Étapes de déploiement](#étapes-de-déploiement)
  - [Configuration de la base de données ClearDB](#configuration-de-la-base-de-données-cleardb)
  - [Variables d'environnement](#variables-denvironnement)
- [Dépannage](#dépannage)

## Installation locale avec XAMPP

### Prérequis (local)

- XAMPP (avec PHP 7.4 ou supérieur)
- Git (optionnel)

### Étapes d'installation (local)

1. Téléchargez et installez XAMPP depuis le [site officiel](https://www.apachefriends.org/index.html).
2. Clonez ou téléchargez le projet dans le dossier `htdocs` de XAMPP :
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/tcardo06/php-blog.git
   ```
   Ou téléchargez et extrayez le zip du projet dans ce dossier.

### Configuration de la base de données

1. Lancez XAMPP et démarrez les services Apache et MySQL.
2. Ouvrez phpMyAdmin (http://localhost/phpmyadmin).
3. Créez une nouvelle base de données pour le projet.
4. Importez le fichier SQL du projet dans cette base de données.
5. Modifiez le fichier de configuration de la base de données du projet (généralement `config.php` ou similaire) avec les informations de connexion :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nom_de_votre_base');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Lancement du projet

1. Ouvrez votre navigateur et accédez à `http://localhost/nom-du-projet`.
2. Le blog devrait maintenant être fonctionnel en local.

## Déploiement sur Heroku

### Prérequis (Heroku)

- Git
- Heroku CLI
- Un compte Heroku
- PHP 7.4 ou supérieur

### Étapes de déploiement

1. Connectez-vous à Heroku et créez une nouvelle application :
   ```
   heroku login
   heroku create nom-de-votre-application
   ```

2. Assurez-vous que le fichier `composer.json` est présent à la racine de votre projet :
   ```json
   {
       "require": {
           "php": "^7.4",
           "ext-mysqli": "*"
       }
   }
   ```

3. Initialisez un dépôt Git et déployez :
   ```
   git init
   git add .
   git commit -m "Initial commit"
   git push heroku master
   ```

### Configuration de la base de données ClearDB

1. Ajoutez ClearDB à votre application Heroku :
   ```
   heroku addons:create cleardb:ignite
   ```

2. Récupérez l'URL de la base de données :
   ```
   heroku config | grep CLEARDB_DATABASE_URL
   ```

3. Configurez les variables d'environnement :
   ```
   heroku config:set DB_HOST=host
   heroku config:set DB_NAME=dbname
   heroku config:set DB_USER=user
   heroku config:set DB_PASS=password
   ```

### Variables d'environnement

Modifiez vos fichiers PHP pour utiliser les variables d'environnement. Exemple pour `db_connection.php` :

```php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    throw new Exception("Échec de la connexion : " . $conn->connect_error);
}
```

## Dépannage

- Pour les problèmes locaux avec XAMPP, vérifiez les logs d'erreur Apache et PHP dans le panneau de contrôle XAMPP.
- Pour Heroku, consultez les logs avec :
  ```
  heroku logs --tail
  ```

Pour plus d'informations sur le déploiement Heroku, consultez la [documentation officielle](https://devcenter.heroku.com/).
