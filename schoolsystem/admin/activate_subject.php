<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if an ID was provided for activation in the URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare the update statement
    $sql = "UPDATE subjects SET IsActive = 1 WHERE SubjectID = :id";

    if($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

        // Set parameters
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()) {
            // Check if the update was successful and redirect to manage subjects page
            if($stmt->rowCount() == 1) {
                header("location: manage_subjects.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($pdo);
} else {
    // If no ID was provided, redirect to the error page
    header("location: error.php");
    exit();
}
?>
