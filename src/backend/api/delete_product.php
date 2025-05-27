<?php
// Rückgabeformat auf JSON setzen
header('Content-Type: application/json');

// Datenbankzugang laden
require_once("../config/dbaccess.php");

// Authentifizierungs-Token aus dem Header extrahieren
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Token aus dem Authorization-Header im Format "Bearer xyz..." extrahieren
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Wenn kein Token vorhanden ist → Anfrage abbrechen
if (!$token) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Token required']);
    exit;
}

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'] ?? null;

// Überprüfen, ob die Produkt-ID übergeben wurde
if (!$productId) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Produkt-ID fehlt']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL-Statement zum Löschen des Produkts vorbereiten
$stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $productId); // i = integer

// Versuch, das Produkt zu löschen
if ($stmt->execute()) {
    echo json_encode(['success' => true]); // Erfolgsmeldung
} else {
    http_response_code(500); // Serverfehler
    echo json_encode(['error' => 'Löschen fehlgeschlagen']);
}
?>