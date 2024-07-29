<?php
// Ensure the session is started to access session variables
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Management System</title>
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body>
    
    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date("Y"); ?> Classroom Management System. All rights reserved.</p>
            <p>Logged in as: <?php echo $_SESSION['username']; ?></p>
        </div>
    </footer>
    </body>
    </html>
</body>
</html>
