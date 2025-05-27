<?php
// Antwort als JSON deklarieren
header('Content-Type: application/json');

// Datenbankverbindung laden
require_once("../config/dbaccess.php");

// Fehleranzeige aktivieren (nur für Entwicklung – in Produktion entfernen)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Datenbankverbindung aufbauen
    $mysqli = new mysqli($host, $username, $password, $dbname);
    if ($mysqli->connect_errno) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }

    // Token aus dem Authorization-Header extrahieren
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Authorization token required", 400); // Bad Request
    }
    $token = $matches[1];

    // Rohdaten aus dem Body einlesen
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception("No input data received", 400);
    }

    // JSON-Daten dekodieren
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data", 400);
    }

    // Prüfen, ob alle erforderlichen Felder vorhanden sind
    $requiredFields = ['firstname', 'lastname', 'address', 'zipcode', 'city', 'password'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Field '$field' is required", 400);
        }
    }

    // Benutzer anhand des Tokens ermitteln
    $stmt = $mysqli->prepare("
        SELECT id, password_hash 
        FROM users 
        WHERE api_token = ? 
        AND is_active = 1
    ");
    if (!$stmt) {
        throw new Exception("Database error: " . $mysqli->error, 500);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired token", 401); // Unauthorized
    }

    $user = $result->fetch_assoc();

    // Passwort prüfen
    if (!password_verify($data['password'], $user['password_hash'])) {
        throw new Exception("Incorrect password", 403); // Forbidden
    }

    // Benutzerinformationen aktualisieren
    $update = $mysqli->prepare("
        UPDATE users 
        SET firstname = ?, lastname = ?, address = ?, zipcode = ?, city = ? 
        WHERE id = ?
    ");
    $update->bind_param(
        "sssssi",
        $data['firstname'],
        $data['lastname'],
        $data['address'],
        $data['zipcode'],
        $data['city'],
        $user['id']
    );

    // Update ausführen und prüfen
    if (!$update->execute()) {
        throw new Exception("Failed to update user data: " . $update->error, 500);
    }

    // Erfolgreiche Antwort
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);

} catch (Exception $e) {
    // Fehlerbehandlung mit passendem HTTP-Code
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Verbindung schließen, falls vorhanden
    if (isset($mysqli))
        $mysqli->close();
}
