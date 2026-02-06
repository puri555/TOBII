<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// --- ส่วนของการ Export เป็นไฟล์ CSV ---
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=report_grades.csv');
    $output = fopen('php://output', 'w');
    // --- บรรทัดที่ต้องเพิ่มเพื่อแก้ภาษาต่างดาวใน Excel ---
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); 
    // ---------------------------------------------
    
    fputcsv($output, array('Student Name', 'Subject', 'Score')); // หัวตารางใน Excel

    $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN subjects sub ON g.subject_id = sub.id";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- ส่วนการแสดงผลหน้าเว็บ ---
?>
<!DOCTYPE html>
<html>
<head>
    <title>รายงานสรุปผลการเรียน</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .btn-export { background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>📊 รายงานสรุปผลการเรียนทั้งหมด</h2>
    <a href="?export=csv" class="btn-export">📥 ดาวน์โหลดเป็น Excel (CSV)</a>
    <a href="admin_menu.php" style="margin-left: 10px;">กลับหน้าหลัก</a>

    <table>
        <tr>
            <th>ชื่อนักเรียน</th>
            <th>รายวิชา</th>
            <th>คะแนน</th>
        </tr>
        <?php
        $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
                FROM grades g
                JOIN students s ON g.student_id = s.id
                JOIN users u ON s.user_id = u.id
                JOIN subjects sub ON g.subject_id = sub.id";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['s_name']}</td>
                    <td>{$row['sub_name']}</td>
                    <td>{$row['score']}</td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>