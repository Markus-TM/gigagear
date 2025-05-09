<?php
header('Content-Type: application/json');
require_once("../config/dbaccess.php");

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $mysqli = new mysqli($host, $username, $password, $dbname);
    if ($mysqli->connect_errno) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }

    // Get authorization header
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Authorization token required", 400);
    }
    $token = $matches[1];

    // Get and validate input
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception("No input data received", 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data", 400);
    }

    // Required fields - now matches your database columns
    $requiredFields = ['firstname', 'lastname', 'address', 'zipcode', 'city', 'password'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Field '$field' is required", 400);
        }
    }

    // Get user by token - using password_hash column
    $stmt = $mysqli->prepare("
        SELECT id, password_hash 
        FROM users 
        WHERE api_token = ? 
        AND is_active = 1
    ");
    if (!$stmt) {
        throw new Exception("Database error: " . $mysqli->error, 500);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired token", 401);
    }

    $user = $result->fetch_assoc();

    // Verify password against password_hash column
    if (!password_verify($data['password'], $user['password_hash'])) {
        throw new Exception("Incorrect password", 403);
    }

    // Update user data
    $update = $mysqli->prepare("
        UPDATE users 
        SET firstname = ?, lastname = ?, address = ?, zipcode = ?, city = ? 
        WHERE id = ?
    ");
    $update->bind_param(
        "sssssi",
        $data['firstname'],
        $data['lastname'],
        $data['address'],
        $data['zipcode'],
        $data['city'],
        $user['id']
    );

    if (!$update->execute()) {
        throw new Exception("Failed to update user data: " . $update->error, 500);
    }

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($mysqli)) $mysqli->close();
}