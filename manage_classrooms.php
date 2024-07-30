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

// Generate a new token and store it in the session if it doesn't exist
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['form_token'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check the token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['form_token']) {
        $error = true;
        $error_message = "Invalid form submission.";
    } else {
        // Invalidate the token after use
        unset($_SESSION['form_token']);

        if (isset($_POST['delete_schedule_id'])) {
            // Handle delete action
            $schedule_id = $conn->real_escape_string($_POST['delete_schedule_id']);
            $delete_sql = "DELETE FROM schedule WHERE id='$schedule_id'";
            if ($conn->query($delete_sql) === TRUE) {
                $success = true;
                // Generate a new token for the next form
                $_SESSION['form_token'] = bin2hex(random_bytes(32));
                $token = $_SESSION['form_token'];
            } else {
                $error = true;
                $error_message = "Error deleting schedule: " . $conn->error;
            }
        } else {
            // Handle update action
            $classroom_id = $conn->real_escape_string($_POST['classroom_id'] ?? '');
            $lecturer_id = $conn->real_escape_string($_POST['lecturer_id'] ?? '');
            $scheduled_time = $conn->real_escape_string($_POST['scheduled_time'] ?? '');
            $check_in_time = $conn->real_escape_string($_POST['check_in_time'] ?? '');
            $check_out_time = $conn->real_escape_string($_POST['check_out_time'] ?? '');
            $day = $conn->real_escape_string($_POST['day'] ?? '');
            $capacity = $conn->real_escape_string($_POST['capacity'] ?? '');
            $equipment = $conn->real_escape_string($_POST['equipment'] ?? '');
            $available = $conn->real_escape_string($_POST['available'] ?? '');

            if ($classroom_id && $lecturer_id && $scheduled_time && $check_in_time && $check_out_time && $day && $capacity && $equipment !== '' && $available !== '') {
                // Check for time conflicts
                $conflict_sql = "SELECT * FROM schedule WHERE classroom_id='$classroom_id' AND day='$day' AND (
                                    ('$check_in_time' < check_out_time AND '$check_out_time' > check_in_time) OR
                                    ('$check_in_time' < scheduled_time AND '$check_out_time' > scheduled_time) OR
                                    ('$check_in_time' >= scheduled_time AND '$check_in_time' < check_out_time)
                                )";
                $conflict_result = $conn->query($conflict_sql);

                if ($conflict_result->num_rows > 0) {
                    $error = true;
                    $error_message = "Classroom is already scheduled at the selected time.";
                } else {
                    $sql = "UPDATE classrooms SET capacity='$capacity', equipment='$equipment', available='$available' WHERE id='$classroom_id'";
                    if ($conn->query($sql) === TRUE) {
                        $schedule_sql = "INSERT INTO schedule (lecturer_id, classroom_id, scheduled_time, check_in_time, check_out_time, day) 
                                        VALUES ('$lecturer_id', '$classroom_id', '$scheduled_time', '$check_in_time', '$check_out_time', '$day') 
                                        ON DUPLICATE KEY UPDATE lecturer_id='$lecturer_id', scheduled_time='$scheduled_time', check_in_time='$check_in_time', check_out_time='$check_out_time', day='$day'";
                        if ($conn->query($schedule_sql) === TRUE) {
                            $success = true;
                            // Generate a new token for the next form
                            $_SESSION['form_token'] = bin2hex(random_bytes(32));
                            $token = $_SESSION['form_token'];
                        } else {
                            $error = true;
                            $error_message = "Error updating schedule: " . $conn->error;
                        }
                    } else {
                        $error = true;
                        $error_message = "Error updating classroom: " . $conn->error;
                    }
                }
            } else {
                $error = true;
                $error_message = "All fields are required.";
            }
        }
    }
}

// Fetch classrooms
$classrooms_result = $conn->query("SELECT * FROM classrooms");

// Fetch lecturers
$lecturers_result = $conn->query("SELECT lecturer_id, name FROM lecturers");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classrooms - CMS</title>
    <link rel="stylesheet" href="assets/css/manage_classrooms.css">
