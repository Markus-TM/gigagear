<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung laden
require_once("../config/dbaccess.php");

// Bestell-ID aus der GET-URL lesen
$order_id = $_GET['order_id'] ?? null;

// Prüfen, ob die Bestell-ID vorhanden ist
if (!$order_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Fehlende Bestell-ID']);
    exit;
}

try {
    // Verbindung zur Datenbank aufbauen
    $mysqli = new mysqli($host, $username, $password, $dbname);

    // Bestellung mit Kundendaten abfragen
    $stmtOrder = $mysqli->prepare("
        SELECT 
            o.id, 
            o.invoice_number, 
            o.order_date, 
            o.total_price,
            u.firstname, 
            u.lastname, 
            u.address, 
            u.zipcode, 
            u.city
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmtOrder->bind_param("i", $order_id); // Bindet Bestell-ID als Integer
    $stmtOrder->execute();
    $orderResult = $stmtOrder->get_result();
    $order = $orderResult->fetch_assoc(); // Einzelne Bestellung als assoziatives Array

    // Artikel zur Bestellung laden (inkl. Produktnamen)
    $stmtItems = $mysqli->prepare("
        SELECT 
            oi.quantity, 
            oi.unit_price, 
            p.name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmtItems->bind_param("i", $order_id);
    $stmtItems->execute();
    $itemsResult = $stmtItems->get_result();

    $items = [];
    while ($row = $itemsResult->fetch_assoc()) {
        $items[] = $row; // Alle Artikel in ein Array schreiben
    }

    // JSON-Antwort zurückgeben mit Bestellung und zugehörigen Artikeln
    echo json_encode([
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    // Im Fehlerfall: HTTP 500 und Fehlermeldung im JSON zurückgeben
    http_response_code(500);
    echo json_encode(['error' => 'Serverfehler: ' . $e->getMessage()]);
}
?>