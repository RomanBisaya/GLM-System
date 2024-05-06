<?php
session_start();

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
$subjects = [];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission for filtering subjects or processing offerings
    if (isset($_POST['schoolLevel'])) {
        $selectedSchoolLevel = $_POST['schoolLevel'];
        // Fetch active subjects based on the selected school level
        try {
            $stmt = $pdo->prepare("SELECT SubjectID, SubjectName FROM subjects WHERE SubjectSchoolLevel = :schoolLevel AND IsActive = 1");
            $stmt->bindParam(":schoolLevel", $selectedSchoolLevel, PDO::PARAM_STR);
            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error = "Error fetching subjects: " . $e->getMessage();
        }
    }

    // Additional handling for form submissions related to offerings should be implemented here
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
    <title>Add Offering</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Add New Offering</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- School Level Filter Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" class="form-control" onchange="this.form.submit()">
                    <option value="">Select School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level); ?>" <?php echo ($selectedSchoolLevel === $level) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Subject Selection and Offering Details Form -->
        <?php if (!empty($subjects)): ?>
            <form action="process_add_offering.php" method="post">
                <div class="form-group">
                    <label for="term">Term:</label>
                    <input type="text" name="term" id="term" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>School Year:</label>
                    <input type="text" name="schoolYearStart" id="schoolYearStart" class="form-control" style="width: 60px;" required placeholder="YYYY">
                    -
                    <input type="text" name="schoolYearEnd" id="schoolYearEnd" class="form-control" style="width: 60px;" required placeholder="YYYY">
                </div>
                <?php foreach ($subjects as $subject): ?>
                    <div class="form-group">
                        <input type="checkbox" name="subjects[]" value="<?php echo $subject['SubjectID']; ?>" id="subject_<?php echo $subject['SubjectID']; ?>" checked>
                        <label for="subject_<?php echo $subject['SubjectID']; ?>"><?php echo htmlspecialchars($subject['SubjectName']); ?></label>
                        <input type="text" name="schedules[<?php echo $subject['SubjectID']; ?>]" class="form-control" placeholder="Input Schedule">
                    </div>
                <?php endforeach; ?>
                <input type="submit" value="Add Offering" class="btn">
            </form>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
