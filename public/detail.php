<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "ไม่พบรายการ";
    exit;
}

$item_id = $_GET['id'];

// ดึงข้อมูลรายการ
$stmt = $pdo->prepare("SELECT
  items.*,
  CONCAT(buyer.first_name, ' ', buyer.last_name) AS buyer_name,
  buyer.email AS buyer_email,
  buyer.role AS buyer_role,
  companies_buyer.name AS company_buyer,
  winner_id,
  CONCAT(winner.first_name,' ',winner.last_name) AS winner_name,
  winner.email AS winner_email,
  winner.role AS winner_role,
  companies_winner.name AS company_winner
FROM items
LEFT JOIN users AS buyer ON items.seller_id = buyer.id
LEFT JOIN companies AS companies_buyer ON buyer.company_id = companies_buyer.id
LEFT JOIN users AS winner ON items.winner_id = winner.id
LEFT JOIN companies AS companies_winner ON winner.company_id = companies_winner.id
WHERE items.id=:id");
$stmt->execute(['id' => $item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "ไม่พบรายการ";
    exit;
}

$date_start = new DateTime($item['bidding_start']) ?? 'ไม่มีข้อมูล';
$date_end = new DateTime($item['bidding_end']) ?? 'ไม่มีข้อมูล';

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายละเอียด: <?= htmlspecialchars($item['title']) ?></title>
    <link rel="stylesheet" href="css/detail.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
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

    <h1><?= htmlspecialchars($item['title']) ?></h1>

    <div class="item-images">
        <?php
        $image_urls = explode(',', $item['image_url']);
        foreach ($image_urls as $url): ?>
            <a href="<?= htmlspecialchars($url) ?>" data-lightbox="item-<?= $item['id'] ?>" data-title="<?= htmlspecialchars($item['title']) ?>">
                <img src="<?= htmlspecialchars($url) ?>" class="item-image" alt="รูปสินค้า">
            </a>
        <?php endforeach; ?>
    </div>

    <p class="item-description"><strong>รายละเอียด: </strong><?= htmlspecialchars($item['description']) ?></p>

    <p><strong>ราคาเปิด:</strong> <?= number_format($item['price']) ?> บาท</p>

    <p class="item-current-price" id="current-price-<?= $item['id'] ?>">
        ราคาปัจจุบัน: <?= number_format($item['update_price'] ?: $item['price']) ?> บาท
    </p>

    <p><strong>เวลาเริ่ม:</strong> <?= $date_start->format('d-m-Y H:i:s') ?></p>
    <p><strong>เวลาหยุด:</strong> <?= $date_end->format('d-m-Y H:i:s') ?></p>

    <p><strong>จำนวนสินค้า:</strong> <?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?></p>

    <p><strong>ผู้เสนอราคาต่ำสุด:</strong>
        <?= $item['winner_name'] ? htmlspecialchars($item['winner_name']) : 'ยังไม่มีผู้เสนอราคา'; ?>
    </p>

    <p><strong>บริษัทผู้เสนอราคาต่ำสุด:</strong>
        <?= $item['company_winner'] ? htmlspecialchars($item['company_winner']) : 'ยังไม่มีบริษัท'; ?>
    </p>
    <p><strong>อีเมลผู้เสนอราคาต่ำสุด: </strong>
        <?= $item['winner_email'] ? htmlspecialchars($item['winner_email']) : 'ยังไม่มีอีเมล'; ?>
    </p>

    <p><strong>ผู้เปิดประมูล: </strong><?= htmlspecialchars($item['buyer_name']) ?></p>

    <p><strong>บริษัทผู้เปิดประมูล: </strong><?= htmlspecialchars($item['company_buyer']) ?></p>
    <p><strong>อีเมลผู้เปิดประมูล: </strong><?= htmlspecialchars($item['buyer_email']) ?></p>

    <?php if (($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'seller') && $item['status'] === 'open' && $item['bidding_end'] > date('Y-m-d H:i:s')): ?>
        <form class="bid-form">
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            <input type="number" name="amount" min="1" placeholder="เสนอราคาต่ำกว่า..." required>
            <button type="submit">เสนอราคาต่ำกว่า</button>
        </form>
    <?php elseif ($_SESSION['user']['role'] === 'buyer' && $item['status'] === 'open' && $item['bidding_end'] > date('Y-m-d H:i:s')): ?>
        <p style="color:red;"><strong>ไม่มีสิทธิ์การประมูล</strong></p>
    <?php else: ?>
        <p style="color:red;"><strong>การประมูลนี้ปิดแล้ว</strong></p>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.bid-form');
            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);

                    try {
                        const response = await fetch('bid.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        // ✅ สร้างหรืออัปเดต div แจ้งผล
                        let resultDiv = form.nextElementSibling;
                        if (!resultDiv || !resultDiv.classList.contains('bid-result')) {
                            resultDiv = document.createElement('div');
                            resultDiv.classList.add('bid-result');
                            form.insertAdjacentElement('afterend', resultDiv);
                        }

                        // ตั้งข้อความแจ้งผล
                        resultDiv.textContent = data.success ? data.message : data.message || 'เกิดข้อผิดพลาด';
                        resultDiv.classList.toggle('success', data.success);
                        resultDiv.classList.toggle('error', !data.success);

                    } catch (err) {
                        alert('เกิดข้อผิดพลาด: ' + err);
                    }
                });
            }
        });
    </script>
</body>

</html>