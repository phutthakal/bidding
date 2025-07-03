<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// เช็ก session ว่าผู้ใช้ login แล้ว และมี role เป็น admin หรือ buyer
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'buyer')) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $minimum_bid = $_POST['minimum_bid'] ?? NULL;  // หากไม่มีการกรอก minimum_bid จะตั้งเป็น NULL
    $quantity = $_POST['quantity'] ?? '';
    $bidding_start = $_POST['bidding_start'] ?? '';  // รับวันที่เปิดการประมูล
    $bidding_end = $_POST['bidding_end'] ?? '';      // รับวันที่ปิดการประมูล
    $unit = htmlspecialchars(trim($_POST['unit'] ?? ''));
    $price = $_POST['price'] ?? 0;
    $seller_id = $_SESSION['user']['id'];
    $auction_room = $_POST['auction_room'] ?? null;


    $image_urls = [];

    // ตรวจสอบและอัปโหลดไฟล์ภาพหลายไฟล์
    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $upload_dir = '../uploads/';  // โฟลเดอร์ที่เก็บไฟล์
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];  // กำหนดประเภทไฟล์ที่รองรับ

        // วนลูปอัปโหลดแต่ละไฟล์
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $image_tmp = $_FILES['images']['tmp_name'][$i];
            $image_name = time() . '-' . basename($_FILES['images']['name'][$i]);
            $image_type = $_FILES['images']['type'][$i];

            // เช็กประเภทไฟล์
            if (in_array($image_type, $allowed_types)) {
                $upload_file = $upload_dir . $image_name;
                if (move_uploaded_file($image_tmp, $upload_file)) {
                    $image_urls[] = $upload_file;  // เก็บ URL ของไฟล์ภาพ
                } else {
                    $error = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์บางไฟล์";
                    break;
                }
            } else {
                $error = "ประเภทไฟล์ไม่ถูกต้อง";
                break;
            }
        }
    }

    // แปลง datetime-local ที่ได้จาก Flatpickr เป็น MySQL datetime
    $start_dt = DateTime::createFromFormat('Y-m-d\TH:i', $bidding_start);
    $end_dt = DateTime::createFromFormat('Y-m-d\TH:i', $bidding_end);

    if ($title && $description && $bidding_start && $bidding_end && $unit) {
        // บันทึกรายการประมูลลงในฐานข้อมูล
        $stmt = $pdo->prepare("INSERT INTO items 
    (seller_id, title, description, image_url, bidding_start, bidding_end, price, minimum_bid, quantity, unit, auction_room) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // แปลง array ของ URL รูปภาพเป็น string ด้วย comma
        $image_urls_string = implode(',', $image_urls);
        $stmt->execute([
            $seller_id,
            $title,
            $description,
            $image_urls_string,
            $bidding_start,
            $bidding_end,
            $price,
            $minimum_bid,
            $quantity,
            $unit
        ]);
        // Redirect ไปหน้า items.php ทันที
        header('Location: items.php');
        exit; // สำคัญ! ป้องกัน PHP รันต่อหลัง redirect
        $success = "✅ รายการประมูลลงทะเบียนสำเร็จ!";
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบ";
    }
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    exit; // ให้ script หยุดทันทีหลังแสดงข้อมูล

}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ลงประมูล</title>
    <link rel="stylesheet" href="css/create_item.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<body>

    <?php include '../ui/navbar.php'; ?>

    <h2>เพิ่มสินค้า</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="create_item.php" enctype="multipart/form-data">
        <label for="title">ชื่อรายการ:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">รายละเอียด:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="price">ราคาเริ่มต้น:</label>
        <input type="number" name="price" id="price" min="1" required>

        <!-- <label for="minimum_bid">ราคาขั้นต่ำ:</label>
        <input type="number" name="minimum_bid" id="minimum_bid" min="1" required> -->

        <!-- <label for="bidding_start">เวลาเริ่มต้นการประมูล:</label>
        <input type="datetime-local" name="bidding_start" id="bidding_start" required> -->
        <!-- <label for="formatted_date">วันที่แบบไทย:</label>
        <input type="text" id="formatted_date" readonly> -->
        <label>เวลาเริ่มต้นการประมูล:</label>
        <input type="text" id="bidding_start" name="bidding_start" required>

        <label>เวลาสิ้นสุดการประมูล:</label>
        <input type="text" id="bidding_end" name="bidding_end" required>
        <!-- <label for="bidding_end">เวลาเสร็จสิ้นการประมูล:</label>
        <input type="datetime-local" name="bidding_end" id="bidding_end" required> -->
        <!-- <label for="formatted_date_end">วันที่แบบไทย:</label>
        <input type="text" id="formatted_date_end" readonly> -->

        <label for="quantity">จำนวนสินค้า:</label>
        <input type="number" name="quantity" id="quantity" min="1" required>

        <label for="unit">หน่วยสินค้า:</label>
        <input type="text" name="unit" id="unit" placeholder="เช่น ชิ้น, กล่อง" required>

        <label>ห้องประมูล:</label>
        <input type="text" name="auction_room" placeholder="เช่น ROOM2024-001" required>

        <label>รูปสินค้า (เลือกหลายรูปได้):</label><br>
        <input type="file" name="images[]" id="image-input" accept="image/*" multiple><br><br>

        <div id="preview"></div><br>

        <button type="submit">สร้างรายการ</button>

    </form>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
