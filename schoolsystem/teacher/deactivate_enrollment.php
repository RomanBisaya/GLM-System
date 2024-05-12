<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if the studentID is present
if (isset($_GET["StudentID"]) && !empty(trim($_GET["StudentID"]))) {
    $studentID = trim($_GET["StudentID"]);

    // Prepare an update statement
    $sql = "UPDATE enrollment SET IsActive = 0 WHERE StudentID = :studentID";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":studentID", $studentID, PDO::PARAM_INT);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records updated successfully. Redirect to manage enrollment page
            header("location: manage_enrollment.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    unset($stmt);
} else {
    // URL doesn't contain studentID parameter. Redirect to error page
    header("location: error.php");
    exit();
}

// Close connection
unset($pdo);
?>
