<?php
session_start();
session_unset();  // ล้างตัวแปร Session ทั้งหมด
session_destroy(); // ทำลาย Session
header("location: index.php"); // กลับไปหน้าล็อคอิน
exit();
?>