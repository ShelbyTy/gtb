<?php
// Ici je protège la page car on doit être connecté pour voir le tableau de bord
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';

// Titre qui va s'afficher dans l'onglet du navigateur
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <!-- Partie principale du dashboard avec les raccourcis des pages -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-2">Tableau de bord</h1>
                    <p class="text-secondary mb-0">
                        Vue simple du projet GTB pour suivre les salles, les caméras et les alertes.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <!-- Carte pour aller sur la liste des salles -->
                    <h2 class="h5 fw-bold">Salles</h2>
                    <p class="text-secondary">Consulter les salles enregistrées dans la base de données.</p>
                    <a href="salles.php" class="btn btn-outline-primary btn-sm">Voir les salles</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <!-- Carte des caméras, pour l'instant c'est surtout une page prévue -->
                    <h2 class="h5 fw-bold">Caméras</h2>
                    <p class="text-secondary">Accéder à la page prévue pour la surveillance vidéo.</p>
                    <a href="cameras.php" class="btn btn-outline-primary btn-sm">Voir les caméras</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <!-- Carte qui envoie vers les alertes du système -->
                    <h2 class="h5 fw-bold">Alertes</h2>
                    <p class="text-secondary">Retrouver les notifications importantes du système.</p>
                    <a href="alertes.php" class="btn btn-outline-primary btn-sm">Voir les alertes</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
