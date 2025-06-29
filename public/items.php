<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// เช็กว่า login แล้ว
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// อัพเดตสถานะของรายการที่หมดเวลาการประมูลแล้ว
$updateStmt = $pdo->prepare("UPDATE items SET status = 'closed' WHERE bidding_end < NOW() AND status != 'closed'");
$updateStmt->execute();

// ดึงข้อมูลรายการประมูลที่ยังไม่หมดเวลา
$stmt = $pdo->query("SELECT * FROM items WHERE bidding_end > NOW() AND bidding_start <= NOW() AND price > 0;");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบการอัปโหลดไฟล์ภาพ
$image_url = null;
if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // กำหนดพาธที่จะเก็บไฟล์ภาพ
    $target_dir = "..uploads/";  // ตรวจสอบให้แน่ใจว่าโฟลเดอร์ uploads มีสิทธิ์ในการเขียน
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // ย้ายไฟล์จาก temp ไปยังโฟลเดอร์ uploads
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;  // เก็บ URL ของไฟล์ที่อัปโหลด
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
    }
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการประมูลที่เปิดอยู่</title>
    <link rel="stylesheet" href="css/items.css" />
</head>

<body>

    <h2>รายการประมูลที่เปิดอยู่</h2>

    <?php if (count($items) === 0): ?>
        <p>ยังไม่มีรายการประมูลในขณะนี้</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="item">
                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <!-- <p><strong>รายละเอียด:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p> -->

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

                <p><strong>ราคาเริ่มต้น:</strong> <?= number_format($item['price']) ?> บาท</p>
                <!-- แสดงราคาปัจจุบัน (รวมราคาทั้งสอง) -->
                <p><strong>ราคาการเสนอขั้นต่ำ:</strong> <?= number_format($item['minimum_bid']) ?> บาท</p>
                <p><strong>ราคาปัจจุบัน:</strong> <?= number_format($item['update_price']) ?> บาท</p>
                <p><strong>จำนวนสินค้า:</strong> <?= $item['quantity'] ?> ชิ้น</p>
                <p><strong>เวลาเริ่มต้นการประมูล:</strong> <?= $item['bidding_start'] ?></p>
                <p><strong>เวลาเสร็จสิ้นการประมูล:</strong> <?= $item['bidding_end'] ?></p>

                <!-- ลิงค์ไปยังหน้า detail.php -->
                <a href="detail.php?id=<?= $item['id'] ?>">ดูรายละเอียด</a>

                <!-- ถ้าสถานะเป็น "open" ให้แสดงฟอร์มเสนอราคา -->
                <?php if ($item['status'] == 'open'): ?>
                    <form method="POST" action="bid.php">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <input type="number" name="amount" min="1" placeholder="เสนอราคา" required>
                        <button type="submit">เสนอราคา</button>
                    </form>

                <?php elseif ($item['status'] == 'closed'): ?>
                    <p>การประมูลนี้ปิดแล้ว</p>
                <?php endif; ?>

                <hr>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</body>

</html>