<?php
header('Content-Type: application/json');
require_once("../config/dbaccess.php");

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Token required']);
    exit;
}

// Empfange Produktdaten aus dem Request-Body
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0.0;
$category = $data['category'] ?? '';
$image_path = $data['image_path'] ?? '';

// Überprüfe, ob alle erforderlichen Daten vorhanden sind
if (empty($name) || empty($description) || $price <= 0 || empty($category) || empty($image_path)) {
    http_response_code(400);
    echo json_encode(['error' => 'Alle Felder müssen ausgefüllt sein']);
    exit;
}

// Daten in die Datenbank einfügen
$mysqli = new mysqli($host, $username, $password, $dbname);

$sql = "INSERT INTO products (name, description, price, category, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$is_active = 1; //Produkt ist automatisch aktiv, wenn hinzugefügt 
$stmt->bind_param("ssdsss", $name, $description, $price, $category, $image_path, $is_active);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Fehler beim Hinzufügen des Produkts']);
}