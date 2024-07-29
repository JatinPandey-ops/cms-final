<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CMS</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="container">
        <div class="menu_container">

            <h1>Dashboard</h1>
            <ul class="menu">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li><a href="manage_lecturers.php" class="btn">Manage Lecturers</a></li>
                    <li><a href="register_classroom.php" class="btn">Register Classroom</a></li>
                    <li><a href="view_classroom.php" class="btn">View Available Classroom</a></li>
                    <li><a href="monitor_check_in_out.php" class="btn">Monitor Check-in/Check-out</a></li>
                    <li><a href="generate_report.php" class="btn">Generate Report</a></li>
                    
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] == 'lecturer'): ?>
                        <li><a href="view_classroom.php" class="btn">View Available Classroom</a></li>
                
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

