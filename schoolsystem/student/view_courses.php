<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Ensure the user is logged in
    exit;
}

require_once '../includes/config.php'; // Database configuration file

$studentID = $_SESSION['student_id']; // Assuming session stores student_id
$courses = [];

// Fetch courses for the logged-in student
try {
    $sql = "SELECT s.SubjectName, o.Term, o.SchoolYear
            FROM subjects s
            JOIN offerings o ON s.SubjectID = o.SubjectID
            JOIN enrollment e ON e.OfferingID = o.OfferingID
            WHERE e.StudentID = :studentID
            ORDER BY o.SchoolYear, o.Term, s.SubjectName";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

include 'sidebar.php'; // Include the sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Path to your CSS file -->
</head>
<body>

<div class="main-content">
    <h1>My Courses</h1>
    <?php if (count($courses) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Term</th>
                    <th>School Year</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['SubjectName']); ?></td>
                        <td><?= htmlspecialchars($course['Term']); ?></td>
                        <td><?= htmlspecialchars($course['SchoolYear']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No courses found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
