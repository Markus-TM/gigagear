<?php
header('Content-Type: application/json');

require_once("../config/dbaccess.php");

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? null;
$password = $data['password'] ?? null;
$remember = $data['remember'] ?? false;

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

$stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid username or password']);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid username or password']);
    exit;
}

//Token erstellen
$token = bin2hex(random_bytes(32));

// Token + Zeit speichern
$stmtUpdate = $mysqli->prepare("UPDATE users SET api_token = ? WHERE id = ?");
$stmtUpdate->bind_param("si", $token, $user['id']);
$stmtUpdate->execute();

//Wenn "Login merken", Cookie setzen
if ($remember) {
    setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true);
}

//User-Daten & Token zurückgeben
echo json_encode([
    'message' => 'Login successful',
    'token' => $token,
    'username' => $user['username'],
    'role' => $user['role']
]);

$stmt->close();
$mysqli->close();
