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
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    flex-grow: 1;
}

h2 {
    text-align: center;
    color: #007BFF;
    font-size: 24px;
    margin-bottom: 20px;
}

.subject-details {
    margin-bottom: 20px;
}

.subject-details p {
    font-size: 18px;
    margin: 10px 0;
    color: #555;
}

.subject-details p strong {
    color: #333;
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
    margin-right: 10px;
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

    </style>
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
