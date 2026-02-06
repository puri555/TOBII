<?php
include 'config.php';
session_start();
if ($_SESSION['role'] != 'student') { die("หน้านี้สำหรับนักเรียนเท่านั้น"); }

// (ส่วนประมวลผลการส่งงานคงเดิมจากที่คุณมี แต่เปลี่ยน $target_dir เป็น uploads/submissions/ เพื่อความระเบียบ)

$query = "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id ORDER BY a.due_date ASC";
$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .badge { padding: 3px 8px; border-radius: 4px; color: white; font-size: 12px; }
        .bg-exam { background: #dc3545; }
        .bg-work { background: #28a745; }
    </style>
</head>
<body>
    <h2>รายการงานที่ได้รับมอบหมาย</h2>
    <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
        <tr style="background:#eee;">
            <th>ประเภท</th>
            <th>วิชา (หัวข้อ)</th>
            <th>ไฟล์โจทย์</th>
            <th>กำหนดส่ง</th>
            <th>ส่งงาน</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($assignments)): ?>
        <tr>
            <td>
                <span class="badge <?php echo ($row['type'] == 'exam') ? 'bg-exam' : 'bg-work'; ?>">
                    <?php echo ($row['type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                </span>
            </td>
            <td>
                <strong><?php echo $row['subject_name']; ?></strong><br>
                <?php echo $row['title']; ?>
            </td>
            <td>
                <?php if($row['attachment_link']): ?>
                    <a href="<?php echo $row['attachment_link']; ?>" target="_blank">📄 ดาวน์โหลดโจทย์</a>
                <?php else: ?> - <?php endif; ?>
            </td>
            <td><?php echo $row['due_date']; ?></td>
            <td>
                <button onclick="document.getElementById('as_id').value='<?php echo $row['id']; ?>'">เลือกงานนี้</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h3>ฟอร์มส่งไฟล์งาน</h3>
    <form method="post" enctype="multipart/form-data">
        ID งานที่เลือก: <input type="text" id="as_id" name="assignment_id" readonly required>
        เลือกไฟล์คำตอบ: <input type="file" name="fileToUpload" required>
        <button type="submit" name="upload">ส่งงาน</button>
    </form>
</body>
</html>