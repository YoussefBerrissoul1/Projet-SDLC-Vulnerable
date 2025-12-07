<?php
// Fichier : src/logout.php
// Logique de déconnexion

require_once 'config.php';

// Détruire la session
session_unset();
session_destroy();

// Rediriger vers la page de connexion
redirect('/login.php');
?>
