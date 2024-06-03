<?php
// Ensure the session is started to access session variables
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Classroom Management System. All rights reserved.</p>
        <p>Logged in as: <?php echo $_SESSION['username']; ?></p>
    </div>
</footer>
</body>
</html>
