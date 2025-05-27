<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankverbindung laden
require_once("../config/dbaccess.php");

// Authentifizierung über Bearer-Token aus dem Header
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Token extrahieren
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Kein Token vorhanden → Zugriff verweigern
if (!$token) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Token erforderlich']);
    exit;
}

// JSON-Daten aus dem Request-Body auslesen
$data = json_decode(file_get_contents('php://input'), true);

// Pflichtfelder auslesen
$id = $data['id'] ?? null;
$invoice_number = $data['invoice_number'] ?? ''; // Optional
$order_date = $data['order_date'] ?? '';         // Pflichtfeld

// Validierung der Eingabedaten
if (!$id || empty($order_date)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Pflichtfelder fehlen']);
    exit;
}

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// SQL-Update vorbereiten: Rechnung & Datum aktualisieren
$sql = "UPDATE orders SET invoice_number = ?, order_date = ? WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssi", $invoice_number, $order_date, $id);

// Update ausführen und Rückmeldung geben
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500); // Serverfehler
    echo json_encode(['error' => 'Fehler beim Speichern der Bestellung']);
}
