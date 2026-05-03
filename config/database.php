<?php

try {
    // Infos de connexion, avec des valeurs par defaut pour le poste en local
    $dbHost = getenv('GTB_DB_HOST') ?: 'localhost';
    $dbName = getenv('GTB_DB_NAME') ?: 'gtb';
    $dbUser = getenv('GTB_DB_USER') ?: 'root';
    $dbPass = getenv('GTB_DB_PASS') ?: 'root';

    // Creation de la connexion PDO vers MySQL
    $conn = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass
    );
    // Mode erreur en exception, c'est plus simple pour le try catch
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si la base ne repond pas on stop la page
    die('Erreur de connexion a la base de donnees.');
}
