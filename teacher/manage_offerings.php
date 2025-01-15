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
    overflow-x: auto; /* Allows horizontal scrolling if the table overflows */
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
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
    table-layout: fixed;
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
    background-color: #d9e9ff;
}

td a {
    color: #007BFF;
    text-decoration: none;
    margin-right: 10px;
    transition: color 0.3s ease;
}

td a:hover {
    color: #0056b3;
}

.btn {
    display: inline-block;
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease;
    cursor: pointer;
    border: none;
}

.btn:hover {
    background-color: #0056b3;
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

.btn-action {
    padding: 8px 12px;
    margin: 0 5px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    font-size: 14px;
    transition: background-color 0.3s ease;
    display: inline-block;
}

.btn-action.edit {
    background-color: #ffc107; /* Yellow color for edit */
}

.btn-action.edit:hover {
    background-color: #e0a800; /* Darker yellow on hover */
}

.btn-action.delete {
    background-color: #dc3545; /* Red color for delete */
}

.btn-action.delete:hover {
    background-color: #c82333; /* Darker red on hover */
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
                            <a href="edit_offering.php?id=<?php echo $offering['OfferingID']; ?>" class="btn-action edit">Edit</a>
                            <a href="delete_offering.php?id=<?php echo $offering['OfferingID']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this offering?');">Delete</a>
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
