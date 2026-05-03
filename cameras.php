<?php
// On verifie que l'utilisateur est bien connecter avant d'afficher les cameras
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';

// Nom de la page dans le navigateur
$pageTitle = 'Caméras';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <!-- Contenu de la page cameras, elle est encore simple pour le moment -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold mb-3">Caméras</h1>
            <p class="text-secondary mb-4">
                Cette page est prévue pour regrouper les caméras du projet GTB.
            </p>

            <!-- Info afficher car les flux video ne sont pas brancher encore -->
            <div class="alert alert-warning mb-0" role="alert">
                Aucun flux caméra n'est encore configuré.
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>