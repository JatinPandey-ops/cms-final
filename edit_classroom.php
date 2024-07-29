<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if (isset($_GET['id'])) {
    $classroom_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM classrooms WHERE id='$classroom_id'");
    if ($result->num_rows > 0) {
        $classroom = $result->fetch_assoc();
    } else {
        echo "Classroom not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classroom_id = $_POST['classroom_id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $available = $_POST['available'];

    $sql = "UPDATE classrooms SET name='$name', capacity='$capacity', equipment='$equipment', available='$available' WHERE id='$classroom_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Classroom updated successfully";
        header('Location: register_classroom.php');
        exit();
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
    <title>Edit Classroom - CMS</title>
    <link rel="stylesheet" href="assets/css/edit_classroom.css">
</head>
<body>
    <div class="container">
        <h1>Edit Classroom</h1>
        <form method="post" action="edit_classroom.php">
            <input type="hidden" name="classroom_id" value="<?php echo htmlspecialchars($classroom['id']); ?>">
            <label for="name">Classroom Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($classroom['NAME']); ?>" required>
            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($classroom['capacity']); ?>" required>
            <label for="equipment">Equipment:</label>
            <textarea id="equipment" name="equipment" required><?php echo htmlspecialchars($classroom['equipment']); ?></textarea>
            <label for="available">Availability:</label>
            <select id="available" name="available" required>
                <option value="1" <?php if ($classroom['available']) echo 'selected'; ?>>Available</option>
                <option value="0" <?php if (!$classroom['available']) echo 'selected'; ?>>Not Available</option>
            </select>
            <button type="submit">Update Classroom</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
