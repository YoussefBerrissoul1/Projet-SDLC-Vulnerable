<?php
// Fichier : src/dashboard.php
// Tableau de bord et formulaire de message vulnérable

require_once 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$db = get_db_connection();
$username = $_SESSION['username'];
$messages = [];
$error = '';

// Logique d'ajout de message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content'])) {
    $content = $_POST['message_content'];
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        $error = "Le message ne peut pas être vide.";
    } else {
        // VULNÉRABILITÉ : XSS (stockage de contenu non échappé)
        $stmt = $db->prepare("INSERT INTO messages (user_id, content) VALUES (?, ?)");
        $stmt->execute([$user_id, $content]);
        redirect('/dashboard.php');
    }
}

// Récupération des messages
$stmt = $db->query("SELECT u.username, m.content, m.created_at FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - Vulnérable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo escape_output($username); ?>!</h1>
        <p><a href="logout.php">Déconnexion</a></p>

        <h2>Poster un Message</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="dashboard.php">
            <!-- VULNÉRABILITÉ : CSRF (absence de token) -->
            <textarea name="message_content" rows="4" placeholder="Écrivez votre message ici..."></textarea>
            <button type="submit">Publier</button>
        </form>

        <h2>Fil de Messages</h2>
        <div class="message-feed">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <strong><?php echo escape_output($message['username']); ?></strong>
                    <small>(<?php echo $message['created_at']; ?>)</small>
                    <p>
                        <!-- VULNÉRABILITÉ : XSS (affichage de contenu non échappé) -->
                        <?php echo $message['content']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
