<?php
// Datenbankverbindung einbinden
require_once '../config/dbaccess.php';

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents("php://input"), true);

// Bestellpositions-ID auslesen und in Integer umwandeln
$orderItemId = intval($data['order_item_id'] ?? 0);

// Wenn eine gültige ID übergeben wurde
if ($orderItemId > 0) {
    // Eintrag aus der Tabelle order_items löschen
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $success = $stmt->execute([$orderItemId]);

    // Ergebnis zurückgeben
    echo json_encode(['success' => $success]);
} else {
    // Fehlerhafte oder fehlende ID
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
}
