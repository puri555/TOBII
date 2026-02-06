<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // เลียนแบบ Pseudo Code: user = DATABASE.find_user(username)
    $sql = "SELECT id, name, role, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // เลียนแบบ Pseudo Code: IF user EXISTS AND verify_password
    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name']; // เพิ่มบรรทัดนี้
        header("Location:admin_menu.php");
    } else {
        echo "<script>alert('Invalid credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - Education Platform</title>
</head>

<body>
    <h2>Login</h2>
    <form method="post">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
        <p>ยังไม่มีบัญชีใช่ไหม? <a href="register.php">สมัครสมาชิกที่นี่</a></p>
    </form>
</body>

</html>