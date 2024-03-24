<?php
session_start();
require_once '../includes/config.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT AdminID, FirstName, LastName, Username, Password FROM Admin WHERE Username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $admin = $stmt->fetch();
        if (password_verify($password, $admin['Password'])) {
            // Correct password; set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['username'] = $admin['Username']; // Keep this if you use the username somewhere
            $_SESSION['name'] = $admin['FirstName'] . " " . $admin['LastName']; // Concatenate first and last name

            // Redirect to admin dashboard
            header("Location: index.php");
            exit;
        } else {
            // Password is incorrect; set error message
            $_SESSION['error'] = 'Username or Password is incorrect.';
            header("Location: login.php");
            exit();
        }
    } else {
        // Username does not exist; set error message
        $_SESSION['error'] = 'Username or Password is incorrect.';
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
