<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $sql = "INSERT INTO classrooms (name, capacity, equipment) VALUES ('$name', '$capacity', '$equipment')";
    if ($conn->query($sql) === TRUE) {
        echo "New classroom registered successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<h1>Register Classroom</h1>
<form method="post" action="register_classroom.php">
    <input type="text" name="name" placeholder="Classroom Name" required>
    <input type="number" name="capacity" placeholder="Capacity" required>
    <textarea name="equipment" placeholder="Equipment" required></textarea>
    <button type="submit">Register Classroom</button>
</form>
<?php include 'includes/footer.php'; ?>
