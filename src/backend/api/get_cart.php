<?php
// Antwortformat auf JSON setzen
header('Content-Type: application/json');

// Verbindung zur Datenbank herstellen
require_once '../config/dbaccess.php';

// Fehlerausgabe unterdrücken (HTML-Fehler nicht im JSON zurückgeben)
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
    // SQL: Warenkorb-Einträge samt Produktinformationen laden
    $stmt = $db_obj->prepare("
        SELECT 
            c.id as cart_id,         -- eindeutige ID im Warenkorb
            c.product_id,            -- Produkt-ID
            c.quantity,              -- Menge
            p.name,                  -- Produktname
            p.price,                 -- Einzelpreis
            p.image_path             -- Bildpfad
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ");
    $stmt->bind_param("s", $session_id); // session_id als Parameter binden
    $stmt->execute();

    $result = $stmt->get_result();
    $items = [];

    // Alle gefundenen Warenkorbpositionen als Array speichern
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    // Gesamtpreis berechnen (Preis × Menge pro Artikel)
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // JSON-Antwort mit Warenkorbartikeln und Gesamtpreis
    echo json_encode([
        'items' => $items,
        'total' => $total
    ]);

} catch (Exception $e) {
    // Fehlerfall: Datenbankfehler an den Client zurückgeben
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>