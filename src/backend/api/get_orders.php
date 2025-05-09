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

$user_id = $_GET['user_id'];
$mysqli = new mysqli($host, $username, $password, $dbname);
$sql = "SELECT id, date FROM orders WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
