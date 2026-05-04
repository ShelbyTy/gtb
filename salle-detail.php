<?php
// Page de détail pour une salle précise
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/database.php';

$salle = null;
$capteurs = [];
$cameras = [];
$capteurStats = [];
$capteurMesures = [];
$erreur = '';
$capteursInfo = '';
$camerasInfo = '';
$mesuresInfo = '';
$refreshDelay = 30;

// Vérifie si une table existe dans la base courante
function table_exists(PDO $conn, string $tableName): bool
{
    $query = $conn->prepare("
        SELECT COUNT(*)
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :table_name
    ");
    $query->execute([':table_name' => $tableName]);

    return (int) $query->fetchColumn() > 0;
}

// Vérifie si une colonne existe dans une table
function column_exists(PDO $conn, string $tableName, string $columnName): bool
{
    $query = $conn->prepare("
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :table_name
        AND COLUMN_NAME = :column_name
    ");
    $query->execute([
        ':table_name' => $tableName,
        ':column_name' => $columnName,
    ]);

    return (int) $query->fetchColumn() > 0;
}

// Renvoie la première colonne trouvée dans une liste possible
function first_existing_column(PDO $conn, string $tableName, array $columns): ?string
{
    foreach ($columns as $column) {
        if (column_exists($conn, $tableName, $column)) {
            return $column;
        }
    }

    return null;
}

// Petit helper pour afficher une valeur si la colonne existe
function array_value(array $row, string $key, string $default = ''): string
{
    return isset($row[$key]) && $row[$key] !== '' ? (string) $row[$key] : $default;
}

// Récupère les statistiques d'un capteur si la table mesures existe
function get_sensor_stats(PDO $conn, int $capteurId): array
{
    if (!table_exists($conn, 'mesures')) {
        return [];
    }

    $valueColumn = first_existing_column($conn, 'mesures', ['valeur', 'value', 'mesure']);
    $dateColumn = first_existing_column($conn, 'mesures', ['created_at', 'date_mesure', 'date']);
    $typeColumn = first_existing_column($conn, 'mesures', ['type_mesure', 'type', 'nom']);

    if (!$valueColumn || !$dateColumn || !column_exists($conn, 'mesures', 'id_capteur')) {
        return [];
    }

    if ($typeColumn) {
        $query = $conn->prepare("
            SELECT
                {$typeColumn} AS mesure_type,
                COUNT(*) AS total_mesures,
                ROUND(MIN({$valueColumn}), 2) AS valeur_min,
                ROUND(MAX({$valueColumn}), 2) AS valeur_max,
                ROUND(AVG({$valueColumn}), 2) AS valeur_moyenne,
                SUBSTRING_INDEX(GROUP_CONCAT({$valueColumn} ORDER BY {$dateColumn} DESC SEPARATOR ','), ',', 1) AS derniere_valeur,
                MAX({$dateColumn}) AS derniere_date
            FROM mesures
            WHERE id_capteur = :id_capteur
            GROUP BY {$typeColumn}
            ORDER BY {$typeColumn} ASC
        ");
    } else {
        $query = $conn->prepare("
            SELECT
                'Mesure' AS mesure_type,
                COUNT(*) AS total_mesures,
                ROUND(MIN({$valueColumn}), 2) AS valeur_min,
                ROUND(MAX({$valueColumn}), 2) AS valeur_max,
                ROUND(AVG({$valueColumn}), 2) AS valeur_moyenne,
                SUBSTRING_INDEX(GROUP_CONCAT({$valueColumn} ORDER BY {$dateColumn} DESC SEPARATOR ','), ',', 1) AS derniere_valeur,
                MAX({$dateColumn}) AS derniere_date
            FROM mesures
            WHERE id_capteur = :id_capteur
            GROUP BY id_capteur
        ");
    }

    $query->execute([':id_capteur' => $capteurId]);

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Recupere toutes les valeurs mesurees pour un capteur
function get_sensor_measures(PDO $conn, int $capteurId): array
{
    if (!table_exists($conn, 'mesures')) {
        return [];
    }

    $valueColumn = first_existing_column($conn, 'mesures', ['valeur', 'value', 'mesure']);
    $dateColumn = first_existing_column($conn, 'mesures', ['created_at', 'date_mesure', 'date']);
    $typeColumn = first_existing_column($conn, 'mesures', ['type_mesure', 'type', 'nom']);

    if (!$valueColumn || !$dateColumn || !column_exists($conn, 'mesures', 'id_capteur')) {
        return [];
    }

    $typeExpression = $typeColumn ? $typeColumn : "'Mesure'";
    $query = $conn->prepare("
        SELECT
            {$typeExpression} AS mesure_type,
            ROUND({$valueColumn}, 2) AS valeur,
            {$dateColumn} AS date_mesure
        FROM mesures
        WHERE id_capteur = :id_capteur
        ORDER BY {$dateColumn} DESC
    ");
    $query->execute([':id_capteur' => $capteurId]);

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$salleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$salleId || $salleId < 1) {
    $erreur = "Identifiant de salle invalide.";
} else {
    try {
        // On récupère la salle demandée
        $query = $conn->prepare("SELECT id, nom, type, open_for_all FROM salles WHERE id = :id");
        $query->execute([':id' => $salleId]);
        $salle = $query->fetch(PDO::FETCH_ASSOC);

        if (!$salle) {
            $erreur = "Salle introuvable.";
        }
    } catch (PDOException $e) {
        $erreur = "Impossible de récupérer les informations de la salle.";
    }
}

if ($salle && empty($erreur)) {
    try {
        if (column_exists($conn, 'capteurs', 'id_salle')) {
            // Si la colonne id_salle existe, on peut relier les capteurs à la salle
            $query = $conn->prepare("SELECT id, type, is_connected, id_arduino, unite FROM capteurs WHERE id_salle = :id_salle ORDER BY type ASC");
            $query->execute([':id_salle' => $salleId]);
            $capteurs = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($capteurs as $capteur) {
                $capteurStats[(int) $capteur['id']] = get_sensor_stats($conn, (int) $capteur['id']);
                $capteurMesures[(int) $capteur['id']] = get_sensor_measures($conn, (int) $capteur['id']);
            }

            if (!table_exists($conn, 'mesures')) {
                $mesuresInfo = "La table mesures n'existe pas encore. Les statistiques apparaîtront quand elle sera créée et remplie.";
            }
        } else {
            $capteursInfo = "La table capteurs existe, mais elle n'a pas encore de colonne id_salle pour relier les capteurs aux salles.";
        }
    } catch (PDOException $e) {
        $capteursInfo = "Impossible de récupérer les capteurs de cette salle.";
    }

    try {
        if (table_exists($conn, 'cameras')) {
            if (column_exists($conn, 'cameras', 'id_salle')) {
                // La table cameras peut avoir plusieurs formes, donc on récupère toutes ses colonnes
                $query = $conn->prepare("SELECT * FROM cameras WHERE id_salle = :id_salle ORDER BY id ASC");
                $query->execute([':id_salle' => $salleId]);
                $cameras = $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $camerasInfo = "La table cameras existe, mais elle n'a pas encore de colonne id_salle.";
            }
        } else {
            $camerasInfo = "La table cameras n'existe pas encore dans la base de données.";
        }
    } catch (PDOException $e) {
        $camerasInfo = "Impossible de récupérer les caméras de cette salle.";
    }
}

$pageTitle = $salle ? 'Salle - ' . $salle['nom'] : 'Détail salle';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <div class="row g-4">
        <div class="col-12">
            <a href="salles.php" class="btn btn-outline-primary btn-sm mb-3">Retour aux salles</a>

            <?php if (!empty($erreur)): ?>
                <div class="alert alert-danger mb-0" role="alert">
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <h1 class="h3 fw-bold mb-2"><?= htmlspecialchars($salle['nom']) ?></h1>
                                <p class="text-secondary mb-2">Type : <?= htmlspecialchars($salle['type']) ?></p>

                                <?php if ((int) $salle['open_for_all'] === 1): ?>
                                    <span class="badge text-bg-success">Ouverte à tous</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Accès limité</span>
                                <?php endif; ?>
                            </div>

                            <p class="text-secondary small mb-0">
                                Actualisation automatique : <?= (int) $refreshDelay ?> secondes
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($salle && empty($erreur)): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Mesures et statistiques des capteurs</h2>

                        <?php if (!empty($mesuresInfo)): ?>
                            <div class="alert alert-warning" role="alert">
                                <?= htmlspecialchars($mesuresInfo) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($capteurs)): ?>
                            <div class="row g-3">
                                <?php foreach ($capteurs as $capteur): ?>
                                    <?php $stats = $capteurStats[(int) $capteur['id']] ?? []; ?>
                                    <?php $mesures = $capteurMesures[(int) $capteur['id']] ?? []; ?>
                                    <div class="col-12">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                                <div>
                                                    <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($capteur['type']) ?></h3>
                                                    <p class="text-secondary mb-0">
                                                        Unité : <?= htmlspecialchars($capteur['unite']) ?> |
                                                        Arduino : <?= htmlspecialchars((string) $capteur['id_arduino']) ?>
                                                    </p>
                                                </div>

                                                <?php if ((int) $capteur['is_connected'] === 1): ?>
                                                    <span class="badge text-bg-success">Connecté</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-danger">Déconnecté</span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (!empty($mesures)): ?>
                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label for="measure-type-<?= (int) $capteur['id'] ?>" class="form-label">Type de mesure</label>
                                                        <select class="form-select sensor-measure-select" id="measure-type-<?= (int) $capteur['id'] ?>" data-capteur-id="<?= (int) $capteur['id'] ?>">
                                                            <?php foreach ($stats as $statIndex => $stat): ?>
                                                                <option value="<?= htmlspecialchars($stat['mesure_type']) ?>" <?= $statIndex === 0 ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($stat['mesure_type']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Mesure</th>
                                                                <th>Valeur</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $selectedMeasureType = $stats[0]['mesure_type'] ?? ''; ?>
                                                            <?php foreach ($mesures as $mesure): ?>
                                                                <tr
                                                                    class="sensor-measure-row <?= $mesure['mesure_type'] === $selectedMeasureType ? '' : 'd-none' ?>"
                                                                    data-capteur-id="<?= (int) $capteur['id'] ?>"
                                                                    data-mesure-type="<?= htmlspecialchars($mesure['mesure_type']) ?>">
                                                                    <td><?= htmlspecialchars($mesure['mesure_type']) ?></td>
                                                                    <td><?= htmlspecialchars((string) $mesure['valeur']) ?></td>
                                                                    <td><?= htmlspecialchars((string) $mesure['date_mesure']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info mb-0" role="alert">
                                                    Aucune mesure n'est encore disponible pour ce capteur.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($capteursInfo)): ?>
                            <div class="alert alert-warning mb-0" role="alert">
                                <?= htmlspecialchars($capteursInfo) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0" role="alert">
                                Aucun capteur n'est rattaché à cette salle.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Caméras installées</h2>

                        <?php if (!empty($cameras)): ?>
                            <div class="list-group">
                                <?php foreach ($cameras as $camera): ?>
                                    <?php
                                    $cameraName = array_value($camera, 'nom', 'Caméra #' . array_value($camera, 'id', '?'));
                                    $cameraUrl = array_value($camera, 'url_flux', array_value($camera, 'flux_url', array_value($camera, 'url')));
                                    $cameraStatus = array_value($camera, 'camera_status', array_value($camera, 'is_connected'));
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($cameraName) ?></h3>

                                                <?php if (!empty($cameraUrl)): ?>
                                                    <a href="<?= htmlspecialchars($cameraUrl) ?>" target="_blank" rel="noopener" class="link-primary">
                                                        Ouvrir le flux
                                                    </a>
                                                <?php else: ?>
                                                    <p class="text-secondary mb-0">Aucun flux renseigné.</p>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($cameraStatus !== ''): ?>
                                                <?php if ((int) $cameraStatus === 1): ?>
                                                    <span class="badge text-bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($camerasInfo)): ?>
                            <div class="alert alert-warning mb-0" role="alert">
                                <?= htmlspecialchars($camerasInfo) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0" role="alert">
                                Aucune caméra n'est rattachée à cette salle.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.querySelectorAll('.sensor-measure-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const capteurId = select.dataset.capteurId;
            const selectedType = select.value;

            document.querySelectorAll('.sensor-measure-row[data-capteur-id="' + capteurId + '"]').forEach(function(row) {
                row.classList.toggle('d-none', row.dataset.mesureType !== selectedType);
            });
        });
    });

    // Actualisation modérée pour remettre les statistiques à jour
    setTimeout(function() {
        window.location.reload();
    }, <?= (int) $refreshDelay ?> * 1000);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
