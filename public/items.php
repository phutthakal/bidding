<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// เช็กว่า login แล้ว
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}


$isLoggedIn = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;

// อัพเดตสถานะของรายการที่หมดเวลาการประมูลแล้ว
$updateStmt = $pdo->prepare("UPDATE items SET status = 'closed' WHERE bidding_end < NOW() AND status != 'closed'");
$updateStmt->execute();

$stmt = $pdo->query("
    SELECT * FROM items
    WHERE DATE(NOW()) BETWEEN DATE(bidding_start) AND DATE(bidding_end)
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ตรวจสอบการอัปโหลดไฟล์ภาพ
$image_url = null;
if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // กำหนดพาธที่จะเก็บไฟล์ภาพ
    $target_dir = "/..uploads/";  // ตรวจสอบให้แน่ใจว่าโฟลเดอร์ uploads มีสิทธิ์ในการเขียน
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // ย้ายไฟล์จาก temp ไปยังโฟลเดอร์ uploads
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;  // เก็บ URL ของไฟล์ที่อัปโหลด
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
    }
}

function formatThaiDateTime($datetime)
{
    $months = [
        '',
        'ม.ค.',
        'ก.พ.',
        'มี.ค.',
        'เม.ย.',
        'พ.ค.',
        'มิ.ย.',
        'ก.ค.',
        'ส.ค.',
        'ก.ย.',
        'ต.ค.',
        'พ.ย.',
        'ธ.ค.'
    ];
    $date = new DateTime($datetime);
    $day = $date->format('j');
    $month = $months[(int)$date->format('n')];
    $year = $date->format('Y');
    $time = $date->format('H:i');
    return "$day $month $year $time น.";
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการประมูลที่เปิดอยู่</title>
    <link rel="stylesheet" href="css/items.css" />
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
</head>

<body class="items-page">

    <!-- เมนูบาร์ -->
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="index.php"><img src="../img/Dai-ichi-Packaging (1).png" alt="Logo"></a>
        </div>
        <ul class="navbar-menu">
            <li class="navbar-item"><a class="navbar-link" href="index.php">หน้าแรก</a></li>
            <!-- <li class="navbar-item"><a class="navbar-link" href="supplier_register.php">สมัครสมาชิก</a></li> -->
            <li class="navbar-item"><a class="navbar-link" href="items.php">ดูรายการประมูล</a></li>
            <?php if ($isLoggedIn): ?>
                <?php if ($user['role'] === 'admin' || $user['role'] === 'buyer'): ?>
                    <li class="navbar-item"><a class="navbar-link" href="create_item.php">ลงข้อมูลการประมูล</a></li>
                    <li class="navbar-item"><a class="navbar-link" href="dashboard.php">แดชบอร์ด</a></li>
                <?php endif; ?>
                <li class="navbar-item"><a class="navbar-link" href="logout.php">ออกจากระบบ</a></li>
            <?php else: ?>
                <li class="navbar-item"><a class="navbar-link" href="contact.php">ติดต่อเรา</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <header class="items-header">
        <h2 class="items-title">รายการประมูลที่เปิดอยู่</h2>
    </header>

    <main class="items-container">
        <?php if (count($items) === 0): ?>
            <p class="no-items-message">ยังไม่มีรายการประมูลในขณะนี้</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <h3 class="item-title"><strong>ชื่อสินค้า: </strong><?= htmlspecialchars($item['title']) ?></h3>

                    <?php if (!empty($item['image_url'])): ?>
                        <div class="item-images">
                            <?php
                            $image_urls = explode(',', $item['image_url']);
                            foreach ($image_urls as $image_url): ?>
                                <a href="<?= htmlspecialchars($image_url) ?>"
                                    data-lightbox="item-<?= $item['id'] ?>"
                                    data-title="<?= htmlspecialchars($item['title']) ?>">
                                    <img class="item-image" src="<?= htmlspecialchars($image_url) ?>" alt="รูปภาพของ <?= htmlspecialchars($item['title']) ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>


                    <?php else: ?>
                        <p class="no-image">ไม่มีรูปภาพสำหรับรายการนี้</p>
                    <?php endif; ?>

                    <div class="item-details">
                        <p class="item-detail"><strong>ราคาเริ่มต้น:</strong> <?= number_format($item['price']) ?> บาท</p>
                        <!-- <p class="item-detail"><strong>ราคาการเสนอขั้นต่ำ:</strong> <?= number_format($item['minimum_bid']) ?> บาท</p> -->
                        <p class="item-current-price" id="current-price-<?= $item['id'] ?>">
                            ราคาปัจจุบัน: <?= number_format($item['update_price']) ?> บาท
                        </p>
                        <p class="item-detail"><strong>จำนวนสินค้า:</strong> <?= $item['quantity'] ?> <?= $item['unit'] ?></p>
                        <!-- <p class="item-detail"><strong>เวลาเริ่มต้นการประมูล:</strong> <?= $item['bidding_start'] ?></p>
                        <p class="item-detail"><strong>เวลาเสร็จสิ้นการประมูล:</strong> <?= $item['bidding_end'] ?></p> -->
                        <p class="item-detail"><strong>เวลาเริ่มต้นการประมูล:</strong> <?= formatThaiDateTime($item['bidding_start']) ?></p>
                        <p class="item-detail"><strong>เวลาเสร็จสิ้นการประมูล:</strong> <?= formatThaiDateTime($item['bidding_end']) ?></p>

                    </div>

                    <div class="item-actions">
                        <a class="item-link" href="detail.php?id=<?= $item['id'] ?>">ดูรายละเอียด</a>

                        <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'seller' && $item['status'] == 'open'): ?>
                            <form class="bid-form" method="POST" action="#">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input class="bid-input" type="number" name="amount" min="1" placeholder="เสนอราคา" required>
                                <button class="bid-button" type="submit">เสนอราคา</button>
                            </form>
                        <?php elseif ($item['status'] == 'closed'): ?>
                            <p class="closed-message">การประมูลนี้ปิดแล้ว</p>
                        <?php elseif ($_SESSION['user']['role'] === 'buyer' && $item['status'] == 'open'): ?>
                            <p class="closed-message">ไม่มีสิทธิ์การประมูล</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bidForms = document.querySelectorAll('.bid-form');
            bidForms.forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);

                    try {
                        const response = await fetch('bid.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        let resultDiv = form.nextElementSibling;
                        if (!resultDiv || !resultDiv.classList.contains('bid-result')) {
                            resultDiv = document.createElement('div');
                            resultDiv.classList.add('bid-result');
                            form.insertAdjacentElement('afterend', resultDiv);
                            
                        }
                        resultDiv.textContent = data.success ? data.message : data.message || 'เกิดข้อผิดพลาด';
                        resultDiv.style.color = data.success ? 'green' : 'red';

                    } catch (err) {
                        alert('เกิดข้อผิดพลาด: ' + err);
                    }
                });
            });

            // เรียลไทม์ polling อัปเดตราคาปัจจุบัน
            const itemIds = [...document.querySelectorAll('.item-current-price')]
                .map(el => el.id.replace('current-price-', ''));

            setInterval(() => {
                itemIds.forEach(id => {
                    fetch(`get_current_price.php?item_id=${id}&_=${Date.now()}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.current_price !== undefined) {
                                const priceElement = document.getElementById('current-price-' + id);
                                const newPrice = parseFloat(data.current_price).toFixed(0);
                                if (priceElement.dataset.lastPrice !== newPrice) {
                                    priceElement.textContent = `ราคาปัจจุบัน: ${parseFloat(newPrice).toLocaleString()} บาท`;
                                    priceElement.dataset.lastPrice = newPrice;
                                    priceElement.classList.add('updated');
                                    setTimeout(() => priceElement.classList.remove('updated'), 500);
                                }
                            }
                        })
                        .catch(err => console.error('อัปเดตราคา error:', err));
                });
            }, 1000);
        });
    </script>
</body>

</html>