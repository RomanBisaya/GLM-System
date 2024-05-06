<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

$schoolLevels = [
    "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten",
    "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3",
    "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
];

$selectedSchoolLevel = $_GET['schoolLevel'] ?? '';
$selectedSchoolYear = $_GET['schoolYear'] ?? '';

try {
    $sql = "SELECT o.OfferingID, s.SubjectName, o.Term, o.SchoolYear, o.ScheduleDetails 
            FROM offerings o 
            INNER JOIN subjects s ON o.SubjectID = s.SubjectID 
            WHERE (:schoolLevel = '' OR s.SubjectSchoolLevel = :schoolLevel) 
            AND (:schoolYear = '' OR o.SchoolYear = :schoolYear)
            ORDER BY s.SubjectSchoolLevel, o.SchoolYear DESC, o.Term, s.SubjectName";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':schoolLevel', $selectedSchoolLevel);
    $stmt->bindParam(':schoolYear', $selectedSchoolYear);
    $stmt->execute();
    $offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $schoolYearQuery = "SELECT DISTINCT SchoolYear FROM offerings ORDER BY SchoolYear DESC";
    $yearStmt = $pdo->query($schoolYearQuery);
    $schoolYears = $yearStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "Oops! Something went wrong. Please try again later.";
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offerings</title>
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
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Offerings</h2>
        <a href="add_offering.php" class="btn">Add New Offering</a>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" class="form-control">
                    <option value="">Select School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level); ?>" <?php echo ($selectedSchoolLevel === $level) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="schoolYear">School Year:</label>
                <select name="schoolYear" id="schoolYear" class="form-control">
                    <option value="">Select School Year</option>
                    <?php foreach ($schoolYears as $year): ?>
                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($selectedSchoolYear === $year) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" value="Filter" class="btn">
        </form>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Term</th>
                    <th>School Year</th>
                    <th>Schedule Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($offerings)): ?>
                    <?php foreach ($offerings as $offering): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($offering['SubjectName']); ?></td>
                        <td><?php echo htmlspecialchars($offering['Term']); ?></td>
                        <td><?php echo htmlspecialchars($offering['SchoolYear']); ?></td>
                        <td><?php echo htmlspecialchars($offering['ScheduleDetails']); ?></td>
                        <td>
                            <a href="edit_offering.php?id=<?php echo $offering['OfferingID']; ?>">Edit</a> |
                            <a href="delete_offering.php?id=<?php echo $offering['OfferingID']; ?>" onclick="return confirm('Are you sure you want to delete this offering?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No offerings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
