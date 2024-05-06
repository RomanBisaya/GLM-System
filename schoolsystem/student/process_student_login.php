<?php
session_start();
require_once '../includes/config.php'; // Ensure this path is correct

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SQL to select student based on username
    $sql = "SELECT StudentID, FirstName, MiddleName, LastName, Username, Password FROM Students WHERE Username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $student = $stmt->fetch();
        if (password_verify($password, $student['Password'])) {
            // Correct password; set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['student_id'] = $student['StudentID'];
            $_SESSION['username'] = $student['Username'];
            $_SESSION['name'] = $student['FirstName'] . ' ' . ($student['MiddleName'] ? $student['MiddleName'] . ' ' : '') . $student['LastName'];

            // Redirect to student dashboard or home page
            header("Location: student_index.php"); // Adjust the redirection URL to your student dashboard page
            exit;
        } else {
            // Password is incorrect; set error message
            $_SESSION['student_error'] = 'Username or Password is incorrect.';
            header("Location: login.php"); // Redirect back to the student login page
            exit();
        }
    } else {
        // Username does not exist; set error message
        $_SESSION['student_error'] = 'Username or Password is incorrect.';
        header("Location: login.php"); // Redirect back to the student login page
        exit();
    }
} else {
    // No login form has been submitted; redirect to the login page
    header("Location: login.php");
    exit();
}
?>
