<?php
// Setzt den Content-Type auf JSON für die API-Antwort
header('Content-Type: application/json');

// Datenbankverbindung laden (verwendet mysqli in dbaccess.php)
require_once '../config/dbaccess.php';

try {
    // SQL-Abfrage: Alle Kategorien alphabetisch nach Namen sortieren
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = $db_obj->query($sql); // Direktes Query, kein Prepared Statement nötig

    // Fehlerprüfung für das Query
    if (!$result) {
        throw new Exception("Datenbankabfrage fehlgeschlagen");
    }

    $categories = [];

    // Alle Kategorien als assoziatives Array sammeln
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    // Erfolgreiche Antwort: Alle Kategorien im JSON-Array zurückgeben
    echo json_encode($categories);

} catch (Exception $e) {
    // Fehlerausgabe im JSON-Format
    http_response_code(500); // Interner Serverfehler
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>