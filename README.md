# Application PHP Vulnérable - Projet SDLC

Cette application est une démonstration **intentionnellement vulnérable** conçue pour le TP sur le Cycle de Vie du Développement Sécurisé (SDLC).

## Objectif

L'objectif est de fournir une base de code pour les tests de sécurité SAST (Static Application Security Testing) et DAST (Dynamic Application Security Testing) avec des outils comme SonarQube et OWASP ZAP.

## Vulnérabilités Incluses

- **Injections SQL** : Les requêtes ne sont pas paramétrées.
- **Cross-Site Scripting (XSS)** : Les entrées utilisateur ne sont pas échappées.
- **Mots de passe faibles** : Les mots de passe sont stockés en clair.
- **Gestion de session insécurisée** : Les cookies de session sont mal configurés.

## Installation

### Avec MySQL (Recommandé)

1. Clonez le dépôt.
2. Assurez-vous d'avoir PHP, MySQL et un serveur web (Apache, Nginx) installés.
3. Copiez `.env.example` en `.env` et configurez les variables MySQL :

```ini
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=vulnerable_app
DB_USER=root
DB_PASSWORD=votre_mot_de_passe
```

1. Exécutez `php src/database_mysql.php` pour initialiser la base de données MySQL.
2. Lancez le serveur PHP intégré : `php -S localhost:8000 -t public`

### Avec SQLite (Hérité)

1. Clonez le dépôt.
2. Assurez-vous d'avoir PHP et un serveur web (Apache, Nginx) installés.
3. Copiez `.env.example` en `.env` et configurez les variables :

```ini
DB_DRIVER=sqlite
DB_PATH=/chemin/vers/database.sqlite
```

1. Exécutez `php src/database.php` pour initialiser la base de données SQLite.
2. Lancez le serveur PHP intégré : `php -S localhost:8000 -t public`

## Avertissement

**NE PAS UTILISER EN PRODUCTION.** Cette application est conçue à des fins éducatives uniquement.
