<?php
// Gibt an, dass die API-Antwort als JSON erfolgt
header('Content-Type: application/json');

// Datenbankkonfiguration laden
require_once("../config/dbaccess.php");

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Prüfen, ob die Verbindung erfolgreich war
if ($mysqli->connect_error) {
    http_response_code(500); // Interner Serverfehler
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// JSON-Daten aus dem Request-Body auslesen
$data = json_decode(file_get_contents('php://input'), true);

// Pflichtfelder definieren und prüfen, ob alle vorhanden sind
$required = ['salutation', 'firstname', 'lastname', 'address', 'zipcode', 'city', 'email', 'username', 'password', 'passwordconf'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['error' => "$field is required"]);
        exit;
    }
}

// Passwort-Bestätigung prüfen
if ($data['password'] !== $data['passwordconf']) {
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

// Prüfen, ob Benutzername oder E-Mail bereits existieren
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $data['username'], $data['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['error' => 'Username or email already exists']);
    exit;
}

// Passwort sicher hashen
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Neuen Benutzer in die Datenbank einfügen (Rolle: user, Status: aktiv)
$stmt = $mysqli->prepare("INSERT INTO users 
    (salutation, firstname, lastname, address, zipcode, city, email, username, password_hash, role, is_active) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user', 1)");

$stmt->bind_param(
    "sssssssss",
    $data['salutation'],
    $data['firstname'],
    $data['lastname'],
    $data['address'],
    $data['zipcode'],
    $data['city'],
    $data['email'],
    $data['username'],
    $hashedPassword
);

// Erfolgreiche Registrierung → Erfolgsmeldung
if ($stmt->execute()) {
    echo json_encode(['message' => 'Registration successful!']);
} else {
    // Fehler bei der Registrierung → Datenbankfehler zurückgeben
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

// Ressourcen freigeben
$stmt->close();
$mysqli->close();
