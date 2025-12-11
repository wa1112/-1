<?php
include('db.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $fullname = $_POST['fullname']; // เพิ่มตัวแปรสำหรับชื่อ-นามสกุล
    $password_raw = $_POST['password'];
    $role = $_POST['role'];
    
    // ตรวจสอบข้อมูลเบื้องต้น
    if (empty($username) || empty($fullname) || empty($password_raw) || empty($role)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

        // 2. ตรวจสอบว่ามี Username ซ้ำหรือไม่
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "❌ ชื่อผู้ใช้ **{$username}** มีในระบบแล้ว";
        } else {
            // 3. บันทึกผู้ใช้ใหม่ (เพิ่ม fullname เข้าไปใน INSERT statement)
            $stmt = $conn->prepare("INSERT INTO users (username, fullname, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $fullname, $password_hashed, $role);
            
            if ($stmt->execute()) {
                $success = "✅ ลงทะเบียน {$fullname} สิทธิ์ {$role} สำเร็จ! กรุณาเข้าสู่ระบบ";
            } else {
                $error = "❌ บันทึกข้อมูลไม่สำเร็จ: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก Parichat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* สไตล์: พื้นหลังไล่เฉดสีส้ม-แดง (Orange-White Theme) */
        body { background: linear-gradient(135deg, #ffc163ff 0%, #eb8b6eff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-container { background-color: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); max-width: 450px; width: 100%; }
        .register-header { 
            background: linear-gradient(90deg, #FF9800, #FF5722); /* Gradient ส้มเข้ม */
            color: white; 
            padding: 10px 0; 
            border-radius: 10px 10px 0 0; 
            margin: -40px -40px 25px -40px; 
            text-align: center; 
        }
        h2 { color: white; margin: 0; }
        .btn-orange { 
            background-color: #FF9800; /* สีส้มหลัก */
            border-color: #FF9800; 
            color: white; 
        }
        .btn-orange:hover { 
            background-color: #E65100; /* สีส้มเข้มตอน hover */
            border-color: #E65100; 
            color: white; 
        }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2 class="mb-0">📜 สมัครสมาชิก</h2>
            <small>สร้างบัญชีเพื่อเข้าสู่ระบบ</small>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อผู้ใช้ </label>
                <input type="text" class="form-control" id="username" name="username" placeholder="กรอกชื่อ" required>
            </div>
            <div class="mb-3">
                <label for="fullname" class="form-label">ชื่อเล่น</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="กรอกชื่อที่จะตั้ง" required>
            </div>
            
            <div class="mb-3">
                <label for="role" class="form-label">ประเภทผู้ใช้ </label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" disabled selected>-- เลือกประเภทผู้ใช้ --</option>
                    <option value="admin">Admin (ผู้ดูแลระบบ)</option>
                    <option value="user">User (ผู้ใช้ทั่วไป)</option>
                    <option value="customer">Customer (ลูกค้า)</option>
                    <option value="employee">Employee (พนักงาน)</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">รหัสผ่าน </label>
                <input type="password" class="form-control" id="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-purple btn-lg">สมัครสมาชิก</button>
            </div>
        </form>
        <p class="text-center mt-3 mb-0">มีบัญชีอยู่แล้ว? <a href="index.php">เข้าสู่ระบบ</a></p>
    </div>
</body>
</html>