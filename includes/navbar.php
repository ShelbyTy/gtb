<?php
// Barre de navigation commune des pages connectées
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../config/database.php';

// Token utilisé pour sécuriser le bouton de déconnexion
$logout_token = get_csrf_token();
// Permet de savoir quelle page est active dans le menu
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$username = 'Utilisateur';

// On récupère le nom de l'utilisateur pour l'afficher dans le menu
if (!empty($_SESSION['user_id'])) {
    try {
        $query = $conn->prepare("SELECT username FROM users WHERE id = :id");
        $query->execute([':id' => $_SESSION['user_id']]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!empty($user['username'])) {
            $username = $user['username'];
        }
    } catch (PDOException $e) {
        $username = 'Utilisateur';
    }
}

// Liste des liens du menu, comme ça c'est plus facile à changer
$navItems = [
    'dashboard.php' => 'Tableau de bord',
    'salles.php' => 'Salles',
    'cameras.php' => 'Caméras',
    'alertes.php' => 'Alertes',
];
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Nom du projet qui renvoie au tableau de bord -->
        <a class="navbar-brand fw-bold" href="dashboard.php">GTB</a>

        <!-- Bouton affiché sur téléphone pour ouvrir le menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Afficher le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Boucle sur les liens pour éviter de recopier plein de HTML -->
                <?php foreach ($navItems as $href => $label): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === $href ? 'active fw-semibold' : '' ?>" href="<?= htmlspecialchars($href) ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Barre de recherche simple pour retrouver une fonction du site -->
            <form class="d-flex me-lg-3 mb-2 mb-lg-0" role="search" id="navbarSearchForm">
                <input class="form-control form-control-sm me-2" type="search" id="navbarSearchInput" placeholder="Rechercher" aria-label="Rechercher une fonction" list="navbarSearchList">
                <datalist id="navbarSearchList">
                    <option value="Tableau de bord">
                    <option value="Salles">
                    <option value="Caméras">
                    <option value="Alertes">
                </datalist>
                <button class="btn btn-outline-light btn-sm" type="submit">OK</button>
            </form>

            <!-- Menu utilisateur avec le nom et la déconnexion -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($username) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="logout.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($logout_token) ?>">
                                <button type="submit" class="dropdown-item">Se déconnecter</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Petite recherche simple, elle envoie vers la page qui correspond au mot tapé
    document.getElementById('navbarSearchForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const searchValue = document.getElementById('navbarSearchInput').value.trim().toLowerCase();
        const pages = {
            'tableau de bord': 'dashboard.php',
            'dashboard': 'dashboard.php',
            'accueil': 'dashboard.php',
            'salle': 'salles.php',
            'salles': 'salles.php',
            'capteur': 'salles.php',
            'capteurs': 'salles.php',
            'camera': 'cameras.php',
            'caméra': 'cameras.php',
            'cameras': 'cameras.php',
            'caméras': 'cameras.php',
            'alerte': 'alertes.php',
            'alertes': 'alertes.php'
        };

        if (pages[searchValue]) {
            window.location.href = pages[searchValue];
            return;
        }

        alert('Aucune fonction trouvée.');
    });
</script>
