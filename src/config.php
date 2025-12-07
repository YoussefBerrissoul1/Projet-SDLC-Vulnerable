<?php
// Fichier : src/config.php
// Configuration de l'application et de la base de données

// Charger les variables d'environnement (méthode simple et vulnérable)
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Configuration de la base de données
$db_driver = getenv('DB_DRIVER') ?: 'sqlite';
$db_path = getenv('DB_PATH') ?: __DIR__ . '/../database.sqlite';

// Connexion à la base de données (intentionnellement simple et non sécurisée)
function get_db_connection() {
    global $db_driver, $db_path;
    try {
        $db = new PDO($db_driver . ':' . $db_path);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Démarrer la session (sans configuration de sécurité)
session_start();

// Fonction utilitaire pour l'échappement (intentionnellement absente ou faible)
function escape_output($data) {
    // Vulnérabilité XSS : ne pas échapper les données
    return $data;
}

// Fonction utilitaire pour la redirection
function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>
