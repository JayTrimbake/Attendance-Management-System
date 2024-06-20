<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $class_id = $_POST['class_id'];
    foreach ($_POST['attendance'] as $student_id => $status) {
        $sql = "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')
                ON DUPLICATE KEY UPDATE status='$status'";
        $conn->query($sql);
    }
    echo "Attendance recorded successfully!";
}

$class_sql = "SELECT * FROM classes";
$class_result = $conn->query($class_sql);

$students = [];
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $sql = "SELECT * FROM students WHERE class_id = $class_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Management System</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Attendance Management System</h1>
    <form method="GET" action="">
        <label>Class:</label>
        <select name="class_id" onchange="this.form.submit()">
            <option value="">Select Class</option>
            <?php while ($class = $class_result->fetch_assoc()) { ?>
            <option value="<?php echo $class['id']; ?>" <?php if (isset($class_id) && $class_id == $class['id']) echo 'selected'; ?>><?php echo $class['class_name']; ?></option>
            <?php } ?>
        </select>
    </form>
    <br>
    <?php if (!empty($students)) { ?>
    <form method="POST" action="">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <label>Date:</label>
        <input type="date" name="date" required><br><br>
        <table>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Status</th>
            </tr>
            <?php foreach ($students as $student) { ?>
            <tr>
                <td><?php echo $student['roll_no']; ?></td>
                <td><?php echo $student['name']; ?></td>
                <td>
                    <div class="radio-group">
                        <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Present" required> Present
                        <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Absent" required> Absent
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <br>
        <input type="submit" value="Submit">
    </form>
    <?php } ?>
</body>
</html>
