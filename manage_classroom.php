<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle updates here based on what action is chosen
}
?>
<h1>Manage Classroom</h1>
<form method="post" action="manage_classroom.php">
    <!-- Options for managing classrooms like updating availability, editing details, etc. -->
</form>
<?php include 'includes/footer.php'; ?>
