<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define the school levels for dropdown
$schoolLevels = [
    "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten", 
    "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3", 
    "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
];

// Define variables and initialize with empty values
$subjectName = $description = $schoolLevel = "";
$isActive = 0;
$subjectID = 0;
$error = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate subject ID
    if(empty(trim($_POST["subjectID"]))) {
        $error = "Subject ID is missing.";
    } else {
        $subjectID = trim($_POST["subjectID"]);
    }

    $subjectName = trim($_POST["subjectName"]);
    $description = trim($_POST["description"]);
    $schoolLevel = trim($_POST["schoolLevel"]);
    $isActive = isset($_POST["isActive"]) ? 1 : 0;

    // Check input errors before inserting in database
    if(empty($error)) {
        // Prepare an update statement
        $sql = "UPDATE subjects SET SubjectName = :subjectName, Description = :description, SchoolLevel = :schoolLevel, IsActive = :isActive WHERE SubjectID = :subjectID";
        
        if($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":subjectName", $subjectName, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":schoolLevel", $schoolLevel, PDO::PARAM_STR);
            $stmt->bindParam(":isActive", $isActive, PDO::PARAM_INT);
            $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()) {
                // Records updated successfully. Redirect to landing page
                header("location: manage_subjects.php");
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
} else {
    // Prepopulate the form if we're editing
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $subjectID = trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM subjects WHERE SubjectID = :subjectID";
        if($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()) {
                if($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    // Retrieve individual field value
                    $subjectName = $row["SubjectName"];
                    $description = $row["Description"];
                    $schoolLevel = $row["SchoolLevel"];
                    $isActive = $row["IsActive"];
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Edit Subject</h2>
        <?php 
        if (!empty($error)) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subjectName" class="form-control" value="<?php echo $subjectName; ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <label>School Level</label>
                <select name="schoolLevel" class="form-control" required>
                    <option value="" disabled>Select a level</option>
                    <?php 
                    foreach ($schoolLevels as $level) {
                        echo "<option value=\"" . htmlspecialchars($level) . "\"" . ($schoolLevel == $level ? ' selected' : '') . ">" . htmlspecialchars($level) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="isActive" value="1"<?php echo ($isActive == 1 ? ' checked' : ''); ?>> Active</label>
            </div>
            <input type="hidden" name="subjectID" value="<?php echo $subjectID; ?>">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="manage_subjects.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
