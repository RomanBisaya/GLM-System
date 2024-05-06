<?php
session_start();
require_once '../includes/config.php'; // Ensure this points to your database configuration file

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Prepare a delete statement
    $sql = "DELETE FROM offerings WHERE OfferingID = :id";
    
    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: manage_offerings.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    unset($stmt);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}

// Close connection
unset($pdo);
?>
