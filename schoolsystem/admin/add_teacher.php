<?php
require_once '../includes/config.php'; // Adjust the path as needed

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare an insert statement
    $sql = "INSERT INTO Teachers (FirstName, MiddleName, LastName, Username, Password) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(1, $_POST['FirstName'], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST['MiddleName'], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST['LastName'], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST['Username'], PDO::PARAM_STR);
        
        // Hash the password before storing it
        $hashedPassword = password_hash($_POST['Password'], PASSWORD_DEFAULT);
        $stmt->bindParam(5, $hashedPassword, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records created successfully. Redirect to landing page
            header("location: manage_teachers.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    unset($stmt);
}

// Close connection
unset($pdo);
?>
