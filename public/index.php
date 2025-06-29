<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

$isLoggedIn = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ระบบประมูลออนไลน์</title>
  <link rel="stylesheet" href="css/index.css">
</head>

<body>

  <!-- เริ่มเมนูบาร์ -->
  <nav>
  <div class="logo">
    <img src="../img/j36632.gif" alt="Logo" />
  </div>
    <ul>
      <li><a href="index.php">หน้าแรก</a></li>
      <li><a href="#">เกี่ยวกับเรา</a></li>
      <li><a href="#">ติดต่อเรา</a></li>
      <?php if ($isLoggedIn): ?>
        <li><a href="#">โปรไฟล์</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <!-- สิ้นสุดเมนูบาร์ -->

  <h1>ระบบประมูลออนไลน์</h1>

  <?php if ($isLoggedIn): ?>
    <p>ยินดีต้อนรับ, <?= htmlspecialchars($user['name']) ?> (<?= $user['role'] ?>)</p>
    <ul>
      <?php if ($user['role'] === 'admin' || $user['role'] === 'buyer'): ?>
        <li><a href="dashboard.php">แดชบอร์ด</a></li>
        <li><a href="create_item.php">ลงข้อมูลการประมูล</a></li>
      <?php endif; ?>
      <li><a href="items.php">ดูรายการประมูล</a></li>
      <li><a href="logout.php">ออกจากระบบ</a></li>
    </ul>
  <?php else: ?>
    <p>เลือกเมนูที่คุณต้องการ:</p>
    <ul>
      <li><a href="supplier_register.php">สมัครสมาชิก</a></li>
      <li><a href="login.php">เข้าสู่ระบบ</a></li>
    </ul>
  <?php endif; ?>

  <footer>
    <hr>
    <small>&copy; <?= date('Y'); ?> ระบบประมูล | พัฒนาโดยทีมคุณ 💻</small>
  </footer>

</body>

</html>