<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$lecturer_id = $_SESSION['lecturer_id'];
$error = false;
$success = false;

// Generate a new token and store it in the session
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['form_token'];

// Handle check-in and check-out
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check the token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['form_token']) {
        $error = true;
        $error_message = "Invalid form submission.";
    } else {
        // Invalidate the token
        unset($_SESSION['form_token']);
        $classroom_id = $conn->real_escape_string($_POST['classroom_id']);
        $action = $conn->real_escape_string($_POST['action']);
        $timestamp = $conn->real_escape_string($_POST['timestamp']);

        if ($action == 'check_in') {
            $sql = "INSERT INTO check_in_out (lecturer_id, classroom_id, check_in_time) VALUES ('$lecturer_id', '$classroom_id', '$timestamp')";
        } elseif ($action == 'check_out') {
            $sql = "UPDATE check_in_out SET check_out_time='$timestamp' WHERE lecturer_id='$lecturer_id' AND classroom_id='$classroom_id' AND check_out_time IS NULL";
        }

        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Generate a new token for the next form
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
            $token = $_SESSION['form_token'];
        } else {
            $error = true;
        }
    }
}

// Fetch assigned classrooms
$sql = "SELECT c.id, c.name, s.day, s.time 
        FROM classrooms c
        JOIN schedule s ON c.id = s.classroom_id
        WHERE s.lecturer_id = '$lecturer_id'";
$result = $conn->query($sql);

// Fetch check-in and check-out times
$check_in_out_sql = "SELECT * FROM check_in_out WHERE lecturer_id = '$lecturer_id'";
$check_in_out_result = $conn->query($check_in_out_sql);
$check_in_out_times = [];
while ($row = $check_in_out_result->fetch_assoc()) {
    $check_in_out_times[$row['classroom_id']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Check-in/Check-out - CMS</title>
    <link rel="stylesheet" href="assets/css/record_checkIn_out.css">
    <script>
        function recordAction(action, classroom_id) {
            var timestamp = prompt("Please enter the " + action.replace('_', ' ') + " time (YYYY-MM-DD HH:MM:SS):");
            if (timestamp != null && timestamp != "") {
                document.getElementById('action').value = action;
                document.getElementById('classroom_id').value = classroom_id;
                document.getElementById('timestamp').value = timestamp;
                document.getElementById('recordForm').submit();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Record Check-in/Check-out</h1>
        <?php if ($success): ?>
            <p class="success-msg">Action recorded successfully.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error-msg">Error recording action.<?php if (isset($error_message)) echo ": " . htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Classroom</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['day']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td><?php echo isset($check_in_out_times[$row['id']]['check_in_time']) ? htmlspecialchars($check_in_out_times[$row['id']]['check_in_time']) : 'N/A'; ?></td>
                            <td><?php echo isset($check_in_out_times[$row['id']]['check_out_time']) ? htmlspecialchars($check_in_out_times[$row['id']]['check_out_time']) : 'N/A'; ?></td>
                            <td>
                                <button onclick="recordAction('check_in', '<?php echo htmlspecialchars($row['id']); ?>')">Check In</button>
                                <button onclick="recordAction('check_out', '<?php echo htmlspecialchars($row['id']); ?>')">Check Out</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No classrooms assigned.</p>
        <?php endif; ?>
    </div>
    <form id="recordForm" method="post" action="record_checkIn_out.php" style="display: none;">
        <input type="hidden" name="classroom_id" id="classroom_id">
        <input type="hidden" name="action" id="action">
        <input type="hidden" name="timestamp" id="timestamp">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
    </form>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
