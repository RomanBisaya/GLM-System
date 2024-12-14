<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start session at the very top

// Check for error message in session and then clear it
if (isset($_SESSION['student_error'])) {
    $errorMessage = $_SESSION['student_error'];
    unset($_SESSION['student_error']); // Clear the error message after using it
} else {
    $errorMessage = "";
}

include '../includes/header.php'; // Include the header file. Adjust the path if necessary.
?>

<div class="login-container">
    <h2>Student Login</h2>
    <form action="process_student_login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" 
                   class="<?php echo (!empty($errorMessage)) ? 'input-error' : ''; ?>" 
                   required>
            <?php if (!empty($errorMessage)): ?>
            <span class="error-message">Username or Password is incorrect.</span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" 
                   class="<?php echo (!empty($errorMessage)) ? 'input-error' : ''; ?>" 
                   required>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
</div>
<br>
<br>
<?php include '../includes/footer.php'; // Include the footer file. Adjust the path if necessary. ?>

<style>
/* General container styling */
.login-container {
    width: 100%;
    max-width: 400px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

/* Input styling */
input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Error input styling */
input.input-error {
    border: 1px solid red;
    background-color: #ffe6e6;
}

/* Error message styling */
.error-message {
    color: red;
    font-size: 12px;
    margin-top: -10px;
    display: block;
}

/* Submit button styling */
button {
    width: 100%;
    padding: 10px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}
</style>
