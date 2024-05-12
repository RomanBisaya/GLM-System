<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Check if the offering ID was provided
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("location: error.php");
    exit;
}

$offeringID = $_GET["id"];

// Variables to hold offering details
$subjectName = $term = $schoolYear = $scheduleDetails = "";

// Fetch current offering details
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT s.SubjectName, o.Term, o.SchoolYear, o.ScheduleDetails 
            FROM offerings o 
            INNER JOIN subjects s ON o.SubjectID = s.SubjectID 
            WHERE o.OfferingID = :offeringID";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch()) {
                $subjectName = $row['SubjectName'];
                $term = $row['Term'];
                $schoolYear = $row['SchoolYear'];
                $scheduleDetails = $row['ScheduleDetails'];
            } else {
                // No records found, redirect to manage page or show an error
                header("location: manage_offerings.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and assign post data
    $subjectName = trim($_POST["subjectName"]);
    $term = trim($_POST["term"]);
    $schoolYear = trim($_POST["schoolYear"]);
    $scheduleDetails = trim($_POST["scheduleDetails"]);

    // Prepare an update statement
    $sql = "UPDATE offerings SET Term = :term, SchoolYear = :schoolYear, ScheduleDetails = :scheduleDetails WHERE OfferingID = :offeringID";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":term", $term, PDO::PARAM_STR);
        $stmt->bindParam(":schoolYear", $schoolYear, PDO::PARAM_STR);
        $stmt->bindParam(":scheduleDetails", $scheduleDetails, PDO::PARAM_STR);
        $stmt->bindParam(":offeringID", $offeringID, PDO::PARAM_INT);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to manage offerings page
            header("location: manage_offerings.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Close statement
    unset($stmt);
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Offering</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Edit Offering</h2>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subjectName" class="form-control" value="<?php echo $subjectName; ?>" required readonly>
            </div>
            <div class="form-group">
                <label>Term</label>
                <input type="text" name="term" class="form-control" value="<?php echo $term; ?>" required>
            </div>
            <div class="form-group">
                <label>School Year</label>
                <input type="text" name="schoolYear" class="form-control" value="<?php echo $schoolYear; ?>" required>
            </div>
            <div class="form-group">
                <label>Schedule Details</label>
                <textarea name="scheduleDetails" class="form-control" required><?php echo $scheduleDetails; ?></textarea>
            </div>
            <input type="submit" class="btn" value="Submit">
            <a href="manage_offerings.php" class="btn">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
