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

$mysqli = new mysqli($host, $username, $password, $dbname);
$sql = "SELECT id, firstname, lastname, email FROM users WHERE role = 'user'";
$result = $mysqli->query($sql);

if ($result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);
