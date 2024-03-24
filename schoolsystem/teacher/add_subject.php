<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define variables and initialize with empty values
$subjectName = $description = $schoolLevel = "";
$isActive = 1; // Default to active
$error = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectName = trim($_POST["subjectName"]);
    $description = trim($_POST["description"]);
    $schoolLevel = trim($_POST["schoolLevel"]);
    $isActive = isset($_POST["isActive"]) ? 1 : 0;

    // Validate input
    if(empty($subjectName) || empty($schoolLevel)) {
        $error = "Subject name and school level are required.";
    } else {
        // Prepare an insert statement
        $sql = "INSERT INTO subjects (SubjectName, Description, SchoolLevel, IsActive) VALUES (:subjectName, :description, :schoolLevel, :isActive)";
        
        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":subjectName", $subjectName, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":schoolLevel", $schoolLevel, PDO::PARAM_STR);
            $stmt->bindParam(":isActive", $isActive, PDO::PARAM_INT);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Records created successfully. Redirect to landing page
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Subject</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Add New Subject</h2>
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
                    <option value="" disabled selected>Select a level</option>
                    <?php 
                    $schoolLevels = [
                        "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten", 
                        "YS 2: Grade 1", "YS 3: Grade 2", "YS 4: Grade 3", 
                        "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
                    ];

                    foreach ($schoolLevels as $level) {
                        echo "<option value=\"$level\">$level</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="isActive" value="1" checked> Is Active</label>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="manage_subjects.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
