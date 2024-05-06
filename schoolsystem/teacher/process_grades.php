<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ratings'])) {
    $ratings = $_POST['ratings']; // Array of ratings with student ID as key

    $pdo->beginTransaction(); // Start transaction

    try {
        foreach ($ratings as $studentID => $grade) {
            // Assuming EnrollmentID and SubjectID should be determined or passed from the previous form or logic
            // For the sake of example, I am querying them based on student ID, you need to adjust this according to your application logic

            // Fetch the most recent enrollment ID for the student
            $enrollmentStmt = $pdo->prepare("SELECT EnrollmentID FROM enrollment WHERE StudentID = :studentID ORDER BY EnrollmentDate DESC LIMIT 1");
            $enrollmentStmt->execute(['studentID' => $studentID]);
            $enrollmentRow = $enrollmentStmt->fetch();
            $enrollmentID = $enrollmentRow['EnrollmentID'] ?? 0;

            // Insert or update the grade for each student
            $sql = "INSERT INTO grades (EnrollmentID, Grade) VALUES (:enrollmentID, :grade) ON DUPLICATE KEY UPDATE Grade = :grade";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['enrollmentID' => $enrollmentID, 'grade' => $grade]);
        }

        $pdo->commit(); // Commit transaction
        $_SESSION['success_message'] = 'Grades have been successfully saved.';
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback transaction on error
        $_SESSION['error_message'] = 'Error saving grades: ' . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = 'No grades submitted.';
}

header("location: manage_grades.php");
exit;
?>
