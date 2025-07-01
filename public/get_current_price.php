<?php
require_once __DIR__ . '/../config/connect.php';

header('Content-Type: application/json');

$item_id = $_GET['item_id'] ?? null;
if (!$item_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing item_id']);
    exit;
}

$stmt = $pdo->prepare("SELECT price, update_price FROM items WHERE id = :item_id");
$stmt->execute(['item_id' => $item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $current_price = ($item['update_price'] > 0) ? $item['update_price'] : $item['price'];
    echo json_encode(['current_price' => $current_price]);
} else {
    echo json_encode(['error' => 'Item not found']);
}
