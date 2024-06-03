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
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $schedule = $_POST['schedule'];
        $username = $_POST['username'];
        $password = $_POST['password']; 
        $role = 'lecturer';

        $sql = "INSERT INTO lecturers (name, phone, schedule) VALUES ('$name', '$phone', '$schedule')";
        if ($conn->query($sql) === TRUE) {
            $lecturer_id = $conn->insert_id; // Get the last inserted ID
            $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
            if ($conn->query($sql) === TRUE) {
                $success = true;
                // echo "New lecturer added successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error = true;
            // echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['update_lecturer'])) {
        // Updating an existing lecturer
        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $schedule = $_POST['schedule'];

        $sql = "UPDATE lecturers SET name='$name', phone='$phone', schedule='$schedule' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $success = true;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
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
        if($success == true) {
          echo "<h4 style='color : green ; padding : 5px'>Lecturer details saved successfully</h4>";
        }elseif($error == true) {
            echo "<h4 style='color : red; padding : 5px'>Something went wrong!!!</h4>";
        };
        ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <form method="post" action="manage_lecturers.php" class="form">
                <input type="hidden" name="add_lecturer" value="1">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <textarea name="schedule" placeholder="Schedule" required></textarea>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Add Lecturer</button>
            </form>
        <?php endif; ?>

        <?php
        $result = $conn->query("SELECT * FROM lecturers");
        if ($result->num_rows > 0) {
            echo "<table class='lecturers-table'>";
            echo "<thead><tr><th>Name</th><th>Phone</th><th>Schedule</th>";
            if ($_SESSION['role'] == 'admin') {
                echo "<th>Actions</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "<td>" . $row['schedule'] . "</td>";
                if ($_SESSION['role'] == 'admin') {
                    echo "<td><a href='edit_lecturer.php?id=" . $row['id'] . "'>Edit</a></td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No lecturers found.</p>";
        }
        ?>
    </div>
    <?php
    
    include 'includes/footer.php';
    ?>
</body>
</html>
