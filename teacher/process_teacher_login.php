<?php
session_start();
require_once '../includes/config.php'; // Adjust this path as necessary

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT TeacherID, FirstName, MiddleName, LastName, Username, Password FROM Teachers WHERE Username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $teacher = $stmt->fetch();
        if (password_verify($password, $teacher['Password'])) {
            // Correct password; set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['teacher_id'] = $teacher['TeacherID'];
            $_SESSION['username'] = $teacher['Username'];
            $_SESSION['name'] = $teacher['FirstName'] . ' ' . ($teacher['MiddleName'] ? $teacher['MiddleName'] . ' ' : '') . $teacher['LastName'];

            // Redirect to teacher dashboard
            header("Location: teacher_index.php"); // Adjust the redirection as needed
            exit;
        } else {
            // Password is incorrect; set error message
            $_SESSION['teacher_error'] = 'Username or Password is incorrect.';
            header("Location: login.php");
            exit();
        }
    } else {
        // Username does not exist; set error message
        $_SESSION['teacher_error'] = 'Username or Password is incorrect.';
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
