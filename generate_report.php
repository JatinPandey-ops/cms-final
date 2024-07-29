<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

// Fetch all relevant information
$lecturers = $conn->query("
    SELECT l.lecturer_id, l.name as lecturer_name, l.phone, l.course_code, l.course_name, l.date, l.time, l.note,
           c.name as classroom_name, cio.check_in_time, cio.check_out_time
    FROM lecturers l
    LEFT JOIN check_in_out cio ON l.lecturer_id = cio.lecturer_id
    LEFT JOIN classrooms c ON cio.classroom_id = c.id
    ORDER BY l.lecturer_id, cio.check_in_time DESC
");

// Function to generate CSV report
function generateCSV($lecturers) {
    $filename = "report_" . date('Ymd') . ".csv";
    $output = fopen("php://output", "w");
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $header = array("Lecturer ID", "Lecturer Name", "Phone", "Course Code", "Course Name", "Date", "Time", "Note", "Classroom Name", "Check-in Time", "Check-out Time");
    fputcsv($output, $header);
    
    foreach ($lecturers as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Handle CSV download request
if (isset($_POST['download_csv'])) {
    $lecturers = $conn->query("
        SELECT l.lecturer_id, l.name as lecturer_name, l.phone, l.course_code, l.course_name, l.date, l.time, l.note,
               c.name as classroom_name, cio.check_in_time, cio.check_out_time
        FROM lecturers l
        LEFT JOIN check_in_out cio ON l.lecturer_id = cio.lecturer_id
        LEFT JOIN classrooms c ON cio.classroom_id = c.id
        ORDER BY l.lecturer_id, cio.check_in_time DESC
    ");
    generateCSV($lecturers);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report - CMS</title>
    <link rel="stylesheet" href="assets/css/generate_report.css">
</head>
<body>
    <div class="container">
        <h1>Generate Report</h1>
        
        <form method="post" action="generate_report.php">
            <button type="submit" name="download_csv">Download CSV</button>
        </form>
        
        <h2>Lecturer and Classroom Usage Details</h2>
        <?php if ($lecturers->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Lecturer ID</th>
                        <th>Lecturer Name</th>
                        <th>Phone</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Note</th>
                        <th>Classroom Name</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $lecturers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['lecturer_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td><?php echo htmlspecialchars($row['note']); ?></td>
                            <td><?php echo htmlspecialchars($row['classroom_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No lecturer and classroom usage details available.</p>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
