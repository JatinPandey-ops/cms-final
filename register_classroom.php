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
$duplicate_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $capacity = $conn->real_escape_string($_POST['capacity']);
    $equipment = $conn->real_escape_string($_POST['equipment']);
    $available = $conn->real_escape_string($_POST['available']);
    $sql = "INSERT INTO classrooms (id, name, capacity, equipment, available) VALUES ('$id', '$name', '$capacity', '$equipment', '$available')";
    try {
        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Redirect to avoid form resubmission
            header('Location: register_classroom.php?success=1');
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $duplicate_error = true;
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
    <title>Register Classroom - CMS</title>
    <link rel="stylesheet" href="assets/css/register_classroom.css">
</head>
<body>
    <div class="container">
        <h1>Register Classroom</h1>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <form method="post" action="register_classroom.php" class="form">
                <input type="text" name="id" placeholder="Classroom ID" required>
                <input type="text" name="name" placeholder="Classroom Name" required>
                <input type="number" name="capacity" placeholder="Capacity" required>
                <textarea name="equipment" placeholder="Equipment" required></textarea>
                <select name="available" required>
                    <option value="1">Available</option>
                    <option value="0">Not Available</option>
                </select>
                <button type="submit">Register Classroom</button>
            </form>
            <?php 
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo "<p style='color: green;'>New classroom registered successfully</p>";
            } elseif ($duplicate_error) {
                echo "<p style='color: red;'>Error: Duplicate Classroom ID found. Please use a unique ID.</p>";
            } elseif ($error) {
                echo "<p style='color: red;'>Error registering classroom</p>";
            }
            ?>
        <?php endif; ?>

        <h2>Available Classrooms</h2>
        <?php
        $result = $conn->query("SELECT * FROM classrooms");
        if ($result->num_rows > 0) {
            echo "<table class='classrooms-table'>";
            echo "<thead><tr><th>ID</th><th>Name</th><th>Capacity</th><th>Equipment</th><th>Availability</th>";
            if ($_SESSION['role'] == 'admin') {
                echo "<th>Actions</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
                echo "<td>" . htmlspecialchars($row['equipment']) . "</td>";
                echo "<td>" . ($row['available'] ? 'Available' : 'Not Available') . "</td>";
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