<script>
    flatpickr("#bidding_start", {
        enableTime: true,
        dateFormat: "Y-m-d\\TH:i",
        locale: "th",
        time_24hr: true
    });
    flatpickr("#bidding_end", {
        enableTime: true,
        dateFormat: "Y-m-d\\TH:i",
        locale: "th",
        time_24hr: true
    });
    document.getElementById('image-input').addEventListener('change', function(event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = ''; // ล้างพรีวิวเดิม

        const files = event.target.files;
        if (files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '150px';
                        img.style.margin = '10px';
                        img.style.border = '1px solid #ccc';
                        img.style.borderRadius = '8px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    // document.getElementById('bidding_start').addEventListener('input', function() {
    //     const value = this.value; // e.g. "2024-07-01T14:30"
    //     if (!value) return;

    //     // แยกวัน/เวลา
    //     const [datePart, timePart] = value.split('T');
    //     const [y, m, d] = datePart.split('-');

    //     // แปลงเป็น d/m/Y เวลา H:i
    //     const formatted = `${d}/${m}/${y} ${timePart}`;
    //     document.getElementById('formatted_date').value = formatted;
    // });
    // document.getElementById('bidding_end').addEventListener('input', function() {
    //     const value = this.value; // e.g. "2024-07-01T14:30"
    //     if (!value) return;

    //     // แยกวัน/เวลา
    //     const [datePart, timePart] = value.split('T');
    //     const [y, m, d] = datePart.split('-');

    //     // แปลงเป็น d/m/Y เวลา H:i
    //     const formatted = `${d}/${m}/${y} ${timePart}`;
    //     document.getElementById('formatted_date_end').value = formatted;
    // });
    // document.getElementById('auctionForm').addEventListener('submit', function(e) {
    //     const formatted = document.getElementById('formatted_date').value; // เช่น "01/07/2024 14:30"
    //     const regex = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;
    //     const match = formatted.match(regex);

    //     if (match) {
    //         const [, d, m, y, hh, mm] = match;
    //         const isoDatetime = `${y}-${m}-${d}T${hh}:${mm}`; // กลับเป็น ISO
    //         document.getElementById('bidding_start').value = isoDatetime;
    //     } else {
    //         alert("รูปแบบวันที่ไม่ถูกต้อง ต้องเป็น dd/mm/yyyy hh:mm");
    //         e.preventDefault();
    //     }
    // });
</script>