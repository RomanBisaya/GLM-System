<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if an offering ID was provided in the URL as a GET parameter
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $offeringID = trim($_GET["id"]);

    // Check for dependent records in the enrollment table
    $checkSql = "SELECT COUNT(*) FROM enrollment WHERE OfferingID = :offeringID";
    if($checkStmt = $pdo->prepare($checkSql)){
        $checkStmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
        $checkStmt->execute();
        if($checkStmt->fetchColumn() > 0){
            // There are dependent records, do not delete and inform the user
            echo "Cannot delete this offering because it is being referenced in enrollments.";
            exit;
        }
    }

    // If no dependent records, proceed to delete
    $sql = "DELETE FROM offerings WHERE OfferingID = :offeringID";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
        if($stmt->execute()){
            // Record deleted successfully, redirect
            header("location: manage_offerings.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
} else {
    // URL doesn't contain id parameter, redirect to error page
    header("location: error.php");
    exit();
}

// Close connection
unset($pdo);
?>
