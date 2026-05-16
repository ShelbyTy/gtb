<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

ensure_session_started();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Non authentifié']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);

$idCapteur  = isset($body['id_capteur'])  ? (int)   $body['id_capteur']  : 0;
$valeur     = isset($body['valeur'])      ? (float)  $body['valeur']     : null;
$seuil      = isset($body['seuil'])       ? (float)  $body['seuil']      : null;
$typeMesure = isset($body['type_mesure']) ? trim((string) $body['type_mesure']) : '';

if (!$idCapteur || $valeur === null || $seuil === null || $typeMesure === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Paramètres manquants']);
    exit;
}

try {
    // Anti-spam : pas de doublon si une alerte non résolue existe déjà pour ce capteur + type
    $check = $conn->prepare("
        SELECT COUNT(*) FROM alertes
        WHERE id_capteur  = :id_capteur
          AND type_alerte = :type_alerte
          AND is_resolved = 0
    ");
    $typeAlerte = $typeMesure . ' — seuil dépassé';
    $check->execute([
        ':id_capteur'  => $idCapteur,
        ':type_alerte' => $typeAlerte,
    ]);

    if ((int) $check->fetchColumn() > 0) {
        echo json_encode(['ok' => true, 'skipped' => true]);
        exit;
    }

    $message = sprintf('%s : %.2f (seuil configuré : %.2f)', $typeMesure, $valeur, $seuil);
    // si la valeur depasse 1.5 fois le seuil c'est critique, sinon c'est juste un warning
    // le 1.5 c'est moi qui l'ai choisi, ca peut etre ajusté selon les besoins
    $niveau  = ($valeur >= $seuil * 1.5) ? 'critical' : 'warning';

    $insert = $conn->prepare("
        INSERT INTO alertes (id_capteur, type_alerte, message, valeur_declencheur, seuil, niveau)
        VALUES (:id_capteur, :type_alerte, :message, :valeur, :seuil, :niveau)
    ");
    $insert->execute([
        ':id_capteur'  => $idCapteur,
        ':type_alerte' => $typeAlerte,
        ':message'     => $message,
        ':valeur'      => $valeur,
        ':seuil'       => $seuil,
        ':niveau'      => $niveau,
    ]);

    echo json_encode(['ok' => true, 'skipped' => false]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur base de données']);
}
