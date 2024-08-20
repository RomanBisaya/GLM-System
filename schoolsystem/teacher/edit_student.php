<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define variables and initialize with empty values
$firstName = $middleName = $lastName = $username = "";
$firstName_err = $middleName_err = $lastName_err = $username_err = $password_err = "";
$id = 0;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate ID
    if (empty(trim($_POST["id"]))) {
        echo "Error: student ID is missing.";
        exit();
    } else {
        $id = trim($_POST["id"]);
    }

    // Other validations...
    $firstName = trim($_POST["firstName"]);
    $middleName = trim($_POST["middleName"]);
    $lastName = trim($_POST["lastName"]);
    $username = trim($_POST["username"]);
    $password_provided = !empty(trim($_POST["password"]));

    // Prepare an update statement without SchoolLevel
    $sql = "UPDATE Students SET FirstName = :firstName, MiddleName = :middleName, LastName = :lastName, Username = :username" .
        ($password_provided ? ", Password = :password" : "") . " WHERE StudentID = :id";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
        $stmt->bindParam(":middleName", $middleName, PDO::PARAM_STR);
        $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);

        if ($password_provided) {
            $hashed_password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            header("location: manage_students.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement and connection
    unset($stmt);
    unset($pdo);
} elseif (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Fetch existing student data for initial form population
    $id = trim($_GET["id"]);

    $sql = "SELECT * FROM Students WHERE StudentID = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $firstName = $row['FirstName'];
                $middleName = $row['MiddleName'];
                $lastName = $row['LastName'];
                $username = $row['Username'];
            } else {
                echo "Error: No student found with the specified ID.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }
    }
    unset($stmt);
} else {
    // URL doesn't contain id parameter. Redirect to error page.
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
    <title>Edit Student</title>
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
    max-width: 800px;
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

p {
    text-align: center;
    font-size: 16px;
    color: #555;
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

.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-group.has-error input {
    border-color: #dc3545;
}

.help-block {
    color: #dc3545;
    font-size: 12px;
}

.btn {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-right: 10px;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #007BFF;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-default {
    background-color: #6c757d;
    color: white;
}

.btn-default:hover {
    background-color: #5a6268;
}

.teacher-content form .form-group:last-child {
    text-align: center;
}

    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Edit Student</h2>
        <p>Please edit the input values and submit to update the student record.</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>" required>
            </div>
            <div class="form-group">
                <label>Middle Name (optional)</label>
                <input type="text" name="middleName" class="form-control" value="<?php echo htmlspecialchars($middleName); ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label>Password (Leave blank to keep current password)</label>
                <input type="text" name="password" class="form-control" placeholder="Enter new password to reset">
            </div>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="manage_students.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
