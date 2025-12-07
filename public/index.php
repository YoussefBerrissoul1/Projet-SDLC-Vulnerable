<?php
// Fichier : public/index.php
// Point d'entrée unique (Front Controller)

// Définir le chemin de base pour l'inclusion des fichiers
define('BASE_PATH', __DIR__ . '/../src/');

// Récupérer le chemin de la requête
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Définir les routes
$routes = [
    '' => 'dashboard.php', // Page par défaut après connexion
    'login.php' => 'login.php',
    'dashboard.php' => 'dashboard.php',
    'logout.php' => 'logout.php',
];

// Router la requête
if (array_key_exists($request_uri, $routes)) {
    $file = BASE_PATH . $routes[$request_uri];
    if (file_exists($file)) {
        require $file;
    } else {
        // VULNÉRABILITÉ : Gestion d'erreur faible
        http_response_code(500);
        echo "Erreur interne du serveur : Fichier de route non trouvé.";
    }
} else {
    // Redirection vers la page de connexion si la route n'est pas trouvée
    // Ceci est une simplification, un vrai routeur ferait mieux
    if (!isset($_SESSION['user_id'])) {
        require BASE_PATH . 'login.php';
    } else {
        require BASE_PATH . 'dashboard.php';
    }
}
?>
