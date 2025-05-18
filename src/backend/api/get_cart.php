<?php
header('Content-Type: application/json');
require_once '../config/dbaccess.php';

// Fehlerausgabe unterdrücken, damit keine HTML-Fehler zurückgegeben werden
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_GET['session_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlender session_id Parameter']);
    exit;
}

$session_id = $_GET['session_id'];

try {
    // Warenkorb-Elemente mit Produktinformationen abrufen
    $stmt = $db_obj->prepare("
        SELECT c.id as cart_id, c.product_id, c.quantity,
               p.name, p.price, p.image_path
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    // Gesamtpreis berechnen
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    echo json_encode([
        'items' => $items,
        'total' => $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
