<?php
include 'config.php';
session_start();

// 1. ตรวจสอบสิทธิ์ (Security Check)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    die("หน้านี้สำหรับครูเท่านั้น <a href='admin_menu.php'>กลับหน้าหลัก</a>");
}

// 2. คำสั่ง SQL ที่สำคัญ (ต้อง JOIN 4 ตารางเพื่อให้ได้ข้อมูลครบ)
// - submissions (การส่งงาน)
// - students/users (ข้อมูลนักเรียน)
// - assignments (โจทย์ที่ครูตั้งไว้)
$sql = "SELECT 
            s.id AS submission_id,
            u.name AS student_name,
            a.title AS task_title,
            a.type AS task_type,
            a.attachment_link AS question_file, -- ไฟล์โจทย์ที่ครูลง
            s.file_link AS student_file,        -- ไฟล์ที่นักเรียนส่ง
            s.submitted_at
        FROM submissions s
        JOIN students st ON s.student_id = st.id
        JOIN users u ON st.user_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        ORDER BY s.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบตรวจงานและข้อสอบ</title>
    <style>
        body { font-family: 'Sarabun', sans-serif; padding: 30px; background-color: #f8f9fa; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-exam { background: #dc3545; } /* สีแดงสำหรับข้อสอบ */
        .bg-homework { background: #28a745; } /* สีเขียวสำหรับการบ้าน */
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .btn-view { background: #17a2b8; color: white; }
        .btn-question { background: #6c757d; color: white; margin-right: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>✅ ตรวจรายการส่งงานและข้อสอบ</h2>
    <p>ครูผู้ตรวจ: <strong><?php echo $_SESSION['name']; ?></strong> | <a href="admin_menu.php">กลับหน้าหลัก</a></p>

    <table>
        <thead>
            <tr>
                <th>วัน-เวลาที่ส่ง</th>
                <th>ชื่อนักเรียน</th>
                <th>หัวข้อ</th>
                <th>ประเภท</th>
                <th>ไฟล์โจทย์</th>
                <th>ไฟล์งานนักเรียน</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['submitted_at'])); ?></td>
                    <td><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['task_title']; ?></td>
                    <td>
                        <span class="badge <?php echo ($row['task_type'] == 'exam') ? 'bg-exam' : 'bg-homework'; ?>">
                            <?php echo ($row['task_type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($row['question_file']): ?>
                            <a href="<?php echo $row['question_file']; ?>" target="_blank" class="btn btn-question">📄 โจทย์</a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo $row['student_file']; ?>" target="_blank" class="btn btn-view">🔍 เปิดตรวจงาน</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">ยังไม่มีข้อมูลการส่งงานในขณะนี้</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>