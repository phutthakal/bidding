<?php
session_start();
session_unset();      // ล้างตัวแปรทั้งหมดใน $_SESSION
session_destroy();    // ทำลาย session ปัจจุบัน

header('Location: index.php');  // กลับไปหน้าแรก
exit;
