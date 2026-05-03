<?php
// Page protegee, sinon quelqu'un non connecter pourrais voir les alertes
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';

// Je met le titre de la page avant de charger le header
$pageTitle = 'Alertes';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <!-- Bloc central pour afficher les alertes, plus tard on pourra mettre une liste -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold mb-3">Alertes</h1>
            <p class="text-secondary mb-4">
                Cette page est prévue pour afficher les alertes importantes du système.
            </p>

            <!-- Message temporaire quand il y a pas encore d'alerte active -->
            <div class="alert alert-warning mb-0" role="alert">
                Aucune alerte active pour le moment.
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>