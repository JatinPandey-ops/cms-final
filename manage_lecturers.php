<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_lecturer'])) {
        // Adding a new lecturer
        $lecturer_id = $_POST['lecturer_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $course_code = $_POST['course_code'];
        $course_name = $_POST['course_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $note = $_POST['note'];
        $username = $_POST['username'];
        $password = $_POST['password']; 
        $role = 'lecturer';

        // Sanitize input
        $lecturer_id = $conn->real_escape_string($lecturer_id);
        $name = $conn->real_escape_string($name);
        $phone = $conn->real_escape_string($phone);
        $course_code = $conn->real_escape_string($course_code);
        $course_name = $conn->real_escape_string($course_name);
        $date = $conn->real_escape_string($date);
        $time = $conn->real_escape_string($time);
        $note = $conn->real_escape_string($note);
        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);

        // Check if lecturer name already exists
        $check_lecturer = $conn->query("SELECT * FROM lecturers WHERE name = '$name'");
        if ($check_lecturer->num_rows > 0) {
            $error = true;
        } else {
            $sql = "INSERT INTO lecturers (lecturer_id, name, phone, course_code, course_name, date, time, note) VALUES ('$lecturer_id', '$name', '$phone', '$course_code', '$course_name', '$date', '$time', '$note')";
            if ($conn->query($sql) === TRUE) {
                $sql_user = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
                if ($conn->query($sql_user) === TRUE) {
                    $success = true;
                    // Redirect to prevent form resubmission
                    header('Location: manage_lecturers.php?success=1');
                    exit();
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }
    } elseif (isset($_POST['update_lecturer'])) {
        // Updating an existing lecturer
        $lecturer_id = $_POST['lecturer_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $course_code = $_POST['course_code'];
        $course_name = $_POST['course_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $note = $_POST['note'];

        // Sanitize input
        $lecturer_id = $conn->real_escape_string($lecturer_id);
        $name = $conn->real_escape_string($name);
        $phone = $conn->real_escape_string($phone);
        $course_code = $conn->real_escape_string($course_code);
        $course_name = $conn->real_escape_string($course_name);
        $date = $conn->real_escape_string($date);
        $time = $conn->real_escape_string($time);
        $note = $conn->real_escape_string($note);

        $sql = "UPDATE lecturers SET name='$name', phone='$phone', course_code='$course_code', course_name='$course_name', date='$date', time='$time', note='$note' WHERE lecturer_id='$lecturer_id'";
        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Redirect to prevent form resubmission
            header('Location: manage_lecturers.php?success=1');
            exit();
        } else {
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers - CMS</title>
    <link rel="stylesheet" href="assets/css/manage_lecturers.css">
</head>
<body>
    <div class="container">
        <h1>Manage Lecturers</h1>
        <?php 
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<h4 style='color: green; padding: 5px;'>Lecturer details saved successfully</h4>";
        } elseif ($error) {
            echo "<h4 style='color: red; padding: 5px;'>Something went wrong or lecturer name already exists!</h4>";
        }
        ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <form method="post" action="manage_lecturers.php" class="form">
                <input type="hidden" name="add_lecturer" value="1">
                <input type="text" name="lecturer_id" placeholder="Lecturer ID" required>
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="text" name="course_code" placeholder="Course Code" required>
                <input type="text" name="course_name" placeholder="Course Name" required>
                <input type="date" name="date" placeholder="Date" required>
                <input type="time" name="time" placeholder="Time" required>
                <textarea name="note" placeholder="Note"></textarea>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Add Lecturer</button>
            </form>
        <?php endif; ?>

        <h2>Lecturer List</h2>
        <?php
        $result = $conn->query("SELECT lecturer_id, name, phone, course_code, course_name, date, time, note FROM lecturers");
        if ($result->num_rows > 0) {
            echo "<table class='lecturers-table'>";
            echo "<thead><tr><th>Lecturer ID</th><th>Name</th><th>Phone</th><th>Course Code</th><th>Course Name</th><th>Date</th><th>Time</th><th>Note</th>";
            if ($_SESSION['role'] == 'admin') {
                echo "<th>Actions</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['lecturer_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['note']) . "</td>";
                if ($_SESSION['role'] == 'admin') {
                    echo "<td><a href='edit_lecturer.php?id=" . $row['lecturer_id'] . "'>Edit</a></td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No lecturers found.</p>";
        }
        ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
