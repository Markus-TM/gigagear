<?php
// Antwort als JSON deklarieren
header('Content-Type: application/json');

// Datenbankzugang einbinden
require_once("../config/dbaccess.php");

// Authentifizierungs-Header auslesen
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Token aus dem Header extrahieren (Format: "Bearer xyz")
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Wenn kein Token vorhanden ist → Zugriff verweigern
if (!$token) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Token required']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL: Nur Benutzer mit der Rolle "user" (also keine Admins) abrufen
$sql = "SELECT id, firstname, lastname, email, is_active FROM users WHERE role = 'user'";
$result = $mysqli->query($sql);

// Wenn keine Benutzer vorhanden sind, leeres Array zurückgeben
if ($result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

// Ergebnisse als Array sammeln
$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

// Rückgabe der Benutzerdaten als JSON-Array
echo json_encode($customers);
