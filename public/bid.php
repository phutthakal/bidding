<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

header('Content-Type: application/json'); // ✅ ตอบ JSON เสมอ

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '❌ คุณต้องเข้าสู่ระบบก่อน']);
    exit;
}

$item_id = $_POST['item_id'] ?? null;
$bid_amount = $_POST['amount'] ?? null;

// ตรวจสอบข้อมูล
if (!$item_id || !$bid_amount) {
    echo json_encode(['success' => false, 'message' => '❌ ข้อมูลไม่ครบถ้วน']);
    exit;
}

// ดึงราคาเปิดและราคาปัจจุบัน
$stmt = $pdo->prepare("SELECT price, update_price FROM items WHERE id = :item_id");
$stmt->execute(['item_id' => $item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['success' => false, 'message' => '❌ ไม่พบรายการสินค้านี้']);
    exit;
}

$current_price = ($item['update_price'] > 0) ? $item['update_price'] : $item['price'];

if ($bid_amount >= $current_price) {
    echo json_encode(['success' => false, 'message' => '❌ ราคาที่คุณเสนอจะต้องต่ำกว่าราคาปัจจุบัน (' . number_format($current_price, 2) . ' บาท)']);
    exit;
}

// อัพเดตราคาใน DB
$stmt = $pdo->prepare("UPDATE items SET update_price = :bid_amount, winner_id = :winner_id WHERE id = :item_id");
$stmt->execute([
    'bid_amount' => $bid_amount,
    'winner_id' => $_SESSION['user']['id'],
    'item_id' => $item_id
]);

echo json_encode(['success' => true, 'message' => '✅ เสนอราคาสำเร็จ! ราคาปัจจุบัน: ' . number_format($bid_amount, 2) . ' บาท']);
exit;
?>
