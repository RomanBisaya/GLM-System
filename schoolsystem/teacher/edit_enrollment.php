<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

$studentID = $_GET['StudentID'] ?? '';

if (!$studentID) {
    echo "Student ID is required.";
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schoolLevel = trim($_POST['schoolLevel']);
    $offeringID = trim($_POST['offeringID']); // Get OfferingID which links to SchoolYear

    // Update the enrollment data
    $sql = "UPDATE enrollment SET SchoolLevel = :schoolLevel, OfferingID = :offeringID WHERE StudentID = :studentID";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":schoolLevel", $schoolLevel, PDO::PARAM_STR);
        $stmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
        $stmt->bindParam(":studentID", $studentID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("location: manage_enrollment.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
}

// Fetch current data for the student
if ($stmt = $pdo->prepare("
    SELECT e.SchoolLevel, o.OfferingID, o.SchoolYear 
    FROM enrollment e 
    JOIN offerings o ON e.OfferingID = o.OfferingID 
    WHERE e.StudentID = :studentID
")) {
    $stmt->bindParam(":studentID", $studentID, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
        $schoolLevel = $enrollment['SchoolLevel'];
        $offeringID = $enrollment['OfferingID'];
        $schoolYear = $enrollment['SchoolYear'];
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    unset($stmt);
}

// Fetch offerings for the dropdown
$offerings = [];
$offeringStmt = $pdo->query("SELECT OfferingID, SchoolYear FROM offerings ORDER BY SchoolYear DESC");
if ($offeringStmt) {
    $offerings = $offeringStmt->fetchAll(PDO::FETCH_ASSOC);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Enrollment</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Edit Enrollment</h2>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
            <div class="form-group">
                <label>School Level</label>
                <input type="text" name="schoolLevel" class="form-control" value="<?php echo htmlspecialchars($schoolLevel); ?>" required>
            </div>
            <div class="form-group">
                <label>School Year</label>
                <select name="offeringID" class="form-control" required>
                    <?php foreach ($offerings as $offering): ?>
                        <option value="<?php echo htmlspecialchars($offering['OfferingID']); ?>" <?php echo ($offeringID == $offering['OfferingID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($offering['SchoolYear']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" class="btn" value="Submit">
            <a href="manage_enrollment.php" class="btn">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
