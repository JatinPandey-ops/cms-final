<?php
session_start();
include 'db.php';
$isInvalid = false;
$noUser = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {  // Plain text comparison
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];
            header('Location: dashboard.php');
            exit();
        } else {
            $isInvalid = true;
        }
    } else {
       $noUser = true;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMS</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h3>Enter your account credentials</h3>
            <?php
            if($isInvalid == true) {
                
                echo "<h3 class='error'>Invalid Password</h3>";
            }elseif($noUser == true) {
                echo "<h3 class='error'>No user found!!</h3>";
            }else {
               echo "";
            }
            ?>
            <form method="post" action="login.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

