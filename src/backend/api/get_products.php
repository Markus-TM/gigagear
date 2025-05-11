<?php
header('Content-Type: application/json');

require_once("../config/dbaccess.php");

// DB-Verbindung wie in deinem Beispiel
$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Aktive Produkte abfragen
$sql = "SELECT id, name, description, rating, category, image_path
        FROM products
        WHERE is_active = 1";

$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit;
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);

$result->free();
$mysqli->close();
