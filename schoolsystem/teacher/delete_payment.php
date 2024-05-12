<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php'; // Include the database configuration file

// Ensure there is a PaymentID in the URL
if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    // Redirect to the error page if the PaymentID is not specified
    header("location: error.php");
    exit;
}

// Prepare a delete statement
$sql = "DELETE FROM payments WHERE PaymentID = :paymentId";

$stmt = $pdo->prepare($sql);

// Bind variables to the prepared statement as parameters
$stmt->bindParam(":paymentId", $param_id, PDO::PARAM_INT);

// Set parameters
$param_id = trim($_GET["id"]);

// Attempt to execute the prepared statement
if ($stmt->execute()) {
    // Redirect to manage payments page after deletion
    header("location: manage_payments.php");
    exit();
} else {
    echo "Oops! Something went wrong. Please try again later.";
}

// Close statement
unset($stmt);

// Close connection
unset($pdo);
?>
