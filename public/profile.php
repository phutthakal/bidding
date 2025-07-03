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
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>

<body>
    
<?php include '../ui/navbar.php'; ?>

    <h1>โปรไฟล์ของคุณ</h1>
    <div class="profile-card">
        <p><strong>ชื่อ-นามสกุล:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>อีเมล:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>สิทธิ์ผู้ใช้:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>บริษัท:</strong> <?= htmlspecialchars($user['company_name']) ?></p>
    </div>
</body>

</html>