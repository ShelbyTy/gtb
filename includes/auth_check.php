<?php

// Demare la session si elle n'est pas deja ouverte
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si aucun utilisateur est en session, il faut retourner a la connexion
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
