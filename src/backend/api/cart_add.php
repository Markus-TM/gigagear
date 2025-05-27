<?php
// Gibt an, dass die Rückgabe im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once '../config/dbaccess.php';

// Fehlerausgabe für Debugging (optional zur Fehleranalyse im Backend)
ini_set('display_errors', 0); // Keine HTML-Fehlerausgabe
error_reporting(E_ALL);

// Empfängt JSON-Daten aus dem HTTP-Request-Body
$data = json_decode(file_get_contents('php://input'), true);

// Validierung: Produkt-ID und Session-ID müssen vorhanden sein
if (!isset($data['product_id']) || !isset($data['session_id'])) {
    http_response_code(400); // Fehlerhafter Request
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

// Werte auslesen
$product_id = $data['product_id'];
$session_id = $data['session_id'];
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1; // Standardmenge = 1

try {
    // Prüfen, ob Produkt bereits im Warenkorb vorhanden ist
    $stmt = $db_obj->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->bind_param("si", $session_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingItem = $result->fetch_assoc();

    if ($existingItem) {
        // Wenn vorhanden: Menge erhöhen
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $db_obj->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $newQuantity, $existingItem['id']);
        $stmt->execute();
    } else {
        // Wenn nicht vorhanden: neuen Eintrag hinzufügen
        $stmt = $db_obj->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $session_id, $product_id, $quantity);
        $stmt->execute();
    }

    // Erfolgsmeldung im JSON-Format zurückgeben
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Bei Datenbankfehlern: Fehlerobjekt mit Fehlermeldung zurückgeben
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>