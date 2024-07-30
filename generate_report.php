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

// Handle search
$search_value = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_value'])) {
    $search_value = $conn->real_escape_string($_POST['search_value']);
}

// Fetch all relevant information
$query = "
    SELECT l.lecturer_id, l.name as lecturer_name, l.phone, l.course_code, l.course_name, l.date, l.time, l.note,
           c.name as classroom_name, cio.check_in_time, cio.check_out_time, s.scheduled_time
    FROM lecturers l
    LEFT JOIN check_in_out cio ON l.lecturer_id = cio.lecturer_id
    LEFT JOIN classrooms c ON cio.classroom_id = c.id
    LEFT JOIN schedule s ON s.classroom_id = c.id AND s.lecturer_id = l.lecturer_id
";

if (!empty($search_value)) {
    $query .= " WHERE l.lecturer_id LIKE '%$search_value%' OR l.name LIKE '%$search_value%'";
}

$query .= " ORDER BY l.lecturer_id, cio.check_in_time DESC";

$lecturers = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report - CMS</title>
    <link rel="stylesheet" href="assets/css/generate_report.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

</head>
<body>
    <div class="container">
        <h1>Generate Report</h1>
        
        <form method="post" action="generate_report.php" class="form">
            <input type="text" name="search_value" placeholder="Search by Lecturer ID or Name" value="<?php echo htmlspecialchars($search_value); ?>">
            <button type="submit">Search</button>
        </form>

        <button onclick="downloadPDF()">Download PDF</button>
        
        <h2>Lecturer and Classroom Usage Details</h2>
        <?php if ($lecturers->num_rows > 0): ?>
            <table id="report_table">
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
                        <th>Scheduled Check-in Time</th>
                        <th>Scheduled Check-out Time</th>
                        <th>Actual Check-in Time</th>
                        <th>Actual Check-out Time</th>
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
                            <td><?php echo htmlspecialchars($row['scheduled_time'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['scheduled_time'] ?? 'N/A'); ?></td>
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
    <script>
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Lecturer and Classroom Usage Details", 10, 10);
            doc.autoTable({ html: '#report_table' });
            doc.save('report.pdf');
        }
    </script>
</body>
</html>
