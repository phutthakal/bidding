<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'role' => $user['role']
            ];

            // ส่งไปหน้าหลักหรือ dashboard
            header('Location: index.php');
            exit;
        } else {
            $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }
    } else {
        $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>เข้าสู่ระบบ</title>
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>

  <h2>เข้าสู่ระบบ</h2>

  <?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST">
    <input type="email" name="email" placeholder="อีเมล" required><br>
    <input type="password" name="password" placeholder="รหัสผ่าน" required><br>
    <button type="submit">เข้าสู่ระบบ</button>
  </form>

  <p id="result"></p>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      const res = await fetch('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await res.json();
      const msg = document.getElementById('result');

      if (res.ok && result.token) {
        localStorage.setItem('token', result.token);
        msg.innerText = '✅ เข้าสู่ระบบสำเร็จ';

        // ไปหน้าแสดงรายการประมูลหลังจากเข้าสู่ระบบ
        setTimeout(() => window.location.href = 'items.php', 1000);
      } else {
        msg.innerText = '❌ ' + (result.message || 'เข้าสู่ระบบไม่สำเร็จ');
      }
    });
  </script>

</body>
</html>
