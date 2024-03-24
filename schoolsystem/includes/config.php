<?php
$host = 'localhost';
$dbname = 'schoolsystem_db'; // Your database name
$username = 'root'; // Your database username, 'root' is default in XAMPP
$password = ''; // Your database password, default is empty in XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}
?>
