<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Adjust the path as needed
    exit;
}

require_once '../includes/config.php'; // Database connection

// Initialize variables and errors
$firstName = $lastName = $username = $password = "";
$firstName_err = $lastName_err = $username_err = $password_err = "";

// Fetch current admin details from the session
$adminID = $_SESSION['admin_id']; // Ensure admin_id is stored in session when the admin logs in

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate first name
    if (empty(trim($_POST["firstName"]))) {
        $firstName_err = "Please enter your first name.";
    } else {
        $firstName = trim($_POST["firstName"]);
    }

    // Validate last name
    if (empty(trim($_POST["lastName"]))) {
        $lastName_err = "Please enter your last name.";
    } else {
        $lastName = trim($_POST["lastName"]);
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password (only if provided)
    if (!empty(trim($_POST["password"]))) {
        $password = trim($_POST["password"]);
        if (strlen($password) < 6) {
            $password_err = "Password must be at least 6 characters long.";
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        }
    }

    // Check for errors before updating the database
    if (empty($firstName_err) && empty($lastName_err) && empty($username_err) && empty($password_err)) {
        $sql = "UPDATE admin SET FirstName = :firstName, LastName = :lastName, Username = :username";
        if (!empty($password)) {
            $sql .= ", Password = :password";
        }
        $sql .= " WHERE AdminID = :adminID";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
            $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":adminID", $adminID, PDO::PARAM_INT);
            if (!empty($password)) {
                $stmt->bindParam(":password", $password, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                // Update session variables
                $_SESSION["name"] = $firstName . ' ' . $lastName;
                $_SESSION["username"] = $username;

            echo '<div style="text-align: center; margin-top: 20px;">Your account has been updated successfully!</div>';
            } else {
            echo '<div style="text-align: center; margin-top: 20px;">Oops! Something went wrong. Please try again later.</div>';
            }
        }
        unset($stmt);
    }
}

// Fetch current admin details from the database
$sql = "SELECT FirstName, LastName, Username FROM admin WHERE AdminID = :adminID";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":adminID", $adminID, PDO::PARAM_INT);
    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $firstName = $row["FirstName"];
            $lastName = $row["LastName"];
            $username = $row["Username"];
        } else {
            echo "Error: Admin not found.";
            exit();
        }
    }
    unset($stmt);
}

require_once 'sidebar.php'; // Include sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
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

form {
    width: 100%;
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

.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.form-group .error {
    color: #FF0000;
    font-size: 14px;
    margin-top: 5px;
    display: block;
}

.btn.btn-primary {
    display: inline-block;
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s ease;
    cursor: pointer;
    border: none;
}

.btn.btn-primary:hover {
    background-color: #0056b3;
}

.btn.btn-primary:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
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
}


    </style>
</head>
<body>
    <div class="admin-content">
        <h2>Admin Settings</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" class="form-control" required>
                <span class="error"><?php echo $firstName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" class="form-control" required>
                <span class="error"><?php echo $lastName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" required>
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password (Leave blank to keep current password)</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password to reset">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Save Changes">
        </form>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>

<?php
unset($pdo);
?>
