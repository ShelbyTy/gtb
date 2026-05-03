<?php
// Page qui affiche les salles enregistrées dans la base
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/database.php';

$salles = [];
$erreur = '';

try {
    // On récupère les salles sans les écrire en dur dans le code
    $query = $conn->query("SELECT id, nom, type, open_for_all FROM salles ORDER BY nom ASC");
    $salles = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erreur = "Impossible de récupérer la liste des salles.";
}

$pageTitle = 'Salles';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-2">Salles</h1>
                    <p class="text-secondary mb-0">
                        Liste des salles répertoriées dans le projet GTB.
                    </p>
                </div>
            </div>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="col-12">
                <div class="alert alert-danger mb-0" role="alert">
                    <?= htmlspecialchars($erreur) ?>
                </div>
            </div>
        <?php elseif (empty($salles)): ?>
            <div class="col-12">
                <div class="alert alert-warning mb-0" role="alert">
                    Aucune salle n'est enregistrée pour le moment.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($salles as $salle): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 fw-bold mb-2"><?= htmlspecialchars($salle['nom']) ?></h2>
                            <p class="text-secondary mb-3">
                                Type : <?= htmlspecialchars($salle['type']) ?>
                            </p>
                            <p class="mb-4">
                                <?php if ((int) $salle['open_for_all'] === 1): ?>
                                    <span class="badge text-bg-success">Ouverte à tous</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Accès limité</span>
                                <?php endif; ?>
                            </p>

                            <a href="salle-detail.php?id=<?= (int) $salle['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto">
                                Voir la salle
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
