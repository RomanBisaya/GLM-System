<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}


require_once 'sidebar.php'; // Include the sidebar here
?>

<div class="admin-content">
    <h1>Welcome to the Admin Dashboard</h1>
    <p>Hello, <b><?php echo htmlspecialchars($_SESSION["name"] ?? 'Admin'); ?></b>. Welcome back.</p>
    <!-- Additional dashboard content goes here -->
</div>

<?php require_once '../includes/footer.php'; ?>
