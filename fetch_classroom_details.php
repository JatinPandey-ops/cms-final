<?php
include 'db.php';

if (isset($_GET['id'])) {
    $classroom_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT c.*, s.lecturer_id, s.scheduled_time, s.check_in_time, s.check_out_time, s.day, l.name as lecturer_name 
            FROM classrooms c 
            LEFT JOIN schedule s ON c.id = s.classroom_id 
            LEFT JOIN lecturers l ON s.lecturer_id = l.lecturer_id 
            WHERE c.id = '$classroom_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $current_time = date('H:i:s');
        if (isset($data['scheduled_time']) && isset($data['day']) && $data['scheduled_time'] <= $current_time && $data['day'] == date('l')) {
            $data['is_busy'] = true;
        } else {
            $data['is_busy'] = false;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Classroom not found']);
    }
} else {
    echo json_encode(['error' => 'No classroom ID provided']);
}
