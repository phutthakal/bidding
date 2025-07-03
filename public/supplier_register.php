<?php
require_once __DIR__ . '/../config/connect.php';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = $_POST['first_name'] ?? '';
  $last_name = $_POST['last_name'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $company = $_POST['company'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $role = $_POST['role'] ?? '';
  // $role = 'seller';

  if ($first_name && $last_name && $email && $password && $company && $role) {
    // ตรวจอีเมลซ้ำ
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
      $error = "อีเมลนี้มีอยู่แล้ว";
    } else {
      // ตรวจหรือเพิ่มบริษัท
      $stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
      $stmt->execute([$company]);
      $company_id = $stmt->fetchColumn();

      if (!$company_id) {
        $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (?)");
        $stmt->execute([$company]);
        $company_id = $pdo->lastInsertId();
      }

      // บันทึกผู้ใช้
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role, phone, company_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([$first_name, $last_name, $email, $hash, $role, $phone, $company_id]);

      $success = "✅ สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
    }
  } else {
    $error = "กรุณากรอกข้อมูลให้ครบ";
  }
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิกผู้ขาย (Supplier)</title>
  <link rel="stylesheet" href="css/supplier_register.css">
  <link rel="stylesheet" href="css/navbar.css">
</head>

<body>
  <?php include '../ui/navbar.php'; ?>

  <h2>สมัครสมาชิกสำหรับผู้ขาย (Supplier)</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php elseif ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="first_name">FullName:</label>
    <input type="text" name="first_name" placeholder="ชื่อจริง" required><br>
    <label for="first_name">LastName:</label>
    <input type="text" name="last_name" placeholder="นามสกุล" required><br>
    <label for="first_name">Email:</label>
    <input type="email" name="email" placeholder="อีเมล" required><br>
    <label for="first_name">Password:</label>
    <input type="password" name="password" placeholder="รหัสผ่าน" required><br>
    <label for="first_name">company:</label>
    <input type="text" name="company" placeholder="ชื่อบริษัท" required><br>
    <label for="first_name">Phone:</label>
    <input type="text" name="phone" placeholder="เบอร์โทรศัพท์"><br>
    <label for="role">Role:</label>
    <select name="role" id="role" required>
      <option value="">-- Role --</option>
      <option value="seller">Seller</option>
      <option value="buyer">Buyer</option>
      <option value="admin">Admin</option>
    </select><br>

    <button type="submit">สมัครสมาชิก</button>
  </form>


</body>

</html>