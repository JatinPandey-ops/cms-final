<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_value = $conn->real_escape_string($_POST['search_value']);
    
    $query = "SELECT 
                c.id as classroom_id, 
                c.name as classroom_name, 
                l.lecturer_id, 
                l.name as lecturer_name, 
                l.course_code, 
                l.course_name, 
                s.scheduled_time, 
                s.check_in_time as scheduled_check_in_time,
                s.check_out_time as scheduled_check_out_time,
                cio.check_in_time, 
                cio.check_out_time
              FROM check_in_out cio
              JOIN classrooms c ON cio.classroom_id = c.id
              JOIN lecturers l ON cio.lecturer_id = l.lecturer_id
              JOIN schedule s ON s.classroom_id = c.id AND s.lecturer_id = l.lecturer_id
              WHERE l.lecturer_id LIKE '%$search_value%' OR l.name LIKE '%$search_value%'
              ORDER BY cio.check_in_time DESC";
    
    $result = $conn->query($query);
} else {
    // Fetch all check-in and check-out details by default
    $result = $conn->query("
        SELECT c.id as classroom_id, c.name as classroom_name, l.lecturer_id, l.name as lecturer_name, l.course_code, l.course_name, s.scheduled_time, s.check_in_time as scheduled_check_in_time, s.check_out_time as scheduled_check_out_time, cio.check_in_time, cio.check_out_time
        FROM check_in_out cio
        JOIN classrooms c ON cio.classroom_id = c.id
        JOIN lecturers l ON cio.lecturer_id = l.lecturer_id
        JOIN schedule s ON s.classroom_id = c.id AND s.lecturer_id = l.lecturer_id
        ORDER BY cio.check_in_time DESC
    ");
}
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
    
    <div class="main-container">
        <div class="container">
            <h1>Monitor Check In and Out</h1>

            <form method="post" action="monitor_check_in_out.php">
                <label for="search_value">Search by Lecturer ID or Name:</label>
                <input type="text" name="search_value" id="search_value" required>
                <button type="submit">Search</button>
            </form>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Classroom ID</th>
                            <th>Classroom Name</th>
                            <th>Lecturer ID</th>
                            <th>Lecturer Name</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Scheduled Check-in Time</th>
                            <th>Scheduled Check-out Time</th>
                            <th>Check-in Time</th>
                            <th>Check-out Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['classroom_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['classroom_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['lecturer_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['scheduled_check_in_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['scheduled_check_out_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No records found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
