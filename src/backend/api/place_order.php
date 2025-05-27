<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// Fehlerausgabe unterdrücken (kein HTML im JSON-Output)
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // Eingehende JSON-Daten auslesen
    $data = json_decode(file_get_contents('php://input'), true);
    $session_id = $data['session_id'] ?? '';
    $user_id = $data['user_id'] ?? 0;

    // Prüfung: Session und Benutzer müssen vorhanden sein
    if (empty($session_id) || !$user_id) {
        http_response_code(400); // Ungültiger Request
        echo json_encode(['error' => 'Bitte melden Sie sich an, um zu bestellen']);
        exit;
    }

    // Warenkorb mit Produktdetails laden
    $stmt = $db_obj->prepare("
        SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $total_price = 0;

    // Alle Positionen durchgehen und Gesamtpreis berechnen
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total_price += $row['price'] * $row['quantity'];
    }

    // Abbruch, wenn Warenkorb leer ist
    if (count($items) === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Warenkorb ist leer']);
        exit;
    }

    // Bestelldatum & zufällige Rechnungsnummer erstellen
    $order_date = date("Y-m-d H:i:s");
    $invoice_number = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);

    // Bestellung in DB speichern
    $stmt = $db_obj->prepare("
        INSERT INTO orders (user_id, order_date, total_price, invoice_number)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isds", $user_id, $order_date, $total_price, $invoice_number);
    $stmt->execute();
    $order_id = $stmt->insert_id; // ID der neu erstellten Bestellung

    // Alle Artikel einzeln in order_items speichern
    foreach ($items as $item) {
        $stmt = $db_obj->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Nach der Bestellung: Warenkorb leeren
    $stmt = $db_obj->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    // Erfolgreiche Antwort mit Bestellnummer und Rechnungsnummer
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'invoice_number' => $invoice_number
    ]);
} catch (Exception $e) {
    // Bei Fehlern: Serverfehler zurückgeben
    http_response_code(500);
    echo json_encode(['error' => 'Serverfehler: ' . $e->getMessage()]);
}
?>