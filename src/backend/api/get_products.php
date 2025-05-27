<?php
// Rückgabeformat auf JSON setzen
header('Content-Type: application/json');

// Datenbankkonfiguration laden
require_once("../config/dbaccess.php");

// Neue mysqli-Verbindung erstellen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Verbindungsfehler prüfen
if ($mysqli->connect_error) {
    http_response_code(500); // Interner Serverfehler
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Grundabfrage: Nur aktive Produkte laden
$sql = "SELECT id, name, description, price, rating, category, image_path
        FROM products
        WHERE is_active = 1";

// Optionaler Suchfilter (per GET-Parameter ?search=...)
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $mysqli->real_escape_string($_GET['search']); // Schutz vor SQL-Injektion
    $sql .= " AND (name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%')";
}

// Optionaler Kategorie-Filter (per GET-Parameter ?category_id=...)
if (isset($_GET['category_id']) && $_GET['category_id'] != 'all') {
    $categoryId = $mysqli->real_escape_string($_GET['category_id']); // Sicherheit
    $sql .= " AND category = '$categoryId'";
}

// Query ausführen
$result = $mysqli->query($sql);

// Prüfen, ob das Query erfolgreich war
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit;
}

// Alle Produkte als Array sammeln
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Rückgabe im JSON-Format
echo json_encode($products);

// Ressourcen freigeben
$result->free();
$mysqli->close();
?>