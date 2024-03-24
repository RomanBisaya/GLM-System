<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define the school levels and initiate an array to hold school years
$schoolLevels = [
    "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten",
    "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3",
    "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
];
$schoolYears = []; // This could be dynamically populated based on your requirements

// Attempt to execute the prepared statement
try {
    // Capture filter values from GET request
    $selectedSchoolLevel = isset($_GET['schoolLevel']) ? $_GET['schoolLevel'] : '';
    $selectedSchoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';

    // Build the SQL query based on filters
    $sql = "SELECT o.OfferingID, s.SubjectName, o.Term, o.SchoolYear, o.ScheduleDetails 
            FROM offerings o 
            INNER JOIN subjects s ON o.SubjectID = s.SubjectID 
            WHERE (:schoolLevel = '' OR s.SchoolLevel = :schoolLevel) 
            AND (:schoolYear = '' OR o.SchoolYear = :schoolYear)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':schoolLevel', $selectedSchoolLevel);
    $stmt->bindParam(':schoolYear', $selectedSchoolYear);
    $stmt->execute();

    // Fetch all offerings records based on filters
    $offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Optionally, fetch distinct school years from offerings for the filter
    $schoolYearQuery = "SELECT DISTINCT SchoolYear FROM offerings ORDER BY SchoolYear DESC";
    $yearStmt = $pdo->query($schoolYearQuery);
    $schoolYears = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    echo "Oops! Something went wrong. Please try again later.";
    error_log($e->getMessage()); // Log error to server's error log
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offerings</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Offerings</h2>
        <a href="add_offering.php" class="btn">Add New Offering</a>

        <!-- Filter Form -->
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

        <!-- Offerings Table -->
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
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
