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
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
}

.main-content {
    margin-left: 250px; /* Adjust based on sidebar width */
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    max-width: 1200px;
    margin: 50px auto;
}

h1 {
    color: #007BFF;
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
    word-wrap: break-word;
}

th {
    background-color: #007BFF;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f8ff;
}

tr:hover {
    background-color: #e1ecff;
}

p {
    text-align: center;
    font-size: 18px;
    color: #666;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: calc(100% - 250px); /* Adjusts based on sidebar width */
    margin-left: 250px; /* Ensures it aligns with the content */
    position: relative;
    bottom: 0;
    left: 0;
}

    </style> 
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
