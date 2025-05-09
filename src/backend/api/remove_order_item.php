<?php
require_once '../config/dbaccess.php';

$data = json_decode(file_get_contents("php://input"), true);
$orderItemId = intval($data['order_item_id'] ?? 0);

if ($orderItemId > 0) {
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $success = $stmt->execute([$orderItemId]);
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
}