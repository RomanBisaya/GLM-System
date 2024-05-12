<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if a subject ID is provided for viewing
$subjectID = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Subject ID not specified.');

$subject = [];

// Fetch subject details
$sql = "SELECT SubjectID, SubjectName, SubjectSchoolLevel, Description, IsActive FROM subjects WHERE SubjectID = :subjectID";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "No records found.";
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit();
    }
}

unset($stmt);
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject Details</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>View Subject Details</h2>

        <div class="subject-details">
            <p><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['SubjectName']); ?></p>
            <p><strong>School Level:</strong> <?php echo htmlspecialchars($subject['SubjectSchoolLevel']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($subject['Description']); ?></p>
            <p><strong>Status:</strong> <?php echo $subject['IsActive'] ? 'Active' : 'Inactive'; ?></p>
        </div>

        <a href="edit_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="btn">Edit Subject</a>
        <a href="manage_subjects.php" class="btn">Back to Subject List</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
