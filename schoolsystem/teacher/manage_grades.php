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
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
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
