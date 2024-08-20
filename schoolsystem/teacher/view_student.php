<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Initialize variables
$firstName = $middleName = $lastName = $schoolLevel = $username = "";
$id = 0;

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare a select statement
    $sql = "SELECT * FROM Students WHERE StudentID = :id";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Retrieve individual field value
                $firstName = $row["FirstName"];
                $middleName = $row["MiddleName"];
                $lastName = $row["LastName"];
                $username = $row["Username"];
                // Consider not displaying sensitive information like passwords
                
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
    <title>View Student</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #e9f2fa;
    color: #333;
    margin: 0;
    padding: 0;
}

.teacher-content {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

h2 {
    text-align: center;
    color: #007BFF;
    font-size: 24px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

.form-group p {
    font-size: 18px;
    color: #333;
    padding: 10px;
    background-color: #f7f9fa;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.btn-primary {
    display: inline-block;
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
    text-align: center;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.teacher-content p {
    text-align: center;
    margin-top: 20px;
}

    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Student Details</h2>
        <div class="form-group">
            <label>First Name:</label>
            <p><b><?php echo htmlspecialchars($firstName); ?></b></p>
        </div>
        <div class="form-group">
            <label>Middle Name:</label>
            <p><b><?php echo htmlspecialchars($middleName); ?></b></p>
        </div>
        <div class="form-group">
            <label>Last Name:</label>
            <p><b><?php echo htmlspecialchars($lastName); ?></b></p>
        </div>
        <div class="form-group">
            <label>Username:</label>
            <p><b><?php echo htmlspecialchars($username); ?></b></p>
        </div>
        <p><a href="manage_students.php" class="btn btn-primary">Back to list</a></p>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