</head>
<body>
    <div class="container">
        <h1>Manage Classrooms</h1>
        <?php if ($success): ?>
            <p class='success-msg'>Action completed successfully.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class='error-msg'><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="post" action="manage_classrooms.php">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <label for="classroom_id">Select Classroom:</label>
            <select name="classroom_id" id="classroom_id" required onchange="fetchClassroomDetails(this.value)">
                <option value="">Select a classroom</option>
                <?php while ($row = $classrooms_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <div id="classroom_details" style="display:none;">
                <div id="classroom_info"></div>
                <label for="lecturer_id">Lecturer:</label>
                <select name="lecturer_id" id="lecturer_id" required>
                    <?php while ($row = $lecturers_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['lecturer_id']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="scheduled_time">Scheduled Time:</label>
                <input type="time" name="scheduled_time" id="scheduled_time" required>

                <label for="check_in_time">Check-in Time:</label>
                <input type="time" name="check_in_time" id="check_in_time" required>

                <label for="check_out_time">Check-out Time:</label>
                <input type="time" name="check_out_time" id="check_out_time" required>

                <label for="day">Day:</label>
                <select name="day" id="day" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>

                <label for="capacity">Capacity:</label>
                <input type="number" name="capacity" id="capacity" required>

                <label for="equipment">Equipment:</label>
                <textarea name="equipment" id="equipment" required></textarea>

                <label for="available">Available:</label>
                <select name="available" id="available" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>

                <button type="submit">Save</button>
            </div>
        </form>

        <label for="filter_lecturer">Filter by Lecturer:</label>
        <select name="filter_lecturer" id="filter_lecturer" onchange="filterSchedule(this.value)">
            <option value="">All</option>
            <?php
            $lecturers_result = $conn->query("SELECT name FROM lecturers");
            while ($row = $lecturers_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['name']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <table>
            <thead>
                <tr>
                    <th>Classroom</th>
                    <th>Lecturer</th>
                    <th>Scheduled Time</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Day</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="schedule_table">
                <?php
                $result = $conn->query("SELECT c.name AS classroom_name, l.name AS lecturer_name, s.id as schedule_id, s.scheduled_time, s.check_in_time, s.check_out_time, s.day, c.capacity, c.equipment, c.available FROM classrooms c LEFT JOIN schedule s ON c.id = s.classroom_id LEFT JOIN lecturers l ON s.lecturer_id = l.lecturer_id");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['classroom_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['scheduled_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['day']); ?></td>
                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                        <td><?php echo htmlspecialchars($row['equipment']); ?></td>
                        <td><?php echo htmlspecialchars($row['available'] ? 'Yes' : 'No'); ?></td>
                        <td>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <form method="post" action="manage_classrooms.php" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                    <input type="hidden" name="delete_schedule_id" value="<?php echo $row['schedule_id']; ?>">
                                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function fetchClassroomDetails(classroomId) {
            if (classroomId) {
                // Fetch classroom details via AJAX and display the form
                fetch(`fetch_classroom_details.php?id=${classroomId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            document.getElementById('classroom_info').innerHTML = `<p class="error-msg">${data.error}</p>`;
                            document.getElementById('classroom_details').style.display = 'none';
                        } else if (data.is_busy) {
                            document.getElementById('classroom_info').innerHTML = `
                                <p class="error-msg">Classroom is currently busy</p>
                                <p><strong>ID:</strong> ${data.id}</p>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Capacity:</strong> ${data.capacity}</p>
                                <p><strong>Equipment:</strong> ${data.equipment}</p>
                                <p><strong>Lecturer:</strong> ${data.lecturer_name}</p>
                                <p><strong>Scheduled Time:</strong> ${data.scheduled_time}</p>
                                <p><strong>Day:</strong> ${data.day}</p>
                            `;
                            document.getElementById('classroom_details').style.display = 'none';
                        } else {
                            document.getElementById('classroom_info').innerHTML = `
                                <p><strong>ID:</strong> ${data.id}</p>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Capacity:</strong> ${data.capacity}</p>
                                <p><strong>Equipment:</strong> ${data.equipment}</p>
                            `;
                            document.getElementById('lecturer_id').value = data.lecturer_id || '';
                            document.getElementById('scheduled_time').value = data.scheduled_time || '';
                            document.getElementById('check_in_time').value = data.check_in_time || '';
                            document.getElementById('check_out_time').value = data.check_out_time || '';
                            document.getElementById('day').value = data.day || '';
                            document.getElementById('capacity').value = data.capacity;
                            document.getElementById('equipment').value = data.equipment;
                            document.getElementById('available').value = data.available ? 1 : 0;
                            document.getElementById('classroom_details').style.display = 'flex';
                        }
                    })
                    .catch(error => console.error('Error fetching classroom details:', error));
            } else {
                document.getElementById('classroom_details').style.display = 'none';
            }
        }

        function filterSchedule(lecturerName) {
            const rows = document.querySelectorAll('#schedule_table tr');
            rows.forEach(row => {
                const lecturerCell = row.querySelector('td:nth-child(2)');
                if (lecturerName === "" || lecturerCell.textContent === lecturerName) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
