<?php
// Antwort im JSON-Format
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

// Token aus dem Authorization-Header extrahieren
$token = getallheaders()['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
  $token = $matches[1];
} else {
  http_response_code(401); // Kein gültiger Token
  echo json_encode(['error' => 'No token']);
  exit;
}

// Prüfen, ob alle Pflichtfelder übergeben wurden
if (!$data['id'] || !$data['firstname'] || !$data['lastname'] || !$data['email']) {
  http_response_code(400); // Ungültige Anfrage
  echo json_encode(['error' => 'Missing parameters']);
  exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Prüfen, ob der übergebene Token zu einem Admin gehört
$adminCheck = $mysqli->prepare("SELECT role FROM users WHERE api_token = ?");
$adminCheck->bind_param("s", $token);
$adminCheck->execute();
$result = $adminCheck->get_result();

// Kein gültiger Benutzer gefunden oder Rolle ist nicht "admin"
if ($result->num_rows === 0 || $result->fetch_assoc()['role'] !== 'admin') {
  http_response_code(403); // Zugriff verweigert
  echo json_encode(['error' => 'Forbidden']);
  exit;
}

// Benutzerinformationen aktualisieren (Vorname, Nachname, E-Mail)
$stmt = $mysqli->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE id = ?");
$stmt->bind_param("sssi", $data['firstname'], $data['lastname'], $data['email'], $data['id']);
$stmt->execute();

// Erfolgreiche Antwort zurückgeben
echo json_encode(['success' => true]);
