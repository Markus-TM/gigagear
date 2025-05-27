<?php
// Setzt den Rückgabetyp auf JSON
header('Content-Type: application/json');

// Verbindungsdaten zur Datenbank einbinden
require_once("../config/dbaccess.php");

// Token aus dem Header extrahieren (für Authentifizierung)
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Prüfen, ob ein Bearer-Token vorhanden ist
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Falls kein Token übergeben wurde → Fehler zurückgeben
if (!$token) {
    http_response_code(401); // HTTP 401 Unauthorized
    echo json_encode(['error' => 'Token required']);
    exit;
}

// JSON-Daten aus dem Request-Body auslesen
$data = json_decode(file_get_contents('php://input'), true);

// Einzelne Felder auslesen oder mit Fallback initialisieren
$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0.0;
$category = $data['category'] ?? '';
$image_path = $data['image_path'] ?? '';

// Prüfen, ob alle Pflichtfelder ausgefüllt sind
if (empty($name) || empty($description) || $price <= 0 || empty($category) || empty($image_path)) {
    http_response_code(400); // HTTP 400 Bad Request
    echo json_encode(['error' => 'Alle Felder müssen ausgefüllt sein']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL-Statement zum Einfügen eines neuen Produkts vorbereiten
$sql = "INSERT INTO products (name, description, price, category, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$is_active = 1; // Neue Produkte sind standardmäßig aktiv

// Parameter binden (Typen: s = string, d = double)
$stmt->bind_param("ssdsss", $name, $description, $price, $category, $image_path, $is_active);

// Ausführen des SQL-Statements und Erfolg prüfen
if ($stmt->execute()) {
    echo json_encode(['success' => true]); // Erfolgreiche Antwort
} else {
    http_response_code(500); // HTTP 500 Internal Server Error
    echo json_encode(['error' => 'Fehler beim Hinzufügen des Produkts']);
}
