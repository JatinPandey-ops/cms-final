<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if (isset($_GET['id'])) {
    $lecturer_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM lecturers WHERE lecturer_id='$lecturer_id'");
    if ($result->num_rows > 0) {
        $lecturer = $result->fetch_assoc();
        // Fetch the associated user details
        $user_result = $conn->query("SELECT username FROM users WHERE username=(SELECT username FROM lecturers WHERE lecturer_id='$lecturer_id')");
        $user = $user_result->fetch_assoc();
    } else {
        echo "Lecturer not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Sanitize input
    $name = $conn->real_escape_string($name);
    $phone = $conn->real_escape_string($phone);
    $course_code = $conn->real_escape_string($course_code);
    $course_name = $conn->real_escape_string($course_name);
    $date = $conn->real_escape_string($date);
    $time = $conn->real_escape_string($time);
    $note = $conn->real_escape_string($note);
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    $sql = "UPDATE lecturers SET name='$name', phone='$phone', course_code='$course_code', course_name='$course_name', date='$date', time='$time', note='$note' WHERE lecturer_id='$lecturer_id'";
    if ($conn->query($sql) === TRUE) {
        // Update username and password in users table if provided
        if (!empty($username)) {
            $sql_user = "UPDATE users SET username='$username' WHERE username='{$user['username']}'";
            $conn->query($sql_user);
        }
        if (!empty($password)) {
            $sql_password = "UPDATE users SET password='$password' WHERE username='$username'";
            $conn->query($sql_password);
        }

        // Redirect to prevent form resubmission
        header('Location: manage_lecturers.php?success=1');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lecturer - CMS</title>
    <link rel="stylesheet" href="assets/css/edit_lecturer.css">
</head>
<body>
    <div class="container">
        <h1>Edit Lecturer</h1>
        <form method="post" action="edit_lecturer.php?id=<?php echo htmlspecialchars($lecturer['lecturer_id']); ?>" class="form">
            <input type="hidden" name="lecturer_id" value="<?php echo htmlspecialchars($lecturer['lecturer_id']); ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Name" value="<?php echo htmlspecialchars($lecturer['name']); ?>" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($lecturer['phone']); ?>" required>
            <label for="course_code">Course Code:</label>
            <input type="text" id="course_code" name="course_code" placeholder="Course Code" value="<?php echo htmlspecialchars($lecturer['course_code']); ?>" required>
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" placeholder="Course Name" value="<?php echo htmlspecialchars($lecturer['course_name']); ?>" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($lecturer['date']); ?>">
            <label for="time">Time:</label>
            <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($lecturer['time']); ?>">
            <label for="note">Note:</label>
            <textarea id="note" name="note" placeholder="Note"><?php echo htmlspecialchars($lecturer['note']); ?></textarea>
            <label for="username">Username (Leave blank if not changing):</label>
            <input type="text" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>">
            <label for="password">Password (Leave blank if not changing):</label>
            <input type="password" id="password" name="password" placeholder="Password">
            <button type="submit">Update Lecturer</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
