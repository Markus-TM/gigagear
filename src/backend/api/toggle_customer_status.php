<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// Token aus den HTTP-Headern auslesen
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Bearer-Token extrahieren (z. B. "Bearer abc123...")
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Falls kein Token vorhanden ist → Zugriff verweigert
if (!$token) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Token required']);
    exit;
}

// Eingehende JSON-Daten auslesen
$data = json_decode(file_get_contents('php://input'), true);

// Kunden-ID und neuer Aktivstatus auslesen
$customerId = $data['id'] ?? null;
$newStatus = $data['active'] ?? null;

// Prüfen, ob beide Werte gültig gesetzt sind
if (!isset($customerId) || !in_array($newStatus, [0, 1], true)) {
    http_response_code(400); // Ungültige Eingabe
    echo json_encode(['error' => 'Ungültige Parameter']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL: Aktivstatus des Benutzers ändern
$stmt = $mysqli->prepare("UPDATE users SET is_active = ? WHERE id = ?");
$stmt->bind_param("ii", $newStatus, $customerId);
$success = $stmt->execute();

// Ergebnis als JSON zurückgeben
echo json_encode(['success' => $success]);
