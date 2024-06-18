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
    $result = $conn->query("SELECT * FROM classrooms WHERE id='$id'");
    if ($result->num_rows > 0) {
        $classroom = $result->fetch_assoc();
    } else {
        echo "Classroom not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];

    $sql = "UPDATE classrooms SET name='$name', capacity='$capacity', equipment='$equipment' WHERE id='$id'";
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
        <form method="post" action="edit_classroom.php" class="form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($classroom['id']); ?>">
            <table>
                <tr>
                    <td><label for="name">Name:</label></td>
                    <td><input type="text" id="name" name="name" value="<?php echo htmlspecialchars($classroom['name']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="capacity">Capacity:</label></td>
                    <td><input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($classroom['capacity']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="equipment">Equipment:</label></td>
                    <td><textarea id="equipment" name="equipment" required><?php echo htmlspecialchars($classroom['equipment']); ?></textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><button type="submit">Update Classroom</button></td>
                </tr>
            </table>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
