<?php
header('Content-Type: application/json');

require_once("../config/dbaccess.php");

$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Basic validation
$required = ['salutation', 'firstname', 'lastname', 'address', 'zipcode', 'city', 'email', 'username', 'password', 'passwordconf'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['error' => "$field is required"]);
        exit;
    }
}

// Check if passwords match
if ($data['password'] !== $data['passwordconf']) {
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

// Check if username/email already exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $data['username'], $data['email']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['error' => 'Username or email already exists']);
    exit;
}

// Hash password
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert into database
$stmt = $mysqli->prepare("INSERT INTO users 
    (salutation, firstname, lastname, address, zipcode, city, email, username, password_hash, role, is_active) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user', 1)");

$stmt->bind_param(
    "sssssssss", 
    $data['salutation'],
    $data['firstname'],
    $data['lastname'],
    $data['address'],
    $data['zipcode'],
    $data['city'],
    $data['email'],
    $data['username'],
    $hashedPassword
);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Registration successful!']);
} else {
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();