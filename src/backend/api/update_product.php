<?php
// Gibt an, dass die API-Antwort als JSON erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// Token aus den HTTP-Headern extrahieren
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Bearer-Token extrahieren
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Kein gültiger Token vorhanden → Zugriff verweigern
if (!$token) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Token required']);
    exit;
}

// JSON-Daten aus dem Request-Body einlesen
$data = json_decode(file_get_contents('php://input'), true);

// Felder auslesen
$id = $data['id'] ?? null;
$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0.0;
$category = $data['category'] ?? '';
$image_path = $data['image_path'] ?? '';

// Eingabevalidierung: Alle Pflichtfelder müssen gefüllt sein
if (
    !$id ||
    empty($name) ||
    empty($description) ||
    $price <= 0 ||
    empty($category) ||
    empty($image_path)
) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Alle Felder müssen ausgefüllt sein']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL-Update vorbereiten: Produktdaten aktualisieren
$sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssdssi", $name, $description, $price, $category, $image_path, $id);

// Update ausführen und Rückmeldung geben
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500); // Serverfehler
    echo json_encode(['error' => 'Fehler beim Aktualisieren des Produkts']);
}
