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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - SDLC</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading">SDLC Social</div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-home me-2"></i>Fil d'actualité
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i>Amis
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-cog me-2"></i>Paramètres
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger mt-3">
                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <button class="btn btn-outline-primary" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <span class="me-3 fw-bold text-dark d-none d-md-block">
                            Bonjour, <?php echo escape_output($username); ?>
                        </span>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        
                        <!-- Post Form -->
                        <div class="custom-card p-4">
                            <h5 class="fw-bold mb-3 text-secondary">Créer une publication</h5>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form method="POST" action="dashboard.php">
                                <div class="d-flex">
                                    <div class="user-avatar flex-shrink-0">
                                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                                    </div>
                                    <div class="w-100">
                                        <textarea name="message_content" class="form-control post-input" rows="3" placeholder="Qu'avez-vous en tête, <?php echo escape_output($username); ?> ?"></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-light text-primary me-2 rounded-pill">
                                        <i class="fas fa-image me-1"></i> Photo
                                    </button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                        Publier <i class="fas fa-paper-plane ms-1"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Feed -->
                        <h5 class="mb-4 text-muted fw-bold">Publications Récents</h5>
                        <div class="message-feed">
                            <?php foreach ($messages as $message): ?>
                                <div class="custom-card">
                                    <div class="card-header-feed d-flex align-items-center">
                                        <div class="user-avatar" style="background-color: var(--secondary-color);">
                                            <?php echo strtoupper(substr($message['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo escape_output($message['username']); ?></h6>
                                            <small class="text-muted" style="font-size: 0.85rem;">
                                                <i class="far fa-clock me-1"></i><?php echo $message['created_at']; ?>
                                            </small>
                                        </div>
                                        <div class="ms-auto">
                                            <button class="btn btn-sm btn-link text-muted"><i class="fas fa-ellipsis-h"></i></button>
                                        </div>
                                    </div>
                                    <div class="p-3 px-4">
                                        <p class="mb-2" style="font-size: 1.05rem; line-height: 1.6;">
                                            <?php echo $message['content']; ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 py-2 px-4 d-flex justify-content-between">
                                        <button class="btn btn-sm btn-link text-secondary text-decoration-none">
                                            <i class="far fa-heart me-1"></i> J'aime
                                        </button>
                                        <button class="btn btn-sm btn-link text-secondary text-decoration-none">
                                            <i class="far fa-comment me-1"></i> Commenter
                                        </button>
                                        <button class="btn btn-sm btn-link text-secondary text-decoration-none">
                                            <i class="fas fa-share me-1"></i> Partager
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    
                    <!-- Right Sidebar (Suggestions in future) -->
                    <div class="col-lg-4 d-none d-lg-block">
                        <div class="custom-card p-3">
                            <h6 class="fw-bold text-muted mb-3">Suggestions</h6>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                                <p class="small">Aucune suggestion pour le moment.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
</body>
</html>
