<?php
// Page de détail d'une salle : capteurs, mesures et caméras
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/database.php';

// Validation de l'ID passé dans l'URL (?id=3)
// FILTER_VALIDATE_INT retourne false si ce n'est pas un entier valide
$salleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$salleId || $salleId < 1) {
    die('Identifiant de salle invalide.');
}

// On récupère la salle — si elle n'existe pas, inutile de continuer
$req = $conn->prepare("SELECT id, nom, type, open_for_all FROM salles WHERE id = :id");
$req->execute([':id' => $salleId]);
$salle = $req->fetch(PDO::FETCH_ASSOC);

if (!$salle) {
    die('Salle introuvable.');
}

// Tous les capteurs rattachés à cette salle
$req = $conn->prepare("
    SELECT id, type, unite, id_arduino, is_connected
    FROM capteurs
    WHERE id_salle = :id_salle
    ORDER BY type ASC
");
$req->execute([':id_salle' => $salleId]);
$capteurs = $req->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque capteur : stats + 10 dernières mesures
$capteurStats   = [];
$capteurMesures = [];

foreach ($capteurs as $capteur) {
    $cid = (int) $capteur['id'];

    // Statistiques groupées par type de mesure (température, CO2, etc.)
    $req = $conn->prepare("
        SELECT
            type_mesure,
            COUNT(*)                AS total,
            ROUND(MIN(valeur), 2)   AS valeur_min,
            ROUND(MAX(valeur), 2)   AS valeur_max,
            ROUND(AVG(valeur), 2)   AS valeur_moyenne
        FROM mesures
        WHERE id_capteur = :id_capteur
        GROUP BY type_mesure
        ORDER BY type_mesure ASC
    ");
    $req->execute([':id_capteur' => $cid]);
    $capteurStats[$cid] = $req->fetchAll(PDO::FETCH_ASSOC);

    // Les 10 mesures les plus récentes pour l'historique
    $req = $conn->prepare("
        SELECT type_mesure, ROUND(valeur, 2) AS valeur, created_at
        FROM mesures
        WHERE id_capteur = :id_capteur
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $req->execute([':id_capteur' => $cid]);
    $capteurMesures[$cid] = $req->fetchAll(PDO::FETCH_ASSOC);
}

// Caméras installées dans la salle
$req = $conn->prepare("
    SELECT id, nom, url_flux, camera_status
    FROM cameras
    WHERE id_salle = :id_salle
    ORDER BY id ASC
");
$req->execute([':id_salle' => $salleId]);
$cameras = $req->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Salle - ' . $salle['nom'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <div class="row g-4">

        <!-- En-tête de la salle -->
        <div class="col-12">
            <a href="salles.php" class="btn btn-outline-primary btn-sm mb-3">Retour aux salles</a>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h3 fw-bold mb-1"><?= htmlspecialchars($salle['nom']) ?></h1>
                            <p class="text-secondary mb-2">Type : <?= htmlspecialchars($salle['type']) ?></p>
                            <?php if ((int) $salle['open_for_all'] === 1): ?>
                                <span class="badge text-bg-success">Ouverte à tous</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Accès limité</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-secondary small mb-0">Actualisation automatique : 30 s</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section capteurs -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Capteurs et mesures</h2>

                    <?php if (empty($capteurs)): ?>
                        <div class="alert alert-info mb-0">Aucun capteur rattaché à cette salle.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($capteurs as $capteur): ?>
                                <?php
                                $cid     = (int) $capteur['id'];
                                $stats   = $capteurStats[$cid];
                                $mesures = $capteurMesures[$cid];
                                ?>
                                <div class="col-12">
                                    <div class="border rounded p-3">

                                        <!-- En-tête du capteur -->
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                            <div>
                                                <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($capteur['type']) ?></h3>
                                                <p class="text-secondary small mb-0">
                                                    Unité : <?= htmlspecialchars($capteur['unite']) ?>
                                                    &mdash; Arduino : <?= htmlspecialchars((string) $capteur['id_arduino']) ?>
                                                </p>
                                            </div>
                                            <?php if ((int) $capteur['is_connected'] === 1): ?>
                                                <span class="badge text-bg-success">Connecté</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-danger">Déconnecté</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (empty($mesures)): ?>
                                            <div class="alert alert-info mb-0">Aucune mesure disponible pour ce capteur.</div>
                                        <?php else: ?>

                                            <!-- Tableau des statistiques (une ligne par type de mesure) -->
                                            <h4 class="h6 text-secondary mb-2">Statistiques</h4>
                                            <div class="table-responsive mb-3">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Type de mesure</th>
                                                            <th>Total relevés</th>
                                                            <th>Min</th>
                                                            <th>Max</th>
                                                            <th>Moyenne</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($stats as $s): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($s['type_mesure']) ?></td>
                                                                <td><?= (int) $s['total'] ?></td>
                                                                <td><?= htmlspecialchars((string) $s['valeur_min']) ?> <?= htmlspecialchars($capteur['unite']) ?></td>
                                                                <td><?= htmlspecialchars((string) $s['valeur_max']) ?> <?= htmlspecialchars($capteur['unite']) ?></td>
                                                                <td><?= htmlspecialchars((string) $s['valeur_moyenne']) ?> <?= htmlspecialchars($capteur['unite']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Les 10 dernières mesures -->
                                            <h4 class="h6 text-secondary mb-2">10 dernières mesures</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Type de mesure</th>
                                                            <th>Valeur</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($mesures as $m): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($m['type_mesure']) ?></td>
                                                                <td><?= htmlspecialchars((string) $m['valeur']) ?> <?= htmlspecialchars($capteur['unite']) ?></td>
                                                                <td><?= htmlspecialchars((string) $m['created_at']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Section caméras -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Caméras installées</h2>

                    <?php if (empty($cameras)): ?>
                        <div class="alert alert-info mb-0">Aucune caméra rattachée à cette salle.</div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($cameras as $camera): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($camera['nom']) ?></h3>
                                            <?php if (!empty($camera['url_flux'])): ?>
                                                <a href="<?= htmlspecialchars($camera['url_flux']) ?>" target="_blank" rel="noopener" class="link-primary">
                                                    Ouvrir le flux
                                                </a>
                                            <?php else: ?>
                                                <p class="text-secondary mb-0">Aucun flux renseigné.</p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ((int) $camera['camera_status'] === 1): ?>
                                            <span class="badge text-bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>
</main>

<!-- Actualisation automatique toutes les 30 secondes -->
<script>
    setTimeout(function () {
        window.location.reload();
    }, 30000);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
