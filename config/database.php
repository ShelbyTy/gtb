<?php

try {
    $conn = new PDO('mysql:host=localhost;dbname=gtb;charset=utf8mb4', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
