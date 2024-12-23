<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php'; // Database connection

// Initialize variables and errors
$firstName = $middleName = $lastName = $username = $password = "";
$firstName_err = $middleName_err = $lastName_err = $username_err = $password_err = "";

// Check if the TeacherID is provided in the URL
if (isset($_GET["TeacherID"]) && !empty(trim($_GET["TeacherID"]))) {
    $teacherID = trim($_GET["TeacherID"]);

    // Fetch the existing teacher details from the database
    $sql = "SELECT * FROM Teachers WHERE TeacherID = :teacherID";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":teacherID", $teacherID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $firstName = $row["FirstName"];
                $middleName = $row["MiddleName"];
                $lastName = $row["LastName"];
                $username = $row["Username"];
            } else {
                echo "Error: Teacher not found.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }
    }
    unset($stmt);
} else {
    // Redirect to error page if no valid TeacherID is provided
    header("location: error.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["FirstName"]))) {
        $firstName_err = "Please enter the first name.";
    } else {
        $firstName = trim($_POST["FirstName"]);
    }

    // Validate last name
    if (empty(trim($_POST["LastName"]))) {
        $lastName_err = "Please enter the last name.";
    } else {
        $lastName = trim($_POST["LastName"]);
    }

    // Validate username
    if (empty(trim($_POST["Username"]))) {
        $username_err = "Please enter the username.";
    } else {
        $username = trim($_POST["Username"]);
    }

    // Validate password (only if provided)
    if (!empty(trim($_POST["Password"]))) {
        $password = trim($_POST["Password"]);
        if (strlen($password) < 6) {
            $password_err = "Password must be at least 6 characters long.";
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        }
    }

    // Check for errors before updating the database
    if (empty($firstName_err) && empty($lastName_err) && empty($username_err) && empty($password_err)) {
        $sql = "UPDATE Teachers SET FirstName = :firstName, MiddleName = :middleName, LastName = :lastName, Username = :username";
        if (!empty($password)) {
            $sql .= ", Password = :password";
        }
        $sql .= " WHERE TeacherID = :teacherID";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
            $stmt->bindParam(":middleName", $middleName, PDO::PARAM_STR);
            $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":teacherID", $teacherID, PDO::PARAM_INT);
            if (!empty($password)) {
                $stmt->bindParam(":password", $password, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                echo '<div style="text-align: center; margin-top: 20px;">Teacher information updated successfully!</div>';
            } else {
                echo '<div style="text-align: center; margin-top: 20px;">Oops! Something went wrong. Please try again later.</div>';
            }
        }
        unset($stmt);
    }
}

require_once 'sidebar.php'; // Include sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    .admin-content {
        max-width: 800px;
        margin: 50px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .error {
        color: #d9534f;
        font-size: 12px;
        margin-top: 5px;
    }

    .success-message {
        text-align: center;
        font-size: 16px;
        color: #28a745;
        margin-top: 20px;
    }

    .error-message {
        text-align: center;
        font-size: 16px;
        color: #d9534f;
        margin-top: 20px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .admin-content {
            padding: 15px;
        }

        .btn {
            width: 100%;
            padding: 12px;
        }
    }
</style>

</head>
<body>
    <div class="admin-content">
        <h2>Edit Teacher</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?TeacherID=' . $teacherID; ?>" method="post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="FirstName" value="<?php echo htmlspecialchars($firstName); ?>" class="form-control" required>
                <span class="error"><?php echo $firstName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Middle Name (optional)</label>
                <input type="text" name="MiddleName" value="<?php echo htmlspecialchars($middleName); ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="LastName" value="<?php echo htmlspecialchars($lastName); ?>" class="form-control" required>
                <span class="error"><?php echo $lastName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="Username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" required>
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password (Leave blank to keep current password)</label>
                <input type="password" name="Password" class="form-control" placeholder="Enter new password to reset">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Save Changes">
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>

<?php
unset($pdo);
?>
