<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

$studentID = $_GET['StudentID'] ?? '';

if (!$studentID) {
    echo "Student ID is required.";
    exit;
}

// Initialize student name
$studentName = "";

// Fetch student details along with enrollment details
try {
    $query = "SELECT s.StudentID, CONCAT(s.FirstName, ' ', s.MiddleName, ' ', s.LastName) AS FullName, e.SchoolLevel, sub.SubjectName, o.SchoolYear
              FROM students s
              LEFT JOIN enrollment e ON s.StudentID = e.StudentID
              LEFT JOIN offerings o ON e.OfferingID = o.OfferingID
              LEFT JOIN subjects sub ON o.SubjectID = sub.SubjectID
              WHERE s.StudentID = :studentID
              ORDER BY sub.SubjectName";  // Added ORDER BY clause to sort by subject name
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->execute();
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the student name from the first row if not empty
    if (!empty($enrollments)) {
        $studentName = $enrollments[0]['FullName'];
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Error fetching enrollment details: " . $e->getMessage();
    exit;
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enrollment</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Student Enrollment Details</h2>
        <?php if ($studentName): ?>
            <p><strong>Student Name:</strong> <?= htmlspecialchars($studentName); ?></p>
        <?php endif; ?>
        <?php if (!empty($enrollments)): ?>
        <table>
            <thead>
                <tr>
                    <th>School Level</th>
                    <th>Subject</th>
                    <th>School Year</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td><?= htmlspecialchars($enrollment['SchoolLevel']); ?></td>
                    <td><?= htmlspecialchars($enrollment['SubjectName']); ?></td>
                    <td><?= htmlspecialchars($enrollment['SchoolYear']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No details available for this student.</p>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
