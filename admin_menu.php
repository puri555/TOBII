<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); }
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head><title>Main Menu</title></head>
<body>
    <h1>ยินดีต้อนรับ, <?php echo $_SESSION['name']; ?> (<?php echo $role; ?>)</h1>
    <hr>
    <ul>
        <li><a href="admin_subjects.php">รายวิชา/หลักสูตร</a></li>
        
        <?php if ($role == 'teacher'): ?>
            <li><a href="enter_grade.php">บันทึกเกรดและประเมินผล</a></li>
            <li><a href="create_assignment.php">ออกข้อสอบ/สั่งการบ้าน</a></li>
            <li><a href="check_submissions.php">ตรวจงาน/เช็คการส่งงานของนักเรียน</a></li>
        <?php else: ?>
            <li><a href="enter_grade.php">ดูผลการเรียน</a></li>
            <li><a href="submit_work.php">ดูข้อสอบและส่งการบ้าน</a></li>
        <?php endif; ?>
        
        <li><a href="export_report.php">Report / Export ข้อมูล</a></li>
    </ul>
<a href="logout.php" style="color:red; font-weight:bold;">ออกจากระบบ</a> ต้องวางตรงไหนบ้าง ไฟล์ไหนบ้าง
</body>
</html>