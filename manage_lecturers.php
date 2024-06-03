<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

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
                echo "New lecturer added successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['update_lecturer'])) {
        // Updating an existing lecturer
        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $schedule = $_POST['schedule'];

        $sql = "UPDATE lecturers SET name='$name', phone='$phone', schedule='$schedule' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo "Lecturer updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<h1>Manage Lecturers</h1>
<?php if ($_SESSION['role'] == 'admin'): ?>
    <form method="post" action="manage_lecturers.php">
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
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>";
        echo $row['name'] . " - " . $row['phone'] . " - " . $row['schedule'];
        if ($_SESSION['role'] == 'admin') {
            echo " <a href='edit_lecturer.php?id=" . $row['id'] . "'>Edit</a>";
        }
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "No lecturers found.";
}
include 'includes/footer.php';
?>
