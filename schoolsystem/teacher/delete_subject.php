<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Validate existence of id parameter
if(empty(trim($_GET["id"]))) {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}

// Prepare a delete statement
$sql = "DELETE FROM subjects WHERE SubjectID = :id";

if($stmt = $pdo->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

    // Set parameters
    $param_id = trim($_GET["id"]);

    // Attempt to execute the prepared statement
    if($stmt->execute()) {
        // Subject was deleted successfully. Redirect to subjects page
        header("location: manage_subjects.php");
        exit();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

// Close statement
unset($stmt);

// Close connection
unset($pdo);
?>
