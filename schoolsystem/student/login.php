<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start session at the very top

// Check for error message in session and then clear it
if (isset($_SESSION['student_error'])) {
    $errorMessage = $_SESSION['student_error'];
    unset($_SESSION['student_error']); // Clear the error message after using it
} else {
    $errorMessage = "";
}

include '../includes/header.php'; // Include the header file. Adjust the path if necessary.
?>

<div class="login-container">
    <h2>Student Login</h2>
    <form action="process_student_login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
    <?php if (!empty($errorMessage)): ?>
    <script>alert('<?php echo $errorMessage; ?>');</script>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; // Include the footer file. Adjust the path if necessary. ?>
