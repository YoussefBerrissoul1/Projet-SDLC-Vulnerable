<?php
// Fichier : src/database.php
// Script d'initialisation de la base de données SQLite

$db_path = __DIR__ . '/../database.sqlite';

try {
    // Supprimer l'ancienne base de données si elle existe
    if (file_exists($db_path)) {
        unlink($db_path);
    }

    // Créer la connexion à la base de données
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la table des utilisateurs
    // Note : Le mot de passe est stocké en clair pour introduire une vulnérabilité SAST
    $db->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT NOT NULL
        );
    ");

    // Créer la table des messages (pour la vulnérabilité XSS/SQLi)
    $db->exec("
        CREATE TABLE messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

    // Insérer un utilisateur de test (mot de passe en clair : 'password123')
    $db->exec("
        INSERT INTO users (username, password, email) VALUES ('admin', 'password123', 'admin@example.com');
    ");

    echo "Base de données SQLite créée et initialisée avec succès : " . $db_path . "\n";

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
}
?>
