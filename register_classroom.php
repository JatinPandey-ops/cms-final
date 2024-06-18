<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Classroom - CMS</title>
    <link rel="stylesheet" href="assets/css/register_classroom.css">
</head>
<body>
    <div class="container">
        <h1>Register Classroom</h1>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <form method="post" action="register_classroom.php" class="form">
                <input type="text" name="name" placeholder="Classroom Name" required>
                <input type="number" name="capacity" placeholder="Capacity" required>
                <textarea name="equipment" placeholder="Equipment" required></textarea>
                <button type="submit">Register Classroom</button>
            </form>
        <?php endif; ?>

        <h2>Available Classrooms</h2>
        <?php
        $result = $conn->query("SELECT * FROM classrooms");
        if ($result->num_rows > 0) {
            echo "<table class='classrooms-table'>";
            echo "<thead><tr><th>Name</th><th>Capacity</th><th>Equipment</th>";
            if ($_SESSION['role'] == 'admin') {
                echo "<th>Actions</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                echo "<td>" . $row['equipment'] . "</td>";
                if ($_SESSION['role'] == 'admin') {
                    echo "<td><a href='edit_classroom.php?id=" . $row['id'] . "'>Edit</a></td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No classrooms found.</p>";
        }
        ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
