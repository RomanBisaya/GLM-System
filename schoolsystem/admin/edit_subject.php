<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define variables and initialize with empty values
$subjectName = $description = "";
$isActive = 0;
$subjectID = 0;
$error = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate subject ID
    if (empty(trim($_POST["subjectID"]))) {
        $error = "Subject ID is missing.";
    } else {
        $subjectID = trim($_POST["subjectID"]);
    }

    $subjectName = trim($_POST["subjectName"]);
    $description = trim($_POST["description"]);
    $isActive = isset($_POST["isActive"]) ? 1 : 0;

    // Check input errors before inserting in database
    if (empty($error)) {
        // Prepare an update statement
        $sql = "UPDATE subjects SET SubjectName = :subjectName, Description = :description, IsActive = :isActive WHERE SubjectID = :subjectID";
        
        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":subjectName", $subjectName, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":isActive", $isActive, PDO::PARAM_INT);
            $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
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
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $subjectID = trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM subjects WHERE SubjectID = :subjectID";
        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":subjectID", $subjectID, PDO::PARAM_INT);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    // Retrieve individual field values
                    $subjectName = $row["SubjectName"];
                    $description = $row["Description"];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
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

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 16px;
}

.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

textarea.form-control {
    height: 100px;
    resize: vertical;
}

select.form-control {
    padding: 10px;
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

.btn-default {
    background-color: #6c757d;
    color: white;
}

.btn-default:hover {
    background-color: #5a6268;
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
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <h2>Edit Subject</h2>
        <?php 
        if (!empty($error)) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subjectName" class="form-control" value="<?php echo htmlspecialchars($subjectName); ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo htmlspecialchars($description); ?></textarea>
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
