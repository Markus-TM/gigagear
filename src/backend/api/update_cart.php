<?php
header('Content-Type: application/json');
require_once '../config/dbaccess.php';

// Fehlerausgabe unterdrücken, damit keine HTML-Fehler zurückgegeben werden
ini_set('display_errors', 0);
error_reporting(E_ALL);

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart_id']) || !isset($data['quantity']) || !isset($data['session_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

$cart_id = $data['cart_id'];
$quantity = intval($data['quantity']);
$session_id = $data['session_id'];

if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Menge']);
    exit;
}

try {
    // Sicherheitscheck: Prüfen, ob der Warenkorb-Eintrag zum Session-ID gehört
    $stmt = $db_obj->prepare("SELECT id FROM cart WHERE id = ? AND session_id = ?");
    $stmt->bind_param("is", $cart_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Zugriff verweigert']);
        exit;
    }
    
    // Menge aktualisieren
    $stmt = $db_obj->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
