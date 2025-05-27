<?php
// Setzt den Response-Type auf JSON
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once '../config/dbaccess.php';

// HTML-Fehlerausgabe deaktivieren, aber Logging aktiv lassen
ini_set('display_errors', 0);
error_reporting(E_ALL);

// JSON-Daten aus dem POST-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

// Prüfen, ob alle nötigen Parameter vorhanden sind
if (!isset($data['cart_id']) || !isset($data['quantity']) || !isset($data['session_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

$cart_id = $data['cart_id']; // ID des Warenkorb-Eintrags
$quantity = intval($data['quantity']); // Neue Menge
$session_id = $data['session_id']; // Prüfen, ob die Session-ID zum Eintrag gehört

// Prüfen, ob die Menge gültig ist (mindestens 1)
if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Menge']);
    exit;
}

try {
    // Sicherheitsprüfung: Eintrag muss zur aktuellen Session-ID gehören
    $stmt = $db_obj->prepare("SELECT id FROM cart WHERE id = ? AND session_id = ?");
    $stmt->bind_param("is", $cart_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Wenn kein passender Eintrag gefunden wurde → Zugriff verweigert
    if ($result->num_rows === 0) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'error' => 'Zugriff verweigert']);
        exit;
    }

    // Menge aktualisieren
    $stmt = $db_obj->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();

    // Erfolgreiche Rückmeldung
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Fehler bei Datenbankvorgängen
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>