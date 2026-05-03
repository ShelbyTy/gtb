<?php
// Valeurs par défaut au cas où une page ne donne pas de titre ou de classe body
$pageTitle = $pageTitle ?? 'GTB';
$bodyClass = $bodyClass ?? '';

// Je récupère un message flash si une autre page en a mis un avant la redirection
$toastMessage = function_exists('get_flash_message') ? get_flash_message() : null;

// Si la page a une erreur directe, on l'affiche aussi en notification
if (!$toastMessage && !empty($erreur)) {
    $toastMessage = [
        'type' => 'danger',
        'message' => $erreur,
    ];
}

// Classe Bootstrap selon si c'est bon ou si c'est une erreur
$toastType = $toastMessage['type'] ?? 'success';
$toastClass = $toastType === 'success' ? 'text-bg-success' : 'text-bg-danger';
$toastTitle = $toastType === 'success' ? 'Réussite' : 'Erreur';
$globalCssVersion = filemtime(__DIR__ . '/../assets/css/global.css');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Entete commun a toute les pages -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css?v=<?= $globalCssVersion ?>">
</head>

<body class="<?= htmlspecialchars($bodyClass) ?>">

    <?php if ($toastMessage): ?>
        <!-- Notification Bootstrap en haut à droite -->
        <div class="toast-container position-fixed end-0 p-3" style="top: 5rem;">
            <div class="toast <?= htmlspecialchars($toastClass) ?> border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
                <div class="toast-header">
                    <strong class="me-auto"><?= htmlspecialchars($toastTitle) ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fermer"></button>
                </div>
                <div class="toast-body">
                    <?= htmlspecialchars($toastMessage['message']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
