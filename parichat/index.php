<?php
// ตรวจสอบว่าไฟล์ db.php ถูกรวมเข้ามาและ Session ได้ถูกเริ่มแล้ว
include('db.php');

$error = '';

// *** ส่วนเสริม: ตรวจสอบสถานะการเชื่อมต่อทันที ***
if (!$conn) {
    die('<div class="alert alert-danger">❌ Error: ไม่สามารถเชื่อมต่อฐานข้อมูลได้! โปรดตรวจสอบ db.php</div>');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ป้องกัน SQL Injection โดยใช้ Prepared Statements
    // เพิ่ม fullname เข้ามาใน SELECT statement
    $stmt = $conn->prepare("SELECT id, username, password, role, fullname FROM users WHERE username = ?");

    if ($stmt === false) {
        // กรณี Prepared Statement ล้มเหลว (มักเกิดจากชื่อตารางผิด)
        $error = "Error: การเตรียมคำสั่ง SQL ล้มเหลว (โปรดตรวจสอบชื่อตาราง 'users')";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
           

            if (password_verify($password, $user['password'])) {
                // ล็อคอินสำเร็จ!
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname']; // **เก็บ fullname ใน session**
                
                header("location: dashboard.php");
                exit();
            } else {
                // รหัสผ่านไม่ตรง
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"; 
            }
        } else {
            // ไม่พบชื่อผู้ใช้
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Parichat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* สไตล์ตามภาพตัวอย่าง: พื้นหลังไล่เฉดสีม่วง-น้ำเงิน */
        body { background: linear-gradient(135deg, #1dcaffff 0%, #2575fc 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-container { background-color: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); max-width: 400px; width: 100%; }
        .login-header { background: linear-gradient(90deg, #71c6ffff, #2575fc); color: white; padding: 10px 0; border-radius: 10px 10px 0 0; margin: -40px -40px 25px -40px; text-align: center; }
        h2 { color: white; margin: 0; }
        .btn-purple { background-color: #85c4eeff; border-color: #85dbf5ff; color: white; }
        .btn-purple:hover { background-color: #04a7e7ff; border-color: #550aab; color: white; }
        .register-link { color: #0796f5ff; }
        .register-link:hover { color: #03afffff; }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2 class="mb-0">🔒 เข้าสู่ระบบ</h2>
            <small>ยินดีต้อนรับกลับมา กรุณาเข้าสู่ระบบสำหรับการจัดการ</small>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="กรอกชื่อผู้ใช้" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่าน" required>
            </div>
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-purple btn-lg">เข้าสู่ระบบ</button>
            </div>
        </form>
        
        <p class="text-center mt-3">
            ยังไม่มีบัญชีใช่ไหม? <a href="register.php" class="register-link fw-bold">สมัครสมาชิก</a>
        </p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>