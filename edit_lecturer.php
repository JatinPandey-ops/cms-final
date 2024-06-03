<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM lecturers WHERE id='$id'");
    if ($result->num_rows > 0) {
        $lecturer = $result->fetch_assoc();
    } else {
        echo "Lecturer not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $schedule = $_POST['schedule'];

    $sql = "UPDATE lecturers SET name='$name', phone='$phone', schedule='$schedule' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Lecturer updated successfully";
        header('Location: manage_lecturers.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h1>Edit Lecturer</h1>
<form method="post" action="edit_lecturer.php">
    <input type="hidden" name="id" value="<?php echo $lecturer['id']; ?>">
    <input type="text" name="name" value="<?php echo $lecturer['name']; ?>" required>
    <input type="text" name="phone" value="<?php echo $lecturer['phone']; ?>" required>
    <textarea name="schedule" required><?php echo $lecturer['schedule']; ?></textarea>
    <button type="submit">Update Lecturer</button>
</form>
<?php include 'includes/footer.php'; ?>
