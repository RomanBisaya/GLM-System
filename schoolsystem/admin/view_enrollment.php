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
        body {
    font-family: Arial, sans-serif;
    background-color: #e9f2fa;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.admin-content {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    flex-grow: 1;
    overflow-x: auto; /* Allows horizontal scrolling if table exceeds container width */
}

h2 {
    text-align: center;
    color: #007BFF;
    font-size: 24px;
    margin-bottom: 20px;
}

p {
    font-size: 18px;
    margin-bottom: 20px;
    color: #555;
}

.table-container {
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 16px;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
    word-wrap: break-word; /* Ensures content wraps within table cells */
}

th {
    background-color: #007BFF;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f8ff;
}

tr:hover {
    background-color: #d9e9ff;
}

.btn-back {
    padding: 10px 20px;
    margin: 10px 0;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    display: inline-block;
    transition: background-color 0.3s ease;
}

.btn-back:hover {
    background-color: #45a049;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: 100%;
    position: relative;
    bottom: 0;
    left: 0;
    clear: both;
}

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <h2>Student Enrollment Details</h2>
        <a href="manage_enrollment.php" class="btn-back">Back to Enrollments</a>
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