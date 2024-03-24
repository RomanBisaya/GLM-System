<?php
session_start();

// Check if the teacher is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php"); // Adjust the path as needed
    exit;
}

// Adjust the path as needed for header and sidebar2
require_once '../includes/header.php';
require_once 'sidebar2.php'; // Include the alternative sidebar here
?>

<div class="teacher-content"> <!-- You might want to rename this class to something more general like 'dashboard-content' -->
    <h1>Welcome to the Teacher Dashboard</h1>
    <p>Hello, <b><?php echo htmlspecialchars($_SESSION["name"] ?? 'Teacher'); ?></b>. Welcome back.</p>
    <!-- Additional dashboard content specific to teachers goes here -->
</div>

<?php require_once '../includes/footer.php'; ?>

