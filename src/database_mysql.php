<?php
// Fichier : src/database_mysql.php
// Script d'initialisation de la base de données MySQL

// Charger les variables d'environnement
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

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'vulnerable_app';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';

try {
    // Connexion au serveur MySQL (sans base de données)
    $dsn = "mysql:host=$db_host;port=$db_port;charset=utf8mb4";
    $db = new PDO($dsn, $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la base de données si elle n'existe pas
    $db->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    echo "Base de données '$db_name' créée ou existante.\n";

    // Sélectionner la base de données
    $db->exec("USE $db_name");

    // Créer la table des utilisateurs
    // Note : Le mot de passe est stocké en clair pour introduire une vulnérabilité SAST
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'users' créée ou existante.\n";

    // Créer la table des messages (pour la vulnérabilité XSS/SQLi)
    $db->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'messages' créée ou existante.\n";

    // Vérifier si l'utilisateur admin existe avant d'insérer
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insérer un utilisateur de test (mot de passe en clair : 'password123')
        $db->exec("
            INSERT INTO users (username, password, email) VALUES ('admin', 'password123', 'admin@example.com');
        ");
        echo "Utilisateur de test 'admin' inséré.\n";
    } else {
        echo "Utilisateur 'admin' existe déjà.\n";
    }


    $stmt->execute(['alice']);
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO users (username, password, email) VALUES ('alice', 'alice123', 'alice@example.com')");
        echo "Utilisateur 'alice' inséré.\n";
    }

    $stmt->execute(['bob']);
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO users (username, password, email) VALUES ('bob', 'bob123', 'bob@example.com')");
        echo "Utilisateur 'bob' inséré.\n";
    }

    $stmt->execute(['hacker']);
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO users (username, password, email) VALUES ('hacker', 'hacker123', 'hacker@evil.com')");
        echo "Utilisateur 'hacker' inséré.\n";
    }

    echo "\nBase de données MySQL initialisée avec succès !\n";
    echo "Hôte: $db_host\n";
    echo "Port: $db_port\n";
    echo "Base de données: $db_name\n";
    echo "Utilisateur: $db_user\n";

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
    exit(1);
}
?>
