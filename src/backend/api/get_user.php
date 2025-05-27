<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Fehlerprüfung bei Verbindungsaufbau
if ($mysqli->connect_error) {
    http_response_code(500); // Interner Serverfehler
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Authorization-Header auslesen
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Token aus dem Header extrahieren (Format: "Bearer <token>")
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Wenn kein Token vorhanden ist, Anfrage ablehnen
if (!$token) {
    http_response_code(400); // Ungültige Anfrage
    echo json_encode(['error' => 'Token required']);
    exit;
}

// Benutzer anhand des Tokens aus der Datenbank abfragen
$stmt = $mysqli->prepare("
    SELECT 
        username, 
        role, 
        email, 
        address, 
        zipcode, 
        city, 
        firstname, 
        lastname
    FROM users
    WHERE api_token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

// Falls kein Benutzer mit diesem Token existiert
if ($result->num_rows === 0) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

// Benutzerinformationen abrufen
$user = $result->fetch_assoc();

// Benutzerinformationen als JSON zurückgeben
echo json_encode([
    'username' => $user['username'],
    'role' => $user['role'],
    'email' => $user['email'],
    'address' => $user['address'],
    'zipcode' => $user['zipcode'],
    'city' => $user['city'],
    'firstname' => $user['firstname'],
    'lastname' => $user['lastname']
]);

// Ressourcen aufräumen
$stmt->close();
$mysqli->close();
