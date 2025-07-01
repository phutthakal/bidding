<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// เช็กสิทธิ์ admin/buyer
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'buyer'])) {
  header('Location: login.php');
  exit;
}

// นับจำนวนรายการเปิด
$stmt = $pdo->query("SELECT COUNT(*) AS open_count FROM items WHERE status='open'");
$open = $stmt->fetch(PDO::FETCH_ASSOC);

// นับจำนวนรายการปิด
$stmt = $pdo->query("SELECT COUNT(*) AS closed_count FROM items WHERE status='closed'");
$closed = $stmt->fetch(PDO::FETCH_ASSOC);

// รายการปิด + ผู้ชนะ
$stmt = $pdo->query("
    SELECT
  items.*,
  CONCAT(buyer.first_name, ' ', buyer.last_name) AS buyer_name,
  buyer.email AS buyer_email,
  buyer.role AS buyer_role,
  companies_buyer.name AS company_buyer,
  winner_id,
  CONCAT(winner.first_name,' ',winner.last_name) AS winner_name,
  winner.email AS winner_email,
  winner.role AS winner_role,
  companies_winner.name AS company_winner
FROM items
LEFT JOIN users AS buyer ON items.seller_id = buyer.id
LEFT JOIN companies AS companies_buyer ON buyer.company_id = companies_buyer.id
LEFT JOIN users AS winner ON items.winner_id = winner.id
LEFT JOIN companies AS companies_winner ON winner.company_id = companies_winner.id
WHERE items.status='closed'
ORDER BY items.bidding_end DESC
");
$closed_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ดประมูล</title>
  <!-- <link rel="stylesheet" href="css/navbar.css"> -->
  <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

  <nav class="navbar">
    <div class="navbar-logo">
      <a href="index.php"><img src="../img/Dai-ichi-Packaging (1).png" alt="Logo"></a>
    </div>
    <ul class="navbar-menu">
      <li class="navbar-item"><a class="navbar-link" href="index.php">หน้าแรก</a></li>
      <li class="navbar-item"><a class="navbar-link" href="items.php">รายการประมูล</a></li>
      <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'buyer'): ?>
        <li class="navbar-item"><a class="navbar-link" href="create_item.php">ลงรายการประมูล</a></li>
        <li class="navbar-item"><a class="navbar-link" href="dashboard.php">แดชบอร์ด</a></li>
      <?php endif; ?>
      <li class="navbar-item"><a class="navbar-link" href="logout.php">ออกจากระบบ</a></li>
    </ul>
  </nav>


  <h1>แดชบอร์ด</h1>

  <div class="dashboard-summary">
    <!-- <div class="summary-card">
      <h2>กำลังเปิด</h2>
      <p><?= (int)$open['open_count'] ?> รายการ</p>
    </div> -->
    <div class="summary-card">
      <h2>ปิดแล้ว</h2>
      <p><?= (int)$closed['closed_count'] ?> รายการ</p>
    </div>
  </div>

  <h2>รายการที่ปิดแล้ว</h2>
  <table>
    <thead>
      <tr>
        <th>ชื่อสินค้า</th>
        <th>ราคาสุดท้าย</th>
        <th>ผู้ชนะ</th>
        <th>อีเมล</th>
        <th>ดูรายละเอียด</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($closed_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['title']) ?></td>
          <td><?= number_format($item['update_price']) ?> บาท</td>
          <td><?= $item['winner_name'] ? htmlspecialchars($item['winner_name']) : 'ไม่มีผู้ชนะ' ?></td>
          <td><?= $item['winner_email'] ? htmlspecialchars($item['winner_email']) : 'ไม่มีผู้ชนะ' ?></td>
          <td><a href="detail.php?id=<?= $item['id'] ?>">ดูรายละเอียด</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>