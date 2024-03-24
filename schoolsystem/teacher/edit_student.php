<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Define variables and initialize with empty values
$firstName = $middleName = $lastName = $schoolLevel = $username = "";
$firstName_err = $middleName_err = $lastName_err = $schoolLevel_err = $username_err = $password_err = "";
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
    $schoolLevel = trim($_POST["schoolLevel"]);
    $username = trim($_POST["username"]);
    // Check if password field was filled out
    $password_provided = !empty(trim($_POST["password"]));
    if ($password_provided) {
        $password = trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash new password
    }

    // Prepare an update statement
    $sql = "UPDATE Students SET FirstName = :firstName, MiddleName = :middleName, LastName = :lastName, SchoolLevel = :schoolLevel, Username = :username" .
        ($password_provided ? ", Password = :password" : "") . " WHERE StudentID = :id";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
        $stmt->bindParam(":middleName", $middleName, PDO::PARAM_STR);
        $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
        $stmt->bindParam(":schoolLevel", $schoolLevel, PDO::PARAM_STR);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        if ($password_provided) {
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
    // Existing student fetching logic for initial form population
    $id = trim($_GET["id"]);
    // Continue with fetching existing data...
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
                <label>School Level</label>
                <select name="schoolLevel" class="form-control" required>
                    <option value="">Select School Level</option>
                    <option value="Junior Nursery" <?php echo ($schoolLevel == 'Junior Nursery') ? 'selected' : ''; ?>>Junior Nursery</option>
                    <option value="Senior Nursery" <?php echo ($schoolLevel == 'Senior Nursery') ? 'selected' : ''; ?>>Senior Nursery</option>
                    <option value="YS 1: Kindergarten" <?php echo ($schoolLevel == 'YS 1: Kindergarten') ? 'selected' : ''; ?>>YS 1: Kindergarten</option>
                    <option value="YS 2: Grade 1" <?php echo ($schoolLevel == 'YS 2: Grade 1') ? 'selected' : ''; ?>>YS 2: Grade 1</option>
                    <option value="YS 3: Grade 2" <?php echo ($schoolLevel == 'YS 3: Grade 2') ? 'selected' : ''; ?>>YS 3: Grade 2</option>
                    <option value="YS 4: Grade 3" <?php echo ($schoolLevel == 'YS 4: Grade 3') ? 'selected' : ''; ?>>YS 4: Grade 3</option>
                    <option value="YS 5: Grade 4" <?php echo ($schoolLevel == 'YS 5: Grade 4') ? 'selected' : ''; ?>>YS 5: Grade 4</option>
                    <option value="YS 6: Grade 5" <?php echo ($schoolLevel == 'YS 6: Grade 5') ? 'selected' : ''; ?>>YS 6: Grade 5</option>
                    <option value="YS 7: Grade 6" <?php echo ($schoolLevel == 'YS 7: Grade 6') ? 'selected' : ''; ?>>YS 7: Grade 6</option>
                </select>
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
