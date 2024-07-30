<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$role = $_SESSION['role'];
$lecturer_id = $_SESSION['lecturer_id'] ?? null;

function display_schedule($conn, $role, $lecturer_id = null) {
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    foreach ($days as $day) {
        echo "<h2>$day</h2>";
        $sql = "SELECT c.name as classroom_name, s.scheduled_time, s.check_in_time, s.check_out_time, l.name as lecturer_name, s.lecturer_id
                FROM schedule s
                JOIN classrooms c ON s.classroom_id = c.id
                JOIN lecturers l ON s.lecturer_id = l.lecturer_id
                WHERE s.day = '$day'";
        if ($lecturer_id) {
            $sql .= " AND s.lecturer_id = '$lecturer_id'";
        }
        $sql .= " ORDER BY s.scheduled_time";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Classroom</th>
                            <th>Scheduled Time</th>
                            <th>Check-in Time</th>
                            <th>Check-out Time</th>
                            <th>Lecturer</th>";
            if ($role == 'admin') {
                echo "<th>Actions</th>";
            }
            echo "    </tr>
                    </thead>
                    <tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['classroom_name']}</td>
                        <td>{$row['scheduled_time']}</td>
                        <td>{$row['check_in_time']}</td>
                        <td>{$row['check_out_time']}</td>
                        <td>{$row['lecturer_name']}</td>";
                if ($role == 'admin') {
                    echo "<td><a href='manage_lecturers.php?lecturer_id={$row['lecturer_id']}'>Manage Lecturer</a></td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No classes scheduled for $day.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classrooms - CMS</title>
    <link rel="stylesheet" href="assets/css/view_classrooms.css">
</head>
<body>
    <div class="container">
        <h1>View Classrooms</h1>
        <?php if ($role == 'admin'): ?>
            <form method="get" action="view_classroom.php">
                <label for="search_lecturer">Search by Lecturer Name or ID:</label>
                <input type="text" name="search_lecturer" id="search_lecturer" required>
                <button type="submit">Search</button>
            </form>
            <?php
            if (isset($_GET['search_lecturer'])) {
                $search_lecturer = $conn->real_escape_string($_GET['search_lecturer']);
                $lecturer_result = $conn->query("SELECT lecturer_id FROM lecturers WHERE name LIKE '%$search_lecturer%' OR lecturer_id LIKE '%$search_lecturer%'");
                if ($lecturer_result->num_rows > 0) {
                    $lecturer = $lecturer_result->fetch_assoc();
                    $lecturer_id = $lecturer['lecturer_id'];
                    display_schedule($conn, $role, $lecturer_id);
                } else {
                    echo "<p>No lecturer found with that name or ID.</p>";
                }
            }
            ?>
        <?php else: ?>
            <?php display_schedule($conn, $role, $lecturer_id); ?>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
