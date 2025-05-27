<?php
// Antwortformat auf JSON setzen
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once '../config/dbaccess.php';

// Fehlerausgabe unterdrücken (für saubere JSON-Antworten ohne HTML)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

// Prüfen, ob cart_id und session_id übergeben wurden
if (!isset($data['cart_id']) || !isset($data['session_id'])) {
    http_response_code(400); // Ungültiger Request
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

$cart_id = $data['cart_id'];
$session_id = $data['session_id'];

try {
    // Sicherheitsprüfung: Gehört dieser cart-Eintrag zur angegebenen Session?
    $stmt = $db_obj->prepare("SELECT id FROM cart WHERE id = ? AND session_id = ?");
    $stmt->bind_param("is", $cart_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Wenn kein passender Eintrag gefunden → Zugriff verweigert
    if ($result->num_rows === 0) {
        http_response_code(403); // Verboten
        echo json_encode(['success' => false, 'error' => 'Zugriff verweigert']);
        exit;
    }

    // Wenn alles passt: Eintrag aus dem Warenkorb löschen
    $stmt = $db_obj->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Fehler bei der Datenbankverbindung oder Abfrage
    http_response_code(500); // Serverfehler
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>