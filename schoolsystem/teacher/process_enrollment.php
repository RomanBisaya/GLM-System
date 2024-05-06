<?php
session_start();
require_once '../includes/config.php'; // Database configuration file

// Check if the user is logged in, if not then redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make sure required data is available
    if (empty($_POST['studentID']) || empty($_POST['schoolLevel']) || empty($_POST['termYear']) || !isset($_POST['subjects'])) {
        // Redirect back to the add enrollment page with an error message
        header("location: add_enrollment.php?error=Missing data required for enrollment.");
        exit;
    }
    
    // Extract and sanitize input
    $studentID = filter_var($_POST['studentID'], FILTER_SANITIZE_NUMBER_INT);
    $schoolLevel = filter_var($_POST['schoolLevel'], FILTER_SANITIZE_STRING);
    list($term, $schoolYear) = explode(' || ', $_POST['termYear']);
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        foreach ($_POST['subjects'] as $subjectID) {
            // Prepare a select statement to check if the offering exists
            $sql = "SELECT OfferingID FROM offerings WHERE SubjectID = ? AND Term = ? AND SchoolYear = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$subjectID, $term, $schoolYear]);
            $offering = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If offering exists, proceed with enrollment
            if ($offering) {
                // Prepare an insert statement
                $sql = "INSERT INTO enrollment (StudentID, OfferingID, EnrollmentDate, Status, SchoolLevel) VALUES (?, ?, CURDATE(), ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(1, $studentID);
                $stmt->bindParam(2, $offering['OfferingID']);
                $status = 'Active'; // Default status
                $stmt->bindParam(3, $status);
                $stmt->bindParam(4, $schoolLevel);
                
                // Execute the prepared statement
                $stmt->execute();
            } else {
                // Rollback transaction and redirect with error if an offering doesn't exist
                $pdo->rollback();
                header("location: add_enrollment.php?error=No offering found for the selected subject.");
                exit;
            }
        }
        
        // Commit transaction
        $pdo->commit();
        // Redirect to a confirmation page or back to the form with success message
        header("location: enrollment_success.php?message=Enrollment successful.");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction if an error occurred
        $pdo->rollback();
        // Redirect back to the add enrollment page with an error message
        header("location: add_enrollment.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Redirect to the add enrollment form if the request method is not POST
    header("location: add_enrollment.php");
    exit;
}

// Close connection
unset($pdo);
?>
