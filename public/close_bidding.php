<?php
require_once __DIR__ . '/../config/connect.php';

$item_id = $_GET['id'] ?? null;
if (!$item_id) exit(json_encode(['success' => false, 'message' => 'ไม่พบรายการ']));

// ดึงรายการนั้น
$stmt = $pdo->prepare("SELECT * FROM items WHERE id=? AND status='open'");
$stmt->execute([$item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) exit(json_encode(['success' => false, 'message' => 'รายการนี้ปิดแล้วหรือไม่พบ']));

if ($item['winner_id']) {
    // บันทึกผู้ชนะ
    $stmtInsert = $pdo->prepare("INSERT INTO winners (item_id, user_id, created_at) VALUES (?, ?, NOW())");
    $stmtInsert->execute([$item_id, $item['winner_id']]);
}

// ปิดสถานะ
$stmtUpdate = $pdo->prepare("UPDATE items SET status='closed' WHERE id=?");
$stmtUpdate->execute([$item_id]);

exit(json_encode(['success' => true, 'message' => '✅ ปิดประมูลเรียบร้อย']));
?>
