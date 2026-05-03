<?php
// Page utiliser pour deconnecter proprement l'utilisateur
require_once __DIR__ . '/includes/security.php';

// On démarre la session pour pouvoir la vider
ensure_session_started();

// La déconnexion doit arriver en POST avec le bon token sinon retour dashboard
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    header('Location: dashboard.php');
    exit();
}

// On vide toute les infos de session
$_SESSION = [];

// Si la session utilise un cookie, on le supprime aussi
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Fin de la session puis retour sur la page de connexion
session_destroy();

// Nouvelle petite session pour afficher le message après la déconnexion
session_start();
set_flash_message('success', 'Déconnexion réussie.');

header('Location: login.php');
exit();
