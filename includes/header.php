<!DOCTYPE html>
<html>
<head>
    <title>Classroom Management System</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <script src="assets/js/header.js" defer></script>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="hamburger-menu" id="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <h2 id="title">Classroom Management System</h2>
        </div>
        <div class="header-right">
            <p>BY: Sarmillah Somasundaram</p>
        </div>
    </header>
    <nav id="nav-menu">
        <div class="close-menu" id="close-menu">&times;</div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li><a href="manage_lecturers.php">Manage Lecturers</a></li>
                <li><a href="register_classroom.php">Register Classroom</a></li>
                <li><a href="monitor_check_in_out.php">Monitor Check-in/Check-out</a></li>
                <li><a href="generate_report.php">Generate Report</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] == 'lecturer'): ?>
                <li><a href="my-details.php" class="btn">Edit details</a></li>
                <li><a href="view_classroom.php">View Available Classroom</a></li>
                <li><a href="record_checkIn_out.php" class="btn">Record Check In/Checkout time</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>
