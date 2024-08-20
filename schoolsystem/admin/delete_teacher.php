<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php'; // Database connection

// Check if TeacherID is provided in the URL
if (isset($_GET["TeacherID"]) && !empty(trim($_GET["TeacherID"]))) {
    $teacherID = trim($_GET["TeacherID"]);

    // Prepare a delete statement
    $sql = "DELETE FROM Teachers WHERE TeacherID = :teacherID";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind the TeacherID to the statement
        $stmt->bindParam(":teacherID", $teacherID, PDO::PARAM_INT);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to manage_teachers.php with success message
            header("location: manage_teachers.php?status=deleted");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
} else {
    // Redirect to an error page or manage_teachers.php if no TeacherID was provided
    header("location: manage_teachers.php");
    exit();
}

unset($pdo);
?>
