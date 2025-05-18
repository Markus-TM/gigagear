<?php
header('Content-Type: application/json');
require_once '../config/dbaccess.php';

// Aktiviere Fehlerprotokollierung für Debugging
ini_set('display_errors', 0); // Unterdrücke HTML-Fehler
error_reporting(E_ALL);

if (!isset($_GET['session_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlender session_id Parameter']);
    exit;
}

$session_id = $_GET['session_id'];

try {
    // Gesamtanzahl der Produkte im Warenkorb abrufen
    $stmt = $db_obj->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $count = $row['count'] ? intval($row['count']) : 0;
    
    echo json_encode(['count' => $count]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
