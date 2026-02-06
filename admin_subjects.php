<?php
include 'config.php';
session_start();
$role = $_SESSION['role'];

// ถ้าเป็นครูและมีการกดปุ่มสร้าง
if ($role == 'teacher' && isset($_POST['add_subject'])) {
    $name = $_POST['subject_name'];
    mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$name')");
    echo "สร้างหลักสูตรสำเร็จ!";
}

// ดึงข้อมูลวิชามาโชว์ (ดูได้ทุกคน)
$result = mysqli_query($conn, "SELECT * FROM subjects");
?>
<h2>รายวิชาทั้งหมด</h2>
<ul>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <li><?php echo $row['name']; ?></li>
    <?php endwhile; ?>
</ul>

<?php if ($role == 'teacher'): ?>
    <hr>
    <h3>สร้างหลักสูตรใหม่ (เฉพาะครู)</h3>
    <form method="post">
        ชื่อวิชา: <input type="text" name="subject_name" required>
        <button type="submit" name="add_subject">บันทึก</button>
    </form>
<?php endif; ?>