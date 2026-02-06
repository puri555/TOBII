<?php
include 'config.php';
session_start();

// ตรวจสอบ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// --- ส่วนของครู: จัดการการบันทึกคะแนน ---
if ($role == 'teacher') {
    if (isset($_POST['submit_grade'])) {
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $sub_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
        $score = mysqli_real_escape_string($conn, $_POST['score']);

        // ตรวจสอบว่าเคยกรอกเกรดวิชานี้ให้นักเรียนคนนี้ไปหรือยัง (Update หรือ Insert)
        $check_sql = "SELECT id FROM grades WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $sql = "UPDATE grades SET score = '$score' WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        } else {
            $sql = "INSERT INTO grades (student_id, subject_id, score) VALUES ('$student_id', '$sub_id', '$score')";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('บันทึกคะแนนเรียบร้อยแล้ว');</script>";
        }
    }

    // ดึงรายชื่อนักเรียนและวิชามาใส่ใน Dropdown
    $students_list = mysqli_query($conn, "SELECT s.id, u.name FROM students s JOIN users u ON s.user_id = u.id");
    $subjects_list = mysqli_query($conn, "SELECT * FROM subjects");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการเกรดและคะแนน</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <?php if ($role == 'teacher'): ?>
        <h2>👨‍🏫 สำหรับอาจารย์: กรอกคะแนนนักเรียน</h2>
        <form method="post">
            <div class="form-group">
                <label>เลือกนักเรียน:</label>
                <select name="student_id" required>
                    <option value="">-- รายชื่อนักเรียน --</option>
                    <?php while($row = mysqli_fetch_assoc($students_list)): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>เลือกรายวิชา:</label>
                <select name="subject_id" required>
                    <option value="">-- รายชื่อวิชา --</option>
                    <?php while($row = mysqli_fetch_assoc($subjects_list)): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>คะแนน (0-100):</label>
                <input type="number" name="score" min="0" max="100" step="0.01" required>
            </div>

            <button type="submit" name="submit_grade">บันทึกคะแนน</button>
            <a href="admin_menu.php">กลับหน้าหลัก</a>
        </form>

    <?php else: ?>
        <h2>🎓 ผลการเรียนของคุณ: <?php echo $_SESSION['name']; ?></h2>
        <table>
            <thead>
                <tr>
                    <th>รายวิชา</th>
                    <th>คะแนนที่ได้</th>
                    <th>การประเมิน</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT s.name as subject_name, g.score 
                        FROM grades g 
                        JOIN subjects s ON g.subject_id = s.id 
                        WHERE g.student_id = (SELECT id FROM students WHERE user_id = '$user_id')";
                $res = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $score = $row['score'];
                        // ตรรกะตัดเกรดเบื้องต้น
                        $grade = ($score >= 50) ? "<span style='color:green;'>ผ่าน</span>" : "<span style='color:red;'>ไม่ผ่าน</span>";
                        echo "<tr>
                                <td>{$row['subject_name']}</td>
                                <td>{$score}</td>
                                <td>{$grade}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center;'>ยังไม่มีข้อมูลคะแนน</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <a href="admin_menu.php">กลับหน้าหลัก</a>
    <?php endif; ?>
</div>

</body>
</html>