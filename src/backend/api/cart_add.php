<?php
header('Content-Type: application/json');
require_once '../config/dbaccess.php';

// Aktiviere Fehlerprotokollierung für Debugging
ini_set('display_errors', 0); // Unterdrücke HTML-Fehler
error_reporting(E_ALL);

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id']) || !isset($data['session_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

$product_id = $data['product_id'];
$session_id = $data['session_id'];
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;

try {
    // Prüfen, ob das Produkt bereits im Warenkorb ist
    $stmt = $db_obj->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->bind_param("si", $session_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingItem = $result->fetch_assoc();
    
    if ($existingItem) {
        // Wenn ja, Menge erhöhen
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $db_obj->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $newQuantity, $existingItem['id']);
        $stmt->execute();
    } else {
        // Wenn nein, neuen Eintrag erstellen
        $stmt = $db_obj->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $session_id, $product_id, $quantity);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
