<nav class="navbar">
  <div class="navbar-logo">
    <a href="index.php"><img src="../img/Dai-ichi-Packaging (1).png" alt="Logo"></a>
  </div>
  <ul class="navbar-menu">
    <li class="navbar-item"><a class="navbar-link" href="index.php">หน้าแรก</a></li>

    <?php if (isset($_SESSION['user'])): ?>
      <?php $role = $_SESSION['user']['role']; ?>

      <?php if ($role === 'admin'): ?>
        <li class="navbar-item"><a class="navbar-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
        <li class="navbar-item"><a class="navbar-link" href="dashboard.php">แดชบอร์ด</a></li>
        <!-- <li class="navbar-item"><a class="navbar-link" href="supplier_register.php">สมัครสมาชิก(Supplier)</a></li> -->
        <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li>

      <?php elseif ($role === 'buyer'): ?>
        <li class="navbar-item"><a class="navbar-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
        <li class="navbar-item"><a class="navbar-link" href="dashboard.php">แดชบอร์ด</a></li>
        <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li>

      <?php elseif ($role === 'seller'): ?>
        <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li>

      <?php endif; ?>

      <!-- เมนูโปรไฟล์/ออกจากระบบ แสดงทุก role ที่ login -->
      <li class="navbar-item"><a class="navbar-link" href="profile.php">โปรไฟล์</a></li>
      <li class="navbar-item"><a class="navbar-link" href="logout.php">ออกจากระบบ</a></li>

    <?php else: ?>
      <li class="navbar-item"><a class="navbar-link" href="login.php">เข้าสู่ระบบ</a></li>
      <li class="navbar-item"><a class="navbar-link" href="https://www.dai-ichipack.com/">ติดต่อเรา</a></li>
    <?php endif; ?>
  </ul>
</nav>
