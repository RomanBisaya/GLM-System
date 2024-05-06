<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $term = $_POST['term'];
    $schoolYearStart = $_POST['schoolYearStart'];
    $schoolYearEnd = $_POST['schoolYearEnd'];
    $schoolYear = $schoolYearStart . '-' . $schoolYearEnd;
    $subjects = $_POST['subjects'] ?? [];
    $schedules = $_POST['schedules'] ?? [];
    
    // Start transaction
    $pdo->beginTransaction();

    try {
        foreach ($subjects as $subjectID) {
            $schedule = $schedules[$subjectID] ?? '';

            // Insert each offering with the schedule
            $sql = "INSERT INTO offerings (SubjectID, Term, SchoolYear, ScheduleDetails) VALUES (:subjectID, :term, :schoolYear, :scheduleDetails)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
            $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);
            $stmt->bindParam(':scheduleDetails', $schedule, PDO::PARAM_STR);
            
            $stmt->execute();
        }

        // Commit the transaction
        $pdo->commit();
        $_SESSION['success_message'] = 'Offerings added successfully.';
        header("location: manage_offerings.php");
        exit();
    } catch (Exception $e) {
        // An error occurred; roll back the transaction
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Error adding offerings: ' . $e->getMessage();
        header("location: add_offering.php");
        exit();
    }
}

// Close connection
unset($pdo);
?>
