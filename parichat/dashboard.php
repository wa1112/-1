<?php
include('db.php');

// ตรวจสอบว่ามีการล็อคอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("location: index.php"); // ถ้ายังไม่ได้ล็อคอิน ให้กลับไปหน้าล็อคอิน
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username']; // ชื่อผู้ใช้สำหรับระบบ (login)
$fullname = $_SESSION['fullname'] ?? $username; // **ชื่อ-นามสกุลสำหรับแสดงผล**

// กำหนดสีและชื่อสิทธิ์ให้สวยงาม
$role_data = [
    'admin' => ['name' => 'ผู้ดูแลระบบ', 'color' => 'bg-danger', 'icon' => '👑'],
    'user' => ['name' => 'ผู้ใช้ทั่วไป', 'color' => 'bg-info', 'icon' => '👤'],
    'customer' => ['name' => 'ลูกค้า', 'color' => 'bg-success', 'icon' => '🛍️'],
    'employee' => ['name' => 'พนักงาน', 'color' => 'bg-warning', 'icon' => '💼'],
];

$current_role = $role_data[$role];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $current_role['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .role-badge {
            font-size: 1.2rem;
            padding: 0.5em 1em;
        }
        .content-box {
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background-color: white;
        }
        /* ปรับปรุงสไตล์ให้คล้ายภาพ CW1.jpg */
        .welcome-card {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .info-card {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background-color: #fcfcfc;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Parichat System</a>
            <div class="d-flex">
                <span class="navbar-text me-3 text-white">
                    ยินดีต้อนรับ <?php echo $fullname; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="welcome-card d-flex justify-content-between align-items-center">
            <div>
                <h2>สวัสดี, <?php echo $fullname; ?></h2> <small>เข้าสู่ระบบสำเร็จ!</small>
            </div>
            <a href="logout.php" class="btn btn-warning">ออกจากระบบ</a>
        </div>

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="info-card">
                    <h4 class="text-primary">ข้อมูลส่วนตัว</h4>
                    <hr>
                    <p><strong>ชื่อผู้ใช้ :</strong> <?php echo $username; ?></p>
                    <p><strong>ชื่อเล่น:</strong> <?php echo $fullname; ?></p>
                    <p><strong>สิทธิ์การใช้งาน:</strong> 
                        <span class="badge <?php echo $current_role['color']; ?> text-white"><?php echo $current_role['name']; ?></span>
                    </p>
                    <p><strong>สถานะ:</strong> ใช้งาน</p>
                </div>
            </div>

            <div class="col-md-7 mb-4">
                <div class="info-card">
                    <h4 class="text-danger">🔥 เมนูสำหรับ<?php echo $current_role['name']; ?></h4>
                    <hr>
                    <div class="content-box p-0 border-0 shadow-none bg-transparent">
                        <?php if ($role == 'admin'): ?>
                            <p>คุณมีสิทธิ์ในการจัดการระบบเต็มรูปแบบ</p>
                            <a href="#" class="btn btn-danger me-2 mb-2">จัดการผู้ใช้ทั้งหมด</a>
                            <a href="#" class="btn btn-outline-danger me-2 mb-2">ดูรายงานและสถิติ</a>
                            <a href="#" class="btn btn-outline-danger mb-2">จัดการสิทธิ์การใช้งาน</a>

                        <?php elseif ($role == 'user'): ?>
                            <p>คุณสามารถเข้าถึงข้อมูลสาธารณะและอัปเดตโปรไฟล์ของคุณ</p>
                            <a href="#" class="btn btn-info me-2 mb-2">ดูโปรไฟล์</a>
                            <a href="#" class="btn btn-outline-info me-2 mb-2">ดูข่าวสาร</a>

                        <?php elseif ($role == 'customer'): ?>
                            <p>คุณสามารถจัดการคำสั่งซื้อและบัญชีลูกค้าของคุณ</p>
                            <a href="#" class="btn btn-success me-2 mb-2">ดูคำสั่งซื้อ</a>
                            <a href="#" class="btn btn-outline-success me-2 mb-2">รายการสินค้า</a>

                        <?php elseif ($role == 'employee'): ?>
                            <p>คุณสามารถเข้าถึงข้อมูลงานและจัดการออร์เดอร์ที่เข้ามา</p>
                            <a href="#" class="btn btn-warning me-2 mb-2">ดูตารางงาน</a>
                            <a href="#" class="btn btn-outline-warning me-2 mb-2">จัดการออร์เดอร์</a>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>