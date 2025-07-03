<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

if (!isset($_SESSION['user'])) {
  die("❌ ต้องเข้าสู่ระบบก่อน");
}

$room = $_GET['room'] ?? '';
if (!$room) die('❌ ไม่พบห้องประมูล');

$stmt = $pdo->prepare("
  SELECT * FROM items
  WHERE auction_room = :room
    AND NOW() BETWEEN bidding_start AND bidding_end
    AND status='open'
");
$stmt->execute(['room' => $room]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ห้องประมูล: <?= htmlspecialchars($room) ?></title>
  <link rel="stylesheet" href="css/items.css">
</head>
<body>
  <h1>🛒 ห้องประมูล <?= htmlspecialchars($room) ?></h1>
  <?php if (!$items): ?>
    <p>❌ ยังไม่มีรายการประมูลในห้องนี้ หรือหมดเวลาแล้ว</p>
  <?php else: ?>
    <div class="items-grid">
      <?php foreach ($items as $item): ?>
        <div class="item-card">
          <h3><?= htmlspecialchars($item['title']) ?></h3>
          <?php if (!empty($item['image_url'])): ?>
            <img src="<?= htmlspecialchars(explode(',', $item['image_url'])[0]) ?>" class="item-image">
          <?php endif; ?>
          <p class="item-price">ราคา: <?= number_format($item['price']) ?> บาท</p>
          <a href="detail.php?id=<?= $item['id'] ?>" class="item-link">ดูรายละเอียด</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</body>
</html>
