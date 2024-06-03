<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';
?>
<h1>Monitor Check-in/Check-out Times</h1>
<?php
$result = $conn->query("SELECT * FROM check_in_out JOIN lecturers ON check_in_out.lecturer_id = lecturers.id");
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Lecturer</th><th>Check-in Time</th><th>Check-out Time</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['name'] . "</td><td>" . $row['check_in_time'] . "</td><td>" . $row['check_out_time'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No check-in/out records found.";
}
include 'includes/footer.php';
?>
