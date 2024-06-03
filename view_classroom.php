<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';
?>
<h1>View Available Classrooms</h1>
<?php
$result = $conn->query("SELECT * FROM classrooms WHERE available = 1");
if ($result->num_rows > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>" . $row['name'] . " - Capacity: " . $row['capacity'] . " - Equipment: " . $row['equipment'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No available classrooms.";
}
include 'includes/footer.php';
?>
