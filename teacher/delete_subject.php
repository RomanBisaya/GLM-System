<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if an ID was passed in the URL
$subjectID = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing subject ID.');

// Check for any dependencies before deletion, such as subject offerings
$sql = "SELECT COUNT(*) FROM offerings WHERE SubjectID = :subjectID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
$stmt->execute();
if($stmt->fetchColumn() > 0) {
    // If there are dependencies, prevent deletion and redirect
    header("location: manage_subjects.php?error=cannotdelete");
    exit;
}

// Prepare a delete statement
$sql = "DELETE FROM subjects WHERE SubjectID = :subjectID";
if ($stmt = $pdo->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Records deleted successfully. Redirect to landing page
        header("location: manage_subjects.php?message=deleted");
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
