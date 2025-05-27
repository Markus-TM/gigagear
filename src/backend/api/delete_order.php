<?php
// Antwortformat auf JSON setzen
header('Content-Type: application/json');

// Datenbank-Zugangsdaten einbinden
require_once("../config/dbaccess.php");

// Token aus dem Header extrahieren
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Token prüfen (Bearer-Format erwartet)
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Wenn kein gültiger Token vorhanden ist → Zugriff verweigern
if (!$token) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Token erforderlich']);
    exit;
}

// Bestellung-ID aus dem JSON-Body lesen
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['id'] ?? null;

// Prüfen, ob eine ID übergeben wurde
if (!$orderId) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Bestellungs-ID fehlt']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL-Befehl vorbereiten zum Löschen der Bestellung
$stmt = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);

// Ausführen des Löschbefehls
if ($stmt->execute()) {
    echo json_encode(['success' => true]); // Erfolgreich gelöscht
} else {
    http_response_code(500); // Serverfehler
    echo json_encode(['error' => 'Löschen fehlgeschlagen']);
}
?>