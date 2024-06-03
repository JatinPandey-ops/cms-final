<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'includes/header.php';
?>
<h1>Welcome to Classroom Management System</h1>
<p>Select an option from the menu.</p>
<?php include 'includes/footer.php'; ?>
