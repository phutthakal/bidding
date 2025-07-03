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

$where = "";
$params = [];

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
  $where = " AND DATE(items.bidding_end) BETWEEN :start AND :end";
  $params['start'] = $_GET['start_date'];
  $params['end'] = $_GET['end_date'];
}

// รายการปิด + ผู้ชนะ
$stmt = $pdo->prepare("
    SELECT
  items.id,
  items.title AS NameProduct,
  items.description,
  items.image_url,
  items.price,
  items.update_price,
  items.quantity,
  items.unit,
  items.bidding_start,
  items.bidding_end,
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
WHERE items.status='closed'$where
    ORDER BY items.bidding_end DESC
");
$stmt->execute($params);
$closed_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ดประมูล</title>
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

  <?php include '../ui/navbar.php'; ?>

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

  <form method="GET" class="dashboard-filter">
    <label>เริ่มวันที่: <input type="date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>"></label>
    <label>ถึงวันที่: <input type="date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>"></label>
    <button type="submit">ค้นหา</button>
    <?php if (!empty($_GET['start_date']) && !empty($_GET['end_date'])): ?>
      <a href="export_winners.php?start_date=<?= urlencode($_GET['start_date']) ?>&end_date=<?= urlencode($_GET['end_date']) ?>" class="export-button">📥 ดาวน์โหลด Excel</a>
    <?php endif; ?>
  </form>

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
          <td><?= htmlspecialchars($item['NameProduct']) ?></td>
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