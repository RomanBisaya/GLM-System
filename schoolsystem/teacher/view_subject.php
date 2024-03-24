<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Initialize variables
$subjectName = $description = $schoolLevel = "";
$isActive = 0;
$subjectID = 0;

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Prepare a select statement
    $sql = "SELECT * FROM subjects WHERE SubjectID = :subjectID";
    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":subjectID", $param_id, PDO::PARAM_INT);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Retrieve individual field value
                $subjectName = $row["SubjectName"];
                $description = $row["Description"];
                $schoolLevel = $row["SchoolLevel"];
                $isActive = $row["IsActive"];
            } else{
                // URL doesn't contain valid id. Redirect to error page
                header("location: error.php");
                exit();
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Close statement
    unset($stmt);
    
    // Close connection
    unset($pdo);
} else {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject Details</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Subject Details</h2>
        <div class="form-group">
            <label>Subject Name:</label>
            <p><?php echo htmlspecialchars($subjectName); ?></p>
        </div>
        <div class="form-group">
            <label>Description:</label>
            <p><?php echo htmlspecialchars($description); ?></p>
        </div>
        <div class="form-group">
            <label>School Level:</label>
            <p><?php echo htmlspecialchars($schoolLevel); ?></p>
        </div>
        <div class="form-group">
            <label>Status:</label>
            <p><?php echo $isActive ? 'Active' : 'Inactive'; ?></p>
        </div>
        <a href="manage_subjects.php" class="btn btn-default">Back to List</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
