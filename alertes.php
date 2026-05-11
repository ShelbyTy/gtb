<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/database.php';

$alertes = [];
$erreur = '';

try {
    $query = $conn->query("
        SELECT
            a.id,
            a.type_alerte,
            a.message,
            a.valeur_declencheur,
            a.seuil,
            a.niveau,
            a.is_resolved,
            a.created_at,
            a.resolved_at,
            c.type AS capteur_type,
            s.nom  AS salle_nom
        FROM alertes a
        JOIN capteurs c ON c.id = a.id_capteur
        JOIN salles  s ON s.id = c.id_salle
        ORDER BY a.is_resolved ASC, a.created_at DESC
    ");
    $alertes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erreur = "Impossible de récupérer les alertes.";
}

$pageTitle = 'Alertes';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container page-shell">
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-2">Alertes</h1>
                    <p class="text-secondary mb-0">
                        Liste des alertes déclenchées par les capteurs du système.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger mb-0" role="alert">
                            <?= htmlspecialchars($erreur) ?>
                        </div>

                    <?php elseif (empty($alertes)): ?>
                        <div class="alert alert-warning mb-0" role="alert">
                            Aucune alerte active pour le moment.
                        </div>

                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($alertes as $alerte): ?>
                                <?php
                                $niveauClass = match ($alerte['niveau']) {
                                    'critical' => 'danger',
                                    'info'     => 'primary',
                                    default    => 'warning',
                                };
                                $resolved = (int) $alerte['is_resolved'] === 1;
                                ?>
                                <div class="list-group-item <?= $resolved ? 'opacity-50' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge text-bg-<?= $niveauClass ?>">
                                                    <?= htmlspecialchars(ucfirst($alerte['niveau'])) ?>
                                                </span>
                                                <h2 class="h6 fw-bold mb-0">
                                                    <?= htmlspecialchars($alerte['type_alerte']) ?>
                                                </h2>
                                            </div>

                                            <p class="text-secondary mb-1">
                                                <?= htmlspecialchars($alerte['message']) ?>
                                            </p>

                                            <p class="text-secondary small mb-0">
                                                Capteur : <?= htmlspecialchars($alerte['capteur_type']) ?>
                                                &mdash;
                                                Salle : <?= htmlspecialchars($alerte['salle_nom']) ?>
                                                <?php if ($alerte['valeur_declencheur'] !== null): ?>
                                                    &mdash;
                                                    Valeur : <strong><?= htmlspecialchars((string) $alerte['valeur_declencheur']) ?></strong>
                                                <?php endif; ?>
                                                <?php if ($alerte['seuil'] !== null): ?>
                                                    &mdash;
                                                    Seuil : <?= htmlspecialchars((string) $alerte['seuil']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <div class="text-end">
                                            <?php if ($resolved): ?>
                                                <span class="badge text-bg-success">Résolue</span>
                                                <?php if ($alerte['resolved_at']): ?>
                                                    <p class="text-secondary small mb-0 mt-1">
                                                        le <?= htmlspecialchars($alerte['resolved_at']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge text-bg-danger">Active</span>
                                            <?php endif; ?>
                                            <p class="text-secondary small mb-0 mt-1">
                                                <?= htmlspecialchars($alerte['created_at']) ?>
                                            </p>
                                        </div>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
