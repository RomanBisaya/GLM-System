<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Ensure session is started at the very top

// Check for error message in session and then clear it
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']); // Unset the error message after storing it to a variable
} else {
    $errorMessage = "";
}

include '../includes/header.php'; 
?>

<div class="login-container">
    <h2>Admin Login</h2>
    <form action="process_login.php" method="post">
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

<?php include '../includes/footer.php'; ?>
