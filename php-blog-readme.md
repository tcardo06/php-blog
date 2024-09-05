# Projet Blog PHP

Ce projet est un blog en PHP utilisant MySQL comme base de données. Ce guide vous expliquera comment installer et déployer ce projet sur Heroku.

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- Git
- Heroku CLI
- PHP (pour tester en local)
- Un compte Heroku
- Une base de données MySQL (ClearDB pour Heroku ou une autre base MySQL)

## Installation et déploiement

### 1. Cloner le projet

```bash
git clone https://github.com/nom-utilisateur/nom-du-projet.git
cd nom-du-projet
```

### 2. Créer une application Heroku

Connectez-vous à Heroku et créez une nouvelle application :

```bash
heroku login
heroku create nom-de-votre-application
```

### 3. Configurer la base de données ClearDB sur Heroku

Ajoutez ClearDB à votre application Heroku :

```bash
heroku addons:create cleardb:ignite
```

Récupérez l'URL de la base de données :

```bash
heroku config | grep CLEARDB_DATABASE_URL
```

### 4. Configurer les variables d'environnement

Définissez les variables d'environnement pour la connexion à la base de données :

```bash
heroku config:set DB_HOST=host
heroku config:set DB_NAME=dbname
heroku config:set DB_USER=user
heroku config:set DB_PASS=password
```

### 5. Déployer le projet sur Heroku

Assurez-vous que le fichier `composer.json` est présent à la racine de votre projet :

```json
{
    "require": {
        "php": "^7.4",
        "ext-mysqli": "*"
    }
}
```

Initialisez un dépôt Git et déployez :

```bash
git init
git add .
git commit -m "Initial commit"
git push heroku master
```

### 6. Migrer la base de données

Exécutez vos fichiers SQL sur la base ClearDB pour créer les tables nécessaires.

### 7. Configurer dynamiquement les fichiers PHP

Modifiez vos fichiers PHP pour utiliser les variables d'environnement. Exemple pour `db_connection.php` :

```php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}
```

### 8. Configuration finale et tests

Ouvrez votre site :

```bash
heroku open
```

## Dépannage

Pour consulter les journaux d'erreurs :

```bash
heroku logs --tail
```

## Variables d'environnement supplémentaires

Pour ajouter des configurations supplémentaires :

```bash
heroku config:set NOM_VARIABLE=valeur
```

## Résumé des étapes

1. Clonez le projet
2. Créez une application Heroku
3. Configurez ClearDB pour la base de données MySQL
4. Configurez les variables d'environnement
5. Déployez l'application sur Heroku
6. Effectuez les migrations SQL
7. Testez votre application

Pour plus d'informations, consultez la [documentation Heroku](https://devcenter.heroku.com/).
