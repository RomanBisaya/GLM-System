<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php'; // Make sure to provide the correct path to your config file

// Check existence of id parameter before processing further
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get URL parameter
    $studentID = trim($_GET["id"]);

    // Prepare an update statement
    $sql = "UPDATE Students SET Status = 'Active' WHERE StudentID = :studentID";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":studentID", $param_id, PDO::PARAM_INT);

        // Set parameters
        $param_id = $studentID;

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records updated successfully. Redirect to landing page
            header("location: manage_students.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($pdo);
} else {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
