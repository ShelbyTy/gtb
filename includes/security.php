<?php

// Fonction pour ouvrir la session sans la relancer deux fois
function ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Genere ou recupere le token CSRF garder dans la session
function get_csrf_token(): string
{
    ensure_session_started();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Verifie que le token recu correspond bien a celui de la session
function is_valid_csrf_token(?string $token): bool
{
    ensure_session_started();

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Sert a garder un message temporaire dans la session
function set_flash_message(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

// Recupere le message puis le supprime pour pas l'afficher plusieurs fois
function get_flash_message(): ?array
{
    ensure_session_started();

    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
