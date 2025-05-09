<?php
header('Content-Type: application/json');
require_once("../config/dbaccess.php");

$data = json_decode(file_get_contents('php://input'), true);
$token = getallheaders()['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
  $token = $matches[1];
} else {
  http_response_code(401);
  echo json_encode(['error' => 'No token']);
  exit;
}

if (!$data['id'] || !$data['firstname'] || !$data['lastname'] || !$data['email']) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing parameters']);
  exit;
}

$mysqli = new mysqli($host, $username, $password, $dbname);
$adminCheck = $mysqli->prepare("SELECT role FROM users WHERE api_token = ?");
$adminCheck->bind_param("s", $token);
$adminCheck->execute();
$result = $adminCheck->get_result();
if ($result->num_rows === 0 || $result->fetch_assoc()['role'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['error' => 'Forbidden']);
  exit;
}

$stmt = $mysqli->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE id = ?");
$stmt->bind_param("sssi", $data['firstname'], $data['lastname'], $data['email'], $data['id']);
$stmt->execute();

echo json_encode(['success' => true]);
