<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // ในงานจริงควรใช้ password_hash
    $role = $_POST['role'];

    // 1. ตรวจสอบ Email ซ้ำ
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email นี้มีในระบบแล้ว');</script>";
    } else {
        // 2. บันทึกลงตาราง users
        $sql_user = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql_user)) {
            $user_id = mysqli_insert_id($conn); // ดึง ID ล่าสุดที่เพิ่งสร้าง
            
            // 3. บันทึกลงตารางแยกตาม Role ตาม Schema
            if ($role == 'student') {
                mysqli_query($conn, "INSERT INTO students (user_id, class_level) VALUES ('$user_id', 'Unassigned')");
            } else if ($role == 'teacher') {
                mysqli_query($conn, "INSERT INTO teachers (user_id, department) VALUES ('$user_id', 'General')");
            }
            
            echo "<script>alert('ลงทะเบียนสำเร็จ'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>สมัครสมาชิก - Education Platform</title></head>
<body>
    <h2>ลงทะเบียนนักเรียน/ครู (ข้อ 2.2.1)</h2>
    <form method="post">
        ชื่อ-นามสกุล: <input type="text" name="name" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        บทบาท: 
        <select name="role">
            <option value="student">นักเรียน</option>
            <option value="teacher">ครู</option>
            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
        </select><br><br>
        <button type="submit" name="register">สมัครสมาชิก</button>
        <a href="login.php">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
    </form>
</body>
</html>