<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'teacher') { die("หน้านี้สำหรับครูเท่านั้น"); }

if (isset($_POST['create'])) {
    $subject_id = $_POST['subject_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type']; // รับค่าประเภท
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    
    $attachment = "";
    // ส่วนของการอัปโหลดไฟล์โจทย์จากครู
    if (!empty($_FILES["attachment"]["name"])) {
        $target_dir = "uploads/questions/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $attachment = $target_dir . time() . "_" . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment);
    }

    $sql = "INSERT INTO assignments (subject_id, title, type, description, attachment_link, due_date) 
            VALUES ('$subject_id', '$title', '$type', '$description', '$attachment', '$due_date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('ประกาศสำเร็จ!'); window.location='admin_menu.php';</script>";
    }
}
$subjects = mysqli_query($conn, "SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html>
<head><title>สร้างโจทย์ข้อสอบ/การบ้าน</title></head>
<body>
    <h2>สร้างข้อสอบ / สั่งการบ้าน</h2>
    <form method="post" enctype="multipart/form-data">
        วิชา: <select name="subject_id" required>
            <?php while($s = mysqli_fetch_assoc($subjects)): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>
        หัวข้อ: <input type="text" name="title" required><br><br>
        ประเภท: 
        <input type="radio" name="type" value="homework" checked> การบ้าน
        <input type="radio" name="type" value="exam"> ข้อสอบ <br><br>
        รายละเอียด: <textarea name="description" rows="3" cols="40"></textarea><br><br>
        ไฟล์โจทย์ (ถ้ามี): <input type="file" name="attachment"><br><br>
        กำหนดส่ง: <input type="date" name="due_date" required><br><br>
        <button type="submit" name="create">ตกลง</button>
    </form>
</body>
</html>