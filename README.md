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

1.  Clonez le dépôt.
2.  Assurez-vous d'avoir PHP et un serveur web (Apache, Nginx) installés.
3.  Copiez `.env.example` en `.env` et configurez les variables.
4.  Exécutez `php src/database.php` pour initialiser la base de données SQLite.
5.  Lancez le serveur PHP intégré : `php -S localhost:8000 -t public`

## Avertissement

**NE PAS UTILISER EN PRODUCTION.** Cette application est conçue à des fins éducatives uniquement.
