<?php
// Antworttyp auf JSON setzen
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once '../config/dbaccess.php';

// Fehlerausgabe aktivieren (aber HTML-Ausgabe unterdrücken)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Prüfen, ob die session_id übergeben wurde
if (!isset($_GET['session_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Fehlender session_id Parameter']);
    exit;
}

$session_id = $_GET['session_id'];

try {
    // SQL: Summe aller Mengen für die übergebene Session-ID berechnen
    $stmt = $db_obj->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Falls nichts gefunden wurde, auf 0 setzen
    $count = $row['count'] ? intval($row['count']) : 0;

    // Rückgabe der Artikelanzahl als JSON
    echo json_encode(['count' => $count]);
} catch (Exception $e) {
    // Fehlerbehandlung bei Problemen mit der Datenbank
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>