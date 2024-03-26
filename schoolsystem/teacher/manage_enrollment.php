<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define the school levels
$schoolLevels = [
    "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten", 
    "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3", 
    "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
];

$selectedSchoolLevel = '';
$selectedSchoolYear = '';
$selectedTerm = '';
$students = [];
$offerings = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedSchoolLevel = $_POST['schoolLevel'];
    $selectedSchoolYear = $_POST['schoolYear'];
    $selectedTerm = $_POST['term'];

    // Fetch students matching the selected school level
    $stmt = $pdo->prepare("SELECT StudentID, CONCAT(FirstName, ' ', LastName) AS StudentName FROM students WHERE SchoolLevel = :schoolLevel");
    $stmt->bindParam(':schoolLevel', $selectedSchoolLevel, PDO::PARAM_STR);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch offerings that match the selected school year, term, and the school level of the subjects
    $stmt = $pdo->prepare("SELECT o.OfferingID, s.SubjectName, o.Term, o.SchoolYear, o.ScheduleDetails 
                           FROM offerings o 
                           INNER JOIN subjects s ON o.SubjectID = s.SubjectID 
                           WHERE o.SchoolYear = :schoolYear AND o.Term = :term AND s.SchoolLevel = :schoolLevel AND s.IsActive = 1");
    $stmt->bindParam(':schoolYear', $selectedSchoolYear, PDO::PARAM_STR);
    $stmt->bindParam(':term', $selectedTerm, PDO::PARAM_STR);
    $stmt->bindParam(':schoolLevel', $selectedSchoolLevel, PDO::PARAM_STR);
    $stmt->execute();
    $offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Close connection
unset($pdo);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enrollment</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Enrollment</h2>

        <!-- Filter Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" required>
                    <option value="">Select School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level); ?>" <?php if($selectedSchoolLevel == $level) echo 'selected'; ?>><?php echo htmlspecialchars($level); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="schoolYear">School Year:</label>
                <input type="text" name="schoolYear" id="schoolYear" value="<?php echo htmlspecialchars($selectedSchoolYear); ?>" required placeholder="YYYY-YYYY">
            </div>
            <div class="form-group">
                <label for="term">Term:</label>
                <input type="text" name="term" id="term" value="<?php echo htmlspecialchars($selectedTerm); ?>" required placeholder="e.g., Fall">
            </div>
            <input type="submit" value="Filter Subjects" class="btn">
        </form>

        <?php if(!empty($students) && !empty($offerings)): ?>
        <!-- Enrollment Form -->
        <form action="process_enrollment.php" method="post">
            <?php foreach($offerings as $offering): ?>
            <div class="form-group">
                <input type="checkbox" name="offerings[]" value="<?php echo $offering['OfferingID']; ?>" id="offering_<?php echo $offering['OfferingID']; ?>" checked>
                <label for="offering_<?php echo $offering['OfferingID']; ?>">
                    <?php echo htmlspecialchars($offering['SubjectName']." - ".$offering['Term']." - ".$offering['SchoolYear']." - ".$offering['ScheduleDetails']); ?>
                </label>
            </div>
            <?php endforeach; ?>
            <div class="form-group">
                <label for="student">Select Student:</label>
                <select name="studentID" id="student" required>
                    <?php foreach($students as $student): ?>
                        <option value="<?php echo $student['StudentID']; ?>"><?php echo htmlspecialchars($student['StudentName']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" name="enroll" value="Enroll" class="btn">
        </form>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
