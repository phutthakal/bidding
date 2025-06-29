<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// รับค่าจากฟอร์ม
$item_id = $_POST['item_id'];
$bid_amount = $_POST['amount'];

// ดึงข้อมูลราคาปัจจุบันและราคาขั้นต่ำของรายการ
$stmt = $pdo->prepare("SELECT price, minimum_bid FROM items WHERE id = :item_id");
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าราคาที่เสนอสูงกว่าราคาเปิดและราคาขั้นต่ำหรือไม่
if ($bid_amount >= $item['price']) {
    echo "ราคาที่คุณเสนอจะต้องน้อยกว่าราคาเปิด (" . number_format($item['price'], 2) . " บาท)";
    exit;
}

if ($bid_amount < $item['minimum_bid']) {
    echo "ราคาที่คุณเสนอจะต้องมากกว่าราคาขั้นต่ำ (" . number_format($item['minimum_bid'], 2) . " บาท)";
    exit;
}

// อัพเดตราคาในฐานข้อมูล
$stmt = $pdo->prepare("UPDATE items SET update_price = :update_price, winner_id = :winner_id WHERE id = :item_id");
$stmt->bindParam(':update_price', $bid_amount);  // อัปเดตเป็นราคาที่เสนอ
$stmt->bindParam(':winner_id', $_SESSION['user']['id']);  // บันทึกผู้ชนะ
$stmt->bindParam(':item_id', $item_id);

if ($stmt->execute()) {
    echo "คุณได้เสนอราคาใหม่เรียบร้อยแล้ว!";
    header("Location: items.php");  // เปลี่ยนกลับไปที่หน้ารายการประมูล
    exit;
} else {
    echo "เกิดข้อผิดพลาดในการเสนอราคา";
}
?>
