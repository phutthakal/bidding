<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// รับ item_id จาก URL
$item_id = $_GET['id'];

// ดึงข้อมูลรายละเอียดของรายการ
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = :item_id");
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "ไม่พบรายการประมูลนี้!";
    exit;
}

// ตรวจสอบเวลาหมดการประมูล
$current_time = new DateTime();
$end_time = new DateTime($item['bidding_end']);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายละเอียดการประมูล</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>

    <h2>รายละเอียดการประมูล</h2>

    <div class="item-detail">
        <h3><?= htmlspecialchars($item['title']) ?></h3>
        <!-- แสดงรูปภาพจาก URL ที่เก็บในฐานข้อมูล -->
        <?php if (!empty($item['image_url'])): ?>
                    <p><strong>รูปภาพ:</strong></p>
                    <?php
                    // แยก URL ของภาพที่ถูกเก็บในฐานข้อมูล (คั่นด้วยคอมมา)
                    $image_urls = explode(',', $item['image_url']);
                    foreach ($image_urls as $image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="รูปภาพของ <?= htmlspecialchars($item['title']) ?>" width="200">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>ไม่มีรูปภาพสำหรับรายการนี้</p>
                <?php endif; ?>
                
        <p><strong>รายละเอียด:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>

        <!-- แสดงราคาเปิด -->
        <p><strong>ราคาเริ่มต้น:</strong> <?= number_format($item['price'], 2) ?> บาท</p>

        <!-- แสดงราคาปัจจุบัน -->
        <p><strong>ราคาปัจจุบัน:</strong> <?= number_format($item['update_price'], 2) ?> บาท</p>
        <p><strong>ราคาปัจจุบัน:</strong> <?= $item['description'] ?> บาท</p>

        <p><strong>สถานะ:</strong> <?= ucfirst($item['status']) ?></p>
        <p><strong>เวลาเริ่มต้นการประมูล:</strong> <?= $item['bidding_start'] ?></p>
        <p><strong>เวลาเสร็จสิ้นการประมูล:</strong> <?= $item['bidding_end'] ?></p>

        <!-- ถ้าการประมูลยังไม่ปิด ให้แสดงฟอร์มเสนอราคา -->
        <?php if ($item['status'] == 'open' && $current_time < $end_time): ?>
            <form method="POST" action="detail.php">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                <input type="number" name="amount" min="1" placeholder="เสนอราคา" required>
                <button type="submit">เสนอราคา</button>
            </form>
        <?php elseif ($item['status'] == 'closed'): ?>
            <p>การประมูลนี้ปิดแล้ว</p>
        <?php endif; ?>

        <!-- ตรวจสอบการเสนอราคา -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // รับค่าจากฟอร์ม
            $bid_amount = $_POST['amount'];

            // ตรวจสอบราคาที่เสนอ
            if ($bid_amount <= $item['price']) {
                echo "<script>alert('ราคาที่คุณเสนอจะต้องมากกว่าราคาเปิด'); window.location.href = 'detail.php?id=" . $item['id'] . "';</script>";
                exit;
            }

            // อัปเดตราคาและบันทึกผู้ชนะ
            $stmt = $pdo->prepare("UPDATE items SET current_bid = :current_bid, winner_id = :winner_id WHERE id = :item_id");
            $stmt->bindParam(':current_bid', $bid_amount);
            $stmt->bindParam(':winner_id', $_SESSION['user']['id']);  // บันทึกผู้ชนะ
            $stmt->bindParam(':item_id', $item_id);

            if ($stmt->execute()) {
                echo "<script>alert('คุณได้เสนอราคาใหม่เรียบร้อยแล้ว!'); window.location.href = 'detail.php?id=" . $item['id'] . "';</script>";
                exit;
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการเสนอราคา'); window.location.href = 'detail.php?id=" . $item['id'] . "';</script>";
                exit;
            }
        }
        ?>

    </div>

</body>

</html>
