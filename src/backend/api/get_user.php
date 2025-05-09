<?php
header('Content-Type: application/json');

require_once("../config/dbaccess.php");

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Bearbeite das Token (z.B. aus dem Authorization-Header ODER dem JSON-Body)
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(400);
    echo json_encode(['error' => 'Token required']);
    exit;
}

$stmt = $mysqli->prepare("
    SELECT username, role, email, address, zipcode, city, firstname, lastname
    FROM users
    WHERE api_token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$user = $result->fetch_assoc();
echo json_encode([
    'username' => $user['username'],
    'role' => $user['role'],
    'email' => $user['email'],
    'address' => $user['address'],
    'zipcode' => $user['zipcode'],
    'city' => $user['city'],
    'firstname' => $user['firstname'],
    'lastname' => $user['lastname']
]);

$stmt->close();
$mysqli->close();
