<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// ดึงข้อมูลผู้ใช้จาก DB
$stmt = $pdo->prepare("
    SELECT users.*, companies.name AS company_name
    FROM users
    LEFT JOIN companies ON users.company_id = companies.id
    WHERE users.id=:id
");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('❌ ไม่พบข้อมูลผู้ใช้');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์: <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <link rel="stylesheet" href="css/profiles.css">
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


    <h1>โปรไฟล์ของคุณ</h1>
    <div class="profile-card">
        <p><strong>ชื่อ-นามสกุล:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>อีเมล:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>สิทธิ์ผู้ใช้:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>บริษัท:</strong> <?= htmlspecialchars($user['company_name']) ?></p>
    </div>
</body>

</html>