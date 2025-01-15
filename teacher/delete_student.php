<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Validate the ID
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Prepare a delete statement
    $sql = "DELETE FROM Students WHERE StudentID = :id";

    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id);

        // Set parameters
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Record deleted successfully. Redirect to landing page
            header("location: manage_students.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($pdo);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
