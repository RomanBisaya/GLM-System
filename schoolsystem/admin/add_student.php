<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$firstName = $middleName = $lastName = $username = $password = "";
$firstName_err = $middleName_err = $lastName_err = $username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = trim($_POST["firstName"]);
    $middleName = trim($_POST["middleName"]); // Even if it's empty
    $lastName = trim($_POST["lastName"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validate required inputs here
    if (empty($firstName)) {
        $firstName_err = "Please enter a first name.";
    }
    if (empty($lastName)) {
        $lastName_err = "Please enter a last name.";
    }
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username exists
        $sql = "SELECT StudentID FROM Students WHERE Username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $username_err = "This username is already taken.";
                }
            }
            unset($stmt);
        }
    }
    if (empty($password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }

    if (empty($firstName_err) && empty($middleName_err) && empty($lastName_err) && empty($username_err) && empty($password_err)) {
        
        $sql = "INSERT INTO Students (FirstName, MiddleName, LastName, Username, Password) VALUES (:firstName, :middleName, :lastName, :username, :password)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
            $stmt->bindParam(":middleName", $middleName, PDO::PARAM_STR); // Bind middleName regardless of it being empty
            $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("location: manage_students.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #e9f2fa;
    color: #333;
    margin: 0;
    padding: 0;
}

.admin-content {
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

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.teacher-content form .form-group:last-child {
    text-align: center;
}

    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="admin-content">
    <h2>Add New Student</h2>
    <p>Please fill this form to create a student account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>">
            <span class="help-block"><?php echo $firstName_err; ?></span>
        </div>    
        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middleName" class="form-control" value="<?php echo $middleName; ?>">
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>">
            <span class="help-block"><?php echo $lastName_err; ?></span>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="text" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Student">
            <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
