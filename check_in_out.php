<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'lecturer') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lecturer_id = $_POST['lecturer_id'];
    $check_in_time = $_POST['check_in_time'];
    $check_out_time = $_POST['check_out_time'];
    $sql = "INSERT INTO check_in_out (lecturer_id, check_in_time, check_out_time) VALUES ('$lecturer_id', '$check_in_time', '$check_out_time')";
    if ($conn->query($sql) === TRUE) {
        echo "Check-in/out time recorded successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<h1>Record Check-in/Check-out Time</h1>
<form method="post" action="check_in_out.php">
    <input type="hidden" name="lecturer_id" value="<?php echo $_SESSION['user_id']; ?>">
    <input type="datetime-local" name="check_in_time" placeholder="Check-in Time" required>
    <input type="datetime-local" name="check_out_time" placeholder="Check-out Time">
    <button type="submit">Record Time</button>
</form>
<?php include 'includes/footer.php'; ?>
