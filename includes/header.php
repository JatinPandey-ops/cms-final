<!DOCTYPE html>
<html>
<head>
    <title>Classroom Management System</title>
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="manage_lecturers.php">Manage Lecturers</a></li>
            <li><a href="register_classroom.php">Register Classroom</a></li>
            <li><a href="manage_classroom.php">Manage Classroom</a></li>
            <li><a href="monitor_check_in_out.php">Monitor Check-in/Check-out</a></li>
            <li><a href="generate_report.php">Generate Report</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['role'] == 'lecturer'): ?>
            <li><a href="view_classroom.php">View Available Classroom</a></li>
            <li><a href="check_in_out.php">Record Check-in/Check-out</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
