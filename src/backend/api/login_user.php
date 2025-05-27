<?php
// Gibt an, dass die API-Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung einbinden
require_once("../config/dbaccess.php");

// Verbindung zur Datenbank herstellen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Verbindung prüfen
if ($mysqli->connect_error) {
    http_response_code(500); // Interner Fehler
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Eingehende JSON-Daten aus dem Request-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

// Felder auslesen (mit Fallbacks)
$username = $data['username'] ?? null;
$password = $data['password'] ?? null;
$remember = $data['remember'] ?? false;
$user_id = $data['user_id'] ?? null; // optional, wird aber hier nicht genutzt

// Eingabevalidierung: Benutzername & Passwort müssen vorhanden sein
if (!$username || !$password) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

// Benutzer mit passendem Benutzernamen abrufen
$stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Falls kein Benutzer gefunden wurde
if ($result->num_rows === 0) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid username or password']);
    exit;
}

$user = $result->fetch_assoc();

// Passwort überprüfen (Hash-Vergleich)
if (!password_verify($password, $user['password_hash'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid username or password']);
    exit;
}

// Wenn Login erfolgreich → API-Token erzeugen
$token = bin2hex(random_bytes(32)); // sicherer zufälliger Token

// Token in der Datenbank für den Benutzer speichern
$stmtUpdate = $mysqli->prepare("UPDATE users SET api_token = ? WHERE id = ?");
$stmtUpdate->bind_param("si", $token, $user['id']);
$stmtUpdate->execute();

// Optional: „Angemeldet bleiben“ → Token als Cookie speichern
if ($remember) {
    setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true); // 30 Tage gültig
}

// Erfolgreiche Login-Antwort mit Benutzerinformationen und Token
echo json_encode([
    'message' => 'Login successful',
    'token' => $token,
    'username' => $user['username'],
    'role' => $user['role'],
    'user_id' => $user['id']
]);

// Ressourcen freigeben
$stmt->close();
$mysqli->close();
