<?php
header('Content-Type: application/json');
require_once '../config/dbaccess.php';

try {
    // Verwende mysqli statt PDO, da deine dbaccess.php mysqli verwendet
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = $db_obj->query($sql);
    
    if (!$result) {
        throw new Exception("Datenbankabfrage fehlgeschlagen");
    }
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode($categories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
