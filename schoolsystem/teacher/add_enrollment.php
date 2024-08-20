<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

$studentsQuery = $pdo->query("SELECT StudentID, CONCAT(FirstName, ' ', MiddleName, ' ', LastName) AS FullName FROM students ORDER BY FirstName, MiddleName, LastName");
$students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);

$schoolLevels = [
    'Junior Nursery', 'Senior Nursery',
    'YS 1: Kindergarten', 'YS 2: Grade 1', 'YS 3: Grade 2',
    'YS 4: Grade 3', 'YS 5: Grade 4', 'YS 6: Grade 5', 'YS 7: Grade 6'
];

$selectedSchoolLevel = $_POST['schoolLevel'] ?? '';
$selectedTermYear = $_POST['termYear'] ?? '';
$selectedStudentID = $_POST['studentID'] ?? '';
$termsAndYears = [];
$subjects = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetch'])) {
    if ($selectedSchoolLevel) {
        $stmt = $pdo->prepare("SELECT DISTINCT CONCAT(o.Term, ' || ', o.SchoolYear) AS TermYear FROM offerings o JOIN subjects s ON s.SubjectID = o.SubjectID WHERE s.SubjectSchoolLevel = ?");
        $stmt->execute([$selectedSchoolLevel]);
        $termsAndYears = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($selectedTermYear) {
            list($term, $schoolYear) = explode(' || ', $selectedTermYear);
            $subjectStmt = $pdo->prepare("SELECT s.SubjectID, s.SubjectName FROM subjects s JOIN offerings o ON s.SubjectID = o.SubjectID WHERE s.SubjectSchoolLevel = ? AND o.Term = ? AND o.SchoolYear = ?");
            $subjectStmt->execute([$selectedSchoolLevel, $term, $schoolYear]);
            $subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enroll']) && !empty($_POST['subjects'])) {
    $pdo->beginTransaction();
    try {
        foreach ($_POST['subjects'] as $subjectID) {
            $stmt = $pdo->prepare("SELECT OfferingID FROM offerings WHERE SubjectID = ? AND Term = ? AND SchoolYear = ?");
            $stmt->execute([$subjectID, $term, $schoolYear]);
            $offering = $stmt->fetch();

            if ($offering) {
                $enrollStmt = $pdo->prepare("INSERT INTO enrollment (StudentID, OfferingID, EnrollmentDate, Status, SchoolLevel) VALUES (?, ?, CURDATE(), 'Active', ?)");
                $enrollStmt->execute([$selectedStudentID, $offering['OfferingID'], $selectedSchoolLevel]);
            } else {
                throw new Exception("No valid offering found for the selected subject.");
            }
        }
        $pdo->commit();
        header('Location: enrollment_success.php?message=Enrollment successful.');
        exit;
    } catch (Exception $e) {
        $pdo->rollback();
        echo "Enrollment failed: " . $e->getMessage();
    }
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Enrollment</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #e9f2fa;
    color: #333;
    margin: 0;
    padding: 0;
}

.teacher-content {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    flex-grow: 1;
    overflow: hidden;
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
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group select,
.form-group input[type="text"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#subjectsContainer label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

#subjectsContainer div {
    margin-bottom: 10px;
}

input[type="checkbox"] {
    margin-right: 10px;
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
    transition: background-color 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3;
}

.alert.alert-danger {
    color: #fff;
    background-color: #dc3545;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.btn-cancel {
    background-color: #dc3545;
}

.btn-cancel:hover {
    background-color: #c82333;
}

    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Add Enrollment</h2>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="student">Student:</label>
                <select name="studentID" id="student" required>
                    <option value="">Select a Student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= htmlspecialchars($student['StudentID']); ?>" <?= ($selectedStudentID == $student['StudentID']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($student['FullName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" required onchange="this.form.submit()">
                    <option value="">Select a School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?= htmlspecialchars($level); ?>" <?= ($selectedSchoolLevel == $level) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($termsAndYears)): ?>
            <div class="form-group">
                <label for="termYear">Term and School Year:</label>
                <select name="termYear" id="termYear" required onchange="this.form.submit()">
                    <option value="">Select Term and School Year</option>
                    <?php foreach ($termsAndYears as $ty): ?>
                        <option value="<?= htmlspecialchars($ty); ?>" <?= ($selectedTermYear == $ty) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($ty); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if (!empty($subjects)): ?>
            <div class="form-group" id="subjectsContainer">
                <label>Subjects:</label>
                <?php foreach ($subjects as $subject): ?>
                    <div>
                        <input type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject['SubjectID']); ?>" checked>
                        <?= htmlspecialchars($subject['SubjectName']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <input type="hidden" name="fetch" value="1">
            <button type="submit" name="enroll" class="btn">Enroll Student</button>
            <a href="manage_enrollment.php" class="btn btn-cancel">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
