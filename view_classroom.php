<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
include 'includes/header.php';

$error = false;
$success = false;

// Generate a new token and store it in the session
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['form_token'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check the token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['form_token']) {
        $error = true;
        $error_message = "Invalid form submission.";
    } else {
        // Invalidate the token
        unset($_SESSION['form_token']);

        $classroom_id = $conn->real_escape_string($_POST['classroom_id']);
        $day = $conn->real_escape_string($_POST['day']);
        $time = $conn->real_escape_string($_POST['time']);
        $check_in_time = $conn->real_escape_string($_POST['check_in_time']);
        $check_out_time = $conn->real_escape_string($_POST['check_out_time']);
        
        if ($_SESSION['role'] == 'admin') {
            $lecturer_id = $conn->real_escape_string($_POST['lecturer_id']);
        } else {
            $lecturer_id = $_SESSION['lecturer_id']; // Assuming lecturer_id is stored in the session when logged in
        }

        // Insert into check_in_out
        $sql = "INSERT INTO check_in_out (lecturer_id, classroom_id, check_in_time, check_out_time) VALUES ('$lecturer_id', '$classroom_id', '$check_in_time', '$check_out_time')";
        if ($conn->query($sql) === TRUE) {
            $check_in_out_id = $conn->insert_id;

            // Insert into schedule
            $schedule_sql = "INSERT INTO schedule (lecturer_id, classroom_id, day, time) VALUES ('$lecturer_id', '$classroom_id', '$day', '$time')";
            $conn->query($schedule_sql);

            // Insert into lecturer_classrooms
            $history_sql = "INSERT INTO lecturer_classrooms (lecturer_id, classroom_id, check_in_out_id) VALUES ('$lecturer_id', '$classroom_id', '$check_in_out_id')";
            $conn->query($history_sql);

            // Update classroom availability
            $update_sql = "UPDATE classrooms SET available = 0 WHERE id = '$classroom_id'";
            $conn->query($update_sql);

            $success = true;
            // Generate a new token for the next form
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
            $token = $_SESSION['form_token'];
        } else {
            $error = true;
        }
    }
}

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = "AND (name LIKE '%$search%' OR id LIKE '%$search%')";
}

// Fetch available classrooms
$result = $conn->query("SELECT * FROM classrooms WHERE available = 1 $search_query");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Available Classrooms - CMS</title>
    <link rel="stylesheet" href="assets/css/view_classrooms.css">
</head>
<body>
    <div class="container">
        <h1>View Available Classrooms</h1>
        <form method="get" action="view_classroom.php">
            <input type="text" name="search" placeholder="Search by Name or ID" value="<?php if (isset($search)) echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <?php if ($success): ?>
            <p class='success-msg'>Classroom assigned successfully</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class='error-msg'>Error assigning classroom<?php if (isset($error_message)) echo ": " . htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php
        if ($result->num_rows > 0) {
            echo "<ul class='classroom-list'>";
            while($row = $result->fetch_assoc()) {
                echo "<li><strong>" . htmlspecialchars($row['NAME']) . "</strong><br>Capacity: " . htmlspecialchars($row['capacity']) . "<br>Equipment: " . htmlspecialchars($row['equipment']);
                echo "<form method='post' action='view_classrooms.php'>
                        <input type='hidden' name='classroom_id' value='" . htmlspecialchars($row['id']) . "'>
                        <input type='hidden' name='token' value='$token'>
                        <label for='day'>Day:</label>
                        <select name='day' required>
                            <option value='Monday'>Monday</option>
                            <option value='Tuesday'>Tuesday</option>
                            <option value='Wednesday'>Wednesday</option>
                            <option value='Thursday'>Thursday</option>
                            <option value='Friday'>Friday</option>
                            <option value='Saturday'>Saturday</option>
                            <option value='Sunday'>Sunday</option>
                        </select>
                        <label for='time'>Time:</label>
                        <input type='time' name='time' required>
                        <label for='check_in_time'>Check-in Time:</label>
                        <input type='datetime-local' name='check_in_time' required>
                        <label for='check_out_time'>Check-out Time:</label>
                        <input type='datetime-local' name='check_out_time' required>";
                if ($_SESSION['role'] == 'admin') {
                    echo "<label for='lecturer_id'>Lecturer:</label>
                          <select name='lecturer_id' required>";
                    $lecturer_result = $conn->query("SELECT lecturer_id, name FROM lecturers");
                    while ($lecturer = $lecturer_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($lecturer['lecturer_id']) . "'>" . htmlspecialchars($lecturer['name']) . "</option>";
                    }
                    echo "</select>";
                }
                echo "<button type='submit'>Assign</button>
                      </form>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No available classrooms.</p>";
        }

        // Display assigned classrooms
        if ($_SESSION['role'] == 'lecturer') {
            if (isset($_SESSION['lecturer_id'])) {
                $assigned_result = $conn->query("SELECT c.name, m.check_in_time, m.check_out_time, s.day, s.time FROM check_in_out m JOIN classrooms c ON m.classroom_id = c.id JOIN schedule s ON m.classroom_id = s.classroom_id WHERE m.lecturer_id = '" . $_SESSION['lecturer_id'] . "'");
                if ($assigned_result->num_rows > 0) {
                    echo "<h2>My Assigned Classrooms</h2>";
                    echo "<ul class='classroom-list'>";
                    while($row = $assigned_result->fetch_assoc()) {
                        echo "<li><strong>" . htmlspecialchars($row['name']) . "</strong><br>Day: " . htmlspecialchars($row['day']) . "<br>Time: " . htmlspecialchars($row['time']) . "<br>Check-in: " . htmlspecialchars($row['check_in_time']) . "<br>Check-out: " . htmlspecialchars($row['check_out_time']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No assigned classrooms.</p>";
                }
            } else {
                echo "<p>Lecturer ID is not set in the session.</p>";
            }
        }
        ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
