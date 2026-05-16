<?php
// Page utiliser pour deconnecter proprement l'utilisateur
require_once __DIR__ . '/includes/security.php';

// On démarre la session pour pouvoir la vider
ensure_session_started();

// La déconnexion doit arriver en POST avec le bon token sinon retour dashboard
// ca empeche quelqu'un de deconnecter un autre utilisateur juste en lui envoyant un lien
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    header('Location: dashboard.php');
    exit();
}

// On vide toute les infos de session
$_SESSION = [];

// Si la session utilise un cookie, on le supprime aussi
// On force httponly=true et secure selon le protocole actuel, sans dépendre de php.ini
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        isset($_SERVER['HTTPS']), // true uniquement en HTTPS, indépendant de php.ini
        true                      // httponly forcé : protège contre le vol de cookie par XSS
    );
}

// Fin de la session puis retour sur la page de connexion
session_destroy();

// Nouvelle petite session pour afficher le message après la déconnexion
session_start();
set_flash_message('success', 'Déconnexion réussie.');

header('Location: login.php');
exit();
