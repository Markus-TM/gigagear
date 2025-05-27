<?php
// Gibt an, dass die Antwort im JSON-Format erfolgt
header('Content-Type: application/json');

// Datenbankzugang laden
require_once("../config/dbaccess.php");

// Token aus dem Authorization-Header extrahieren
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

// Bearer-Token extrahieren (Format: "Bearer abc123...")
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Kein Token → Zugriff verweigern
if (!$token) {
    http_response_code(401); // Nicht autorisiert
    echo json_encode(['error' => 'Token required']);
    exit;
}

// Benutzer-ID aus dem GET-Parameter lesen
$user_id = $_GET['user_id'];

// Verbindung zur Datenbank aufbauen
$mysqli = new mysqli($host, $username, $password, $dbname);

// Alle Bestellungen des angegebenen Nutzers abfragen
$sql = "SELECT id, invoice_number, order_date, total_price FROM orders WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id); // Benutzer-ID als Integer binden
$stmt->execute();
$result = $stmt->get_result();

// Falls keine Bestellungen vorhanden → leeres Array zurückgeben
if ($result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

// Ergebnisse in Array speichern
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Rückgabe der Bestelldaten im JSON-Format
echo json_encode($orders);
