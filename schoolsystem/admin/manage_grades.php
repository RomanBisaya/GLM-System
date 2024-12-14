<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$schoolLevels = ["Junior Nursery", "Senior Nursery", "YS 1: Kindergarten", "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3", "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"];
$offerings = [];
$enrolledStudents = [];
$selectedSchoolLevel = '';
$selectedOffering = '';

// Fetch offerings based on selected school level
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['schoolLevel'])) {
    $selectedSchoolLevel = $_POST['schoolLevel'];
    $sql = "SELECT o.OfferingID, o.SubjectID, s.SubjectName, o.Term, o.SchoolYear 
            FROM offerings o 
            INNER JOIN subjects s ON o.SubjectID = s.SubjectID 
            WHERE s.SubjectSchoolLevel = :schoolLevel AND s.IsActive = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':schoolLevel', $selectedSchoolLevel, PDO::PARAM_STR);
    $stmt->execute();
    $offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$selectedOffering = $_POST['offering'] ?? '';

if (!empty($selectedOffering) && isset($_POST['proceed'])) {
    $sql = "SELECT s.StudentID, CONCAT(s.FirstName, ' ', s.MiddleName, ' ', s.LastName) AS FullName, e.SchoolLevel, e.EnrollmentID, o.SubjectID,
                   g.Grade
            FROM students s
            INNER JOIN enrollment e ON s.StudentID = e.StudentID
            INNER JOIN offerings o ON e.OfferingID = o.OfferingID
            LEFT JOIN grades g ON g.EnrollmentID = e.EnrollmentID AND g.SubjectID = o.SubjectID
            WHERE e.OfferingID = :offeringID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':offeringID', $selectedOffering, PDO::PARAM_INT);
    $stmt->execute();
    $enrolledStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Save grades if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_grades'])) {
    foreach ($_POST['ratings'] as $studentID => $grade) {
        $enrollmentID = $_POST['enrollmentIds'][$studentID];
        $subjectID = $_POST['subjectIds'][$studentID];
        $sql = "INSERT INTO grades (EnrollmentID, SubjectID, Grade) VALUES (:enrollmentID, :subjectID, :grade)
                ON DUPLICATE KEY UPDATE Grade = VALUES(Grade)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':enrollmentID', $enrollmentID, PDO::PARAM_INT);
        $stmt->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmt->execute();
    }
    echo "<script>alert('Grades saved successfully!');</script>";
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
}

.admin-content {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

h2 {
    text-align: center;
    color: #007BFF;
    font-size: 24px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
    text-align: center;
}

label {
    font-weight: bold;
    margin-right: 10px;
}

select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
}

button[type="submit"] {
    padding: 10px 20px;
    font-size: 16px;
    color: white;
    background-color: #007BFF;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
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

select[name^="ratings"] {
    width: 100%;
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button[type="submit"] {
    margin-top: 20px;
    display: block;
    width: 100%;
    max-width: 200px;
    margin-left: auto;
    margin-right: auto;
}

.btn {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s ease;
    display: inline-block;
    margin-right: 10px;
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
}

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <h2>Manage Grades</h2>

        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" onchange="this.form.submit()">
                    <option value="">Select School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?= htmlspecialchars($level); ?>" <?= $selectedSchoolLevel == $level ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="offering">Offering:</label>
                <select name="offering" id="offering">
                    <option value="">Select Offering</option>
                    <?php foreach ($offerings as $offering): ?>
                        <option value="<?= $offering['OfferingID']; ?>" <?= $selectedOffering == $offering['OfferingID'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($offering['SubjectName'] . " - " . $offering['Term'] . " - " . $offering['SchoolYear']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="proceed">Proceed</button>
            </div>
            
            <?php if (!empty($enrolledStudents)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>School Level</th>
                        <th>Ratings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrolledStudents as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['FullName']); ?></td>
                        <td><?= htmlspecialchars($student['SchoolLevel']); ?></td>
                        <td>
                            <select name="ratings[<?= $student['StudentID']; ?>]">
                                <?php
                                $options = ["Ok", "Good", "Very Good", "Excellent"];
                                $currentGrade = $student['Grade'] ?? 'Ok'; // Default to 'Ok' if no grade is set
                                foreach ($options as $option) {
                                    echo '<option value="' . $option . '"' . ($currentGrade == $option ? ' selected' : '') . '>' . $option . '</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="enrollmentIds[<?= $student['StudentID']; ?>]" value="<?= $student['EnrollmentID']; ?>">
                            <input type="hidden" name="subjectIds[<?= $student['StudentID']; ?>]" value="<?= $student['SubjectID']; ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="save_grades">Save Grades</button>
            <?php endif; ?>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
