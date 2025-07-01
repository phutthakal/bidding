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

  <!-- เมนูบาร์ -->
  <nav class="navbar">
    <div class="navbar-logo">
      <a href="index.php"><img src="../img/Dai-ichi-Packaging (1).png" alt="Logo"></a>
    </div>
    <ul class="navbar-menu">
      <li class="navbar-item"><a class="navbar-link" href="index.php">หน้าแรก</a></li>
      <li class="navbar-item"><a class="navbar-link" href="login.php">เข้าสู่ระบบ</a></li>
      <?php if ($isLoggedIn): ?>
        <?php if ($user['role'] === 'admin' || $user['role'] === 'buyer'): ?>
          <li class="navbar-item"><a class="navbar-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
          <li class="navbar-item"><a class="navbar-link" href="dashboard.php">แดชบอร์ด</a></li>
        <?php endif; ?>
        <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li>
        <li class="navbar-item"><a class="navbar-link" href="logout.php">ออกจากระบบ</a></li>
        <li class="navbar-item"><a class="navbar-link" href="profiles.php">โปรไฟล์</a></li>
      <?php else: ?>
        <!-- <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li> -->
        <li class="navbar-item"><a class="navbar-link" href="https://www.dai-ichipack.com/">ติดต่อเรา</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <!-- ส่วนหัว -->
  <header class="hero">
    <h1 class="hero-title">ระบบประมูลออนไลน์</h1>
  </header>

  <!-- เนื้อหาหลัก -->
  <main class="main-content">
    <?php if ($isLoggedIn): ?>
      <div class="welcome-box">
        <p class="welcome-message">ยินดีต้อนรับ, <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</p>
        <ul class="menu-list">
          <?php if ($user['role'] === 'admin'): ?>
            <li class="menu-item"><a class="menu-link" href="dashboard.php">แดชบอร์ด</a></li>
            <li class="menu-item"><a class="menu-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
            <li class="menu-item"><a class="menu-link" href="supplier_register.php">สมัครสมาชิก</a></li>
          <?php elseif ($user['role'] === 'buyer'): ?>
            <li class="menu-item"><a class="menu-link" href="dashboard.php">แดชบอร์ด</a></li>
            <li class="menu-item"><a class="menu-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
          <?php endif; ?>
          <li class="menu-item"><a class="menu-link" href="items.php">ดูรายการประมูล</a></li>
          <li class="menu-item"><a class="menu-link" href="logout.php">ออกจากระบบ</a></li>
        </ul>
      </div>
    <?php else: ?>
      <div class="guest-box">
        <p class="guest-message">เลือกเมนูที่คุณต้องการ:</p>
        <ul class="menu-list">
          <!-- <li class="menu-item"><a class="menu-link" href="supplier_register.php">สมัครสมาชิก</a></li> -->
          <li class="menu-item"><a class="menu-link" href="login.php">เข้าสู่ระบบ</a></li>
        </ul>
      </div>
    <?php endif; ?>
  </main>

  <!-- ฟุตเตอร์ -->
  <footer class="main-footer">
    <hr>
    <small class="footer-text">&copy; <?= date('Y'); ?> ระบบประมูล | พัฒนาโดยทีมคุณ 💻</small>
  </footer>

</body>

</html>