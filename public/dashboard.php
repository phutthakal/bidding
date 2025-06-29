<?php
require_once __DIR__ . '/../config/connect.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ด (Admin/Buyer)</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <h2>แดชบอร์ด - ผู้ดูแล/ผู้จัดซื้อ</h2>
  <div id="dashboardContent">กำลังโหลดข้อมูล...</div>

  <script>
    const token = localStorage.getItem('token');
    if (!token) {
      alert('กรุณาเข้าสู่ระบบ');
      window.location.href = 'login.php';
    }

    function parseJwt (token) {
      const base64 = token.split('.')[1];
      return JSON.parse(atob(base64));
    }

    const user = parseJwt(token);
    if (!['admin', 'buyer'].includes(user.role)) {
      alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
      window.location.href = 'index.php';
    }

    // โหลดรายการทั้งหมดที่มีผู้ชนะแล้ว (ตัวอย่างเฉย ๆ ใช้ API เฉพาะผู้ชนะ)
    fetch('/admin/winners', {
      headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(res => res.json())
    .then(data => {
      const div = document.getElementById('dashboardContent');
      div.innerHTML = '';

      if (!Array.isArray(data) || data.length === 0) {
        div.innerHTML = '<p>ยังไม่มีผลผู้ชนะในระบบ</p>';
        return;
      }

      data.forEach(row => {
        const block = document.createElement('div');
        block.innerHTML = `
          <h3>${row.item_title}</h3>
          <p><strong>ผู้ชนะ:</strong> ${row.seller_name}</p>
          <p><strong>ราคา:</strong> ${row.amount} บาท</p>
          <p><strong>บริษัท:</strong> ${row.company}</p>
          <hr>
        `;
        div.appendChild(block);
      });
    })
    .catch(err => {
      console.error(err);
      document.getElementById('dashboardContent').innerHTML = '<p>เกิดข้อผิดพลาดในการโหลดข้อมูล</p>';
    });
  </script>

</body>
</html>
