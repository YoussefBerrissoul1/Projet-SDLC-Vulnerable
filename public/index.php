<?php
// Fichier : public/index.php
// Point d'entrée unique (Front Controller)

// Définir le chemin de base pour l'inclusion des fichiers
define('BASE_PATH', __DIR__ . '/../src/');

// Récupérer le chemin de la requête (Compatible sous-dossiers)
$script_name = $_SERVER['SCRIPT_NAME'];
$request_uri = $_SERVER['REQUEST_URI'];

// Retirer les paramètres de requête (?)
if (false !== $pos = strpos($request_uri, '?')) {
    $request_uri = substr($request_uri, 0, $pos);
}

// Détecter le dossier de base (ex: /Projet-SDLC-Vulnerable/public)
$base_dir = dirname($script_name);

// Retirer le dossier de base de l'URI demandée
if (strpos($request_uri, $base_dir) === 0) {
    $request_uri = substr($request_uri, strlen($base_dir));
}

// Nettoyer les slashs
$request_uri = trim($request_uri, '/');

// Définir une constante pour l'URL de base (pour les redirections)
define('WEB_ROOT', rtrim($base_dir, '/') . '/');

// Définir les routes
$routes = [
    '' => 'dashboard.php',
    'login' => 'login.php', // Route sans extension
    'login.php' => 'login.php',
    'dashboard' => 'dashboard.php', // Route sans extension
    'dashboard.php' => 'dashboard.php',
    'logout' => 'logout.php',
    'logout.php' => 'logout.php',
];

// Router la requête
if (array_key_exists($request_uri, $routes)) {
    $file = BASE_PATH . $routes[$request_uri];
    if (file_exists($file)) {
        require $file;
    } else {
        // VULNÉRABILITÉ : Gestion d'erreur faible
        http_response_code(404);
        echo "Erreur 404 : Page non trouvée ($request_uri).";
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
