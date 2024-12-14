<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Success</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>

    <div class="teacher-content">
        <h2>Enrollment Success</h2>
        <p>Enrollment successful.</p>
        <a href="add_enrollment.php">Back to Enrollment</a> | <a href="dashboard.php">Go to Dashboard</a>
    </div>

    <?php 
    // Ensure the footer path is correct relative to the current file's location
    include_once '../includes/footer.php'; 
    ?>

</body>
</html>
