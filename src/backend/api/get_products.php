<?php
header('Content-Type: application/json');

require_once("../config/dbaccess.php");

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Basisabfrage
$sql = "SELECT id, name, description, price, rating, category, image_path
        FROM products
        WHERE is_active = 1";

// Suchparameter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $mysqli->real_escape_string($_GET['search']);
    $sql .= " AND (name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%')";
}

// Wenn eine Kategorie-ID übergeben wurde, filtere danach
if (isset($_GET['category_id']) && $_GET['category_id'] != 'all') {
    $categoryId = $mysqli->real_escape_string($_GET['category_id']);
    $sql .= " AND category = '$categoryId'";
}

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
?>
