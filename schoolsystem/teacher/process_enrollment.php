<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enroll'])) {
    $studentID = $_POST['studentID'];
    $offerings = $_POST['offerings'] ?? [];
    $errors = [];

    // Validate inputs
    if (empty($studentID) || empty($offerings)) {
        $errors[] = "Both student and offerings must be selected.";
    }

    // Proceed if there are no errors
    if (empty($errors)) {
        foreach ($offerings as $offeringID) {
            // Prepare an insert statement for each offering
            $sql = "INSERT INTO enrollment (StudentID, OfferingID, EnrollmentDate, Status) VALUES (:studentID, :offeringID, CURDATE(), 'Active')";
            
            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":studentID", $studentID, PDO::PARAM_INT);
                $stmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
                
                // Execute the prepared statement
                if (!$stmt->execute()) {
                    // Add error to errors array if execution failed
                    $errors[] = "Failed to enroll student (ID: $studentID) in offering (ID: $offeringID).";
                }
            } else {
                $errors[] = "There was an error preparing the enrollment statement for offering ID: $offeringID.";
            }
        }

        // Redirect or show success/failure messages
        if (empty($errors)) {
            // If successful, redirect to the manage enrollments page with a success message
            $_SESSION['success_message'] = 'Student enrolled successfully.';
            header("location: manage_enrollment.php");
            exit();
        } else {
            // Display errors if any
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

// Close connection
unset($pdo);
?>
