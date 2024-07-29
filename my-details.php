<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$lecturer_id = $_SESSION['lecturer_id'];
$error = false;
$success = false;

// Fetch lecturer details
$sql = "SELECT * FROM lecturers WHERE lecturer_id = '$lecturer_id'";
$result = $conn->query($sql);
$lecturer = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $course_code = $conn->real_escape_string($_POST['course_code']);
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $note = $conn->real_escape_string($_POST['note']);

    // Update lecturer details
    $sql = "UPDATE lecturers SET 
            name='$name', 
            phone='$phone', 
            course_code='$course_code', 
            course_name='$course_name', 
            date='$date', 
            time='$time', 
            note='$note' 
            WHERE lecturer_id='$lecturer_id'";

    // Update username in users table
    $user_sql = "UPDATE users SET username='$username' WHERE username='{$_SESSION['username']}'";

    if ($conn->query($sql) === TRUE && $conn->query($user_sql) === TRUE) {
        $_SESSION['username'] = $username;
        $success = true;
        // Fetch updated details
        $result = $conn->query("SELECT * FROM lecturers WHERE lecturer_id = '$lecturer_id'");
        $lecturer = $result->fetch_assoc();
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Details - CMS</title>
    <link rel="stylesheet" href="assets/css/my-details.css">
</head>
<body>
    <div class="container">
        <h1>My Details</h1>
        <?php if ($success): ?>
            <p class="success-msg">Details updated successfully.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error-msg">Error updating details.</p>
        <?php endif; ?>

        <form method="post" action="my-details.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($lecturer['NAME']); ?>" required>
            
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($lecturer['phone']); ?>">
            
            <label for="course_code">Course Code:</label>
            <input type="text" id="course_code" name="course_code" value="<?php echo htmlspecialchars($lecturer['course_code']); ?>" required>
            
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($lecturer['course_name']); ?>" required>
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($lecturer['DATE']); ?>">
            
            <label for="time">Time:</label>
            <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($lecturer['TIME']); ?>">
            
            <label for="note">Note:</label>
            <textarea id="note" name="note"><?php echo htmlspecialchars($lecturer['note']); ?></textarea>
            
            <button type="submit">Update Details</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
