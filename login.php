<?php
session_start();
include 'db.php';

$error_message = '';

// Generate a new token and store it in the session
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['form_token'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check the token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['form_token']) {
        $error_message = "Invalid form submission.";
    } else {
        // Invalidate the token
        unset($_SESSION['form_token']);

        $username = $_POST['username'];
        $password = $_POST['password'];

        // Sanitize input
        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'lecturer') {
                $lecturer_result = $conn->query("SELECT lecturer_id FROM lecturers WHERE lecturer_id = (SELECT lecturer_id FROM users WHERE username = '$username')");
                if ($lecturer_result->num_rows > 0) {
                    $lecturer = $lecturer_result->fetch_assoc();
                    $_SESSION['lecturer_id'] = $lecturer['lecturer_id'];
                }
            }
            // Generate a new token for the next form
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Classroom Management System</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h3>Enter your account credentials</h3>
            <?php if (!empty($error_message)): ?>
                <p class="error-msg"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="post" action="login.php">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
