<?php
// Cet endpoint n'est plus utilisé depuis la simplification de salle-detail.php.
// Le système de seuils côté client (localStorage + fetch) a été supprimé.
// Les alertes sont désormais créées directement côté serveur (Arduino → BDD).

http_response_code(410);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Endpoint non utilisé.']);
