<?php
session_start();

// Check if the student is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php"); // Adjust the path as needed
    exit;
}


require_once 'sidebar.php'; // Include the student sidebar here

?>

<div class="main-content"> <!-- Rename the class to something like 'dashboard-content' for a more general use -->
    <h1>Welcome to the Student Dashboard</h1>
    <p>Hello, <b><?php echo htmlspecialchars($_SESSION["name"] ?? 'Student'); ?></b>. Welcome back.</p>
    <!-- Additional dashboard content specific to students goes here -->
</div>

<?php require_once '../includes/footer.php'; ?>
