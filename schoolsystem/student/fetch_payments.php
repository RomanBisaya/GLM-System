<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Unauthorized access."]);
    exit;
}

$studentID = $_SESSION['student_id'];
$selectedYear = $_GET['year'] ?? date('Y');
$selectedMonth = $_GET['month'] ?? date('m');

// Fetch payments for the logged-in student based on selected year and month
try {
    $sql = "SELECT Amount, AmountPaid, StartDate, EndDate, PaymentStatus 
            FROM payments 
            WHERE StudentID = :studentID AND YEAR(StartDate) = :year AND MONTH(StartDate) = :month
            ORDER BY StartDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->bindParam(':year', $selectedYear);
    $stmt->bindParam(':month', $selectedMonth);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($payments); // Return JSON response
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching payments: " . $e->getMessage()]);
}
