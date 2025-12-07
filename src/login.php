<?php
// Fichier : src/login.php
// Logique d'authentification vulnérable

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $db = get_db_connection();

        // VULNÉRABILITÉ : INJECTION SQL (requête non paramétrée)
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $stmt = $db->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // VULNÉRABILITÉ : SESSION INSÉCURISÉE (pas de régénération d'ID)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('/dashboard.php');
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Vulnérable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <p>Utilisateur de test : admin / password123</p>
    </div>
</body>
</html>
