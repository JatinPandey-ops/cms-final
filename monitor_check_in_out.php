<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

// Fetch current busy classrooms
$current_time = date('Y-m-d H:i:s');
$busy_classrooms = $conn->query("
    SELECT c.id as classroom_id, c.name as classroom_name, l.lecturer_id, l.name as lecturer_name, cio.check_in_time, cio.check_out_time
    FROM check_in_out cio
    JOIN classrooms c ON cio.classroom_id = c.id
    JOIN lecturers l ON cio.lecturer_id = l.lecturer_id
    WHERE cio.check_in_time <= '$current_time' AND (cio.check_out_time IS NULL OR cio.check_out_time >= '$current_time')
");

// Fetch all check-in and check-out details
$check_in_out_details = $conn->query("
    SELECT c.id as classroom_id, c.name as classroom_name, l.lecturer_id, l.name as lecturer_name, cio.check_in_time, cio.check_out_time
    FROM check_in_out cio
    JOIN classrooms c ON cio.classroom_id = c.id
    JOIN lecturers l ON cio.lecturer_id = l.lecturer_id
    ORDER BY cio.check_in_time DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Check In and Out - CMS</title>
    <link rel="stylesheet" href="assets/css/monitor_check_in_out.css">
</head>
<body>
    <div class="container">
        <h1>Monitor Check In and Out</h1>
        
        <h2>Currently Busy Classrooms</h2>
        <?php if ($busy_classrooms->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Classroom ID</th>
                        <th>Classroom Name</th>
                        <th>Lecturer ID</th>
                        <th>Lecturer Name</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $busy_classrooms->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['classroom_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['classroom_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No classrooms are currently busy.</p>
        <?php endif; ?>

        <h2>All Check In and Out Details</h2>
        <?php if ($check_in_out_details->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Classroom ID</th>
                        <th>Classroom Name</th>
                        <th>Lecturer ID</th>
                        <th>Lecturer Name</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $check_in_out_details->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['classroom_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['classroom_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No check-in and check-out details available.</p>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
