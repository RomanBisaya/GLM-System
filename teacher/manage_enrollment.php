<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Fetch unique school levels and school years for dropdowns
$schoolLevelsQuery = $pdo->query("SELECT DISTINCT SchoolLevel FROM enrollment WHERE SchoolLevel IS NOT NULL ORDER BY SchoolLevel");
$schoolLevels = $schoolLevelsQuery->fetchAll(PDO::FETCH_COLUMN);

$schoolYearsQuery = $pdo->query("SELECT DISTINCT SchoolYear FROM offerings ORDER BY SchoolYear DESC");
$schoolYears = $schoolYearsQuery->fetchAll(PDO::FETCH_COLUMN);

$selectedSchoolLevel = $_POST['schoolLevel'] ?? '';
$selectedSchoolYear = $_POST['schoolYear'] ?? '';

$enrollments = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "SELECT DISTINCT s.StudentID, CONCAT(s.FirstName, ' ', s.MiddleName, ' ', s.LastName) AS FullName, e.SchoolLevel, o.SchoolYear, e.IsActive
              FROM enrollment e
              JOIN students s ON e.StudentID = s.StudentID
              JOIN offerings o ON e.OfferingID = o.OfferingID
              WHERE e.SchoolLevel LIKE :schoolLevel AND o.SchoolYear LIKE :schoolYear
              ORDER BY e.SchoolLevel, s.FirstName, s.MiddleName, s.LastName";

    $stmt = $pdo->prepare($query);
    $schoolLevelParam = $selectedSchoolLevel !== '' ? $selectedSchoolLevel : '%';
    $schoolYearParam = $selectedSchoolYear !== '' ? $selectedSchoolYear : '%';
    $stmt->bindParam(':schoolLevel', $schoolLevelParam);
    $stmt->bindParam(':schoolYear', $schoolYearParam);
    $stmt->execute();
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enrollment</title>
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

.teacher-content {
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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    margin-right: 10px;
    font-weight: bold;
}

.form-group select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
}

.form-group button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.form-group button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 16px;
    table-layout: auto; /* Adjusts table width automatically based on content */
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

/* Action Buttons */
.btn {
    padding: 8px 15px;
    text-decoration: none;
    font-size: 14px;
    border-radius: 5px;
    margin-right: 5px;
    color: white;
    transition: background-color 0.3s ease;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn.view {
    background-color: #17a2b8;
}

.btn.view:hover {
    background-color: #117a8b;
}

.btn.edit {
    background-color: #ffc107;
    color: #333;
}

.btn.edit:hover {
    background-color: #e0a800;
    color: #333;
}

.btn.deactivate {
    background-color: #dc3545;
}

.btn.deactivate:hover {
    background-color: #c82333;
}

.btn.activate {
    background-color: #28a745;
}

.btn.activate:hover {
    background-color: #218838;
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

.btn {
    display: inline-block;
    padding: 10px 20px;
    margin-bottom: 20px;
    background-color: #007BFF; /* Blue background */
    color: white; /* White text */
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}

    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Enrollment</h2>
        <a href="add_enrollment.php" class="btn">Add Enroll Student</a>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel">
                    <option value="">All Levels</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?= htmlspecialchars($level); ?>" <?= $selectedSchoolLevel === $level ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="schoolYear">School Year:</label>
                <select name="schoolYear" id="schoolYear">
                    <option value="">All Years</option>
                    <?php foreach ($schoolYears as $year): ?>
                        <option value="<?= htmlspecialchars($year); ?>" <?= $selectedSchoolYear === $year ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($year); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
            </div>
        </form>
        <?php if (!empty($enrollments)): ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>School Level</th>
                    <th>School Year</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td><?= htmlspecialchars($enrollment['FullName']); ?></td>
                    <td><?= htmlspecialchars($enrollment['SchoolLevel']); ?></td>
                    <td><?= htmlspecialchars($enrollment['SchoolYear']); ?></td>
                    <td>
                        <a href="view_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>" class="btn view">View</a>
                        <a href="edit_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>" class="btn edit">Edit</a>
                        <?php if ($enrollment['IsActive']): ?>
                            <a href="deactivate_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>" class="btn deactivate" onclick="return confirm('Are you sure you want to deactivate this enrollment?');">Deactivate</a>
                        <?php else: ?>
                            <a href="activate_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>" class="btn activate" onclick="return confirm('Are you sure you want to activate this enrollment?');">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No enrollment data found. Please adjust your filters.</p>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
