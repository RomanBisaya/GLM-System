<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subjects'], $_POST['term'], $_POST['schoolYear'])) {
    $term = $_POST['term'];
    $schoolYear = $_POST['schoolYear'];
    $subjects = $_POST['subjects'];
    $schedules = $_POST['schedules'] ?? [];
    
    $errors = [];

    // Validate inputs (basic validation)
    if (empty($term) || empty($schoolYear)) {
        $errors[] = "Term and school year are required.";
    }

    // Proceed if there are no errors
    if (empty($errors)) {
        foreach ($subjects as $subjectID) {
            // Check if the schedule for this subject was provided
            if (!empty($schedules[$subjectID])) {
                $scheduleDetail = $schedules[$subjectID];
                
                // Prepare an insert statement for each subject offering
                $sql = "INSERT INTO offerings (SubjectID, Term, SchoolYear, ScheduleDetails) VALUES (:subjectID, :term, :schoolYear, :scheduleDetails)";
                if ($stmt = $pdo->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);
                    $stmt->bindParam(":term", $term, PDO::PARAM_STR);
                    $stmt->bindParam(":schoolYear", $schoolYear, PDO::PARAM_STR);
                    $stmt->bindParam(":scheduleDetails", $scheduleDetail, PDO::PARAM_STR);
                    
                    // Execute the prepared statement
                    if (!$stmt->execute()) {
                        // Add error to errors array if execution failed
                        $errors[] = "Failed to add offering for subject ID $subjectID.";
                    }
                }
            } else {
                // Add error to errors array if schedule detail was missing
                $errors[] = "Schedule detail is missing for subject ID $subjectID.";
            }
        }

        // Redirect or show success/failure messages
        if (empty($errors)) {
            // If successful, redirect to the manage offerings page with a success message
            $_SESSION['success_message'] = 'Offerings added successfully.';
            header("location: manage_offerings.php");
            exit();
        } else {
            // Output errors if present
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    } else {
        // Output errors if initial validation failed
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

// Close connection
unset($pdo);
?>
