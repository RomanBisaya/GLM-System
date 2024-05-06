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
    $query = "SELECT DISTINCT s.StudentID, CONCAT(s.FirstName, ' ', s.MiddleName, ' ', s.LastName) AS FullName, e.SchoolLevel, o.SchoolYear
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
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
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
                        <a href="view_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>">View</a> |
                        <a href="edit_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>">Edit</a> |
                        <a href="delete_enrollment.php?StudentID=<?= $enrollment['StudentID']; ?>" onclick="return confirm('Are you sure you want to delete this enrollment?');">Delete</a>
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
