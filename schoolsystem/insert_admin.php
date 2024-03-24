<?php
require_once 'includes/config.php';

// Admin user details
$firstName = "Forrest";
$lastName = "Gump";
$username = "theadmin"; // Ensure this is unique in your database
$rawPassword = "admin12345"; // The admin's original password

// Hash the password for security
$hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

// Prepare SQL query to insert a new admin user
$sql = "INSERT INTO Admin (FirstName, LastName, Username, Password) VALUES (:firstName, :lastName, :username, :password)";
$stmt = $pdo->prepare($sql);

// Bind parameters to the prepared statement
$stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
$stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

// Execute the statement
try {
    $stmt->execute();
    echo "New admin account successfully created.";
} catch (PDOException $e) {
    die("Error creating admin account: " . $e->getMessage());
}
?>
