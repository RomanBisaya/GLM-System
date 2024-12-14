<?php
// Include the config.php for database connection
include('../includes/config.php');

// Check if payment_id, StudentID, and EnrollmentID are provided
if (isset($_GET['payment_id']) && isset($_GET['StudentID']) && isset($_GET['EnrollmentID'])) {
    $paymentID = $_GET['payment_id'];
    $studentID = $_GET['StudentID'];
    $enrollmentID = $_GET['EnrollmentID'];
} else {
    die('Missing required parameters.');
}

// Begin transaction to ensure atomic operation
$pdo->beginTransaction();

try {
    // Delete the payment history records related to the payment_id
    $deleteHistoryQuery = "DELETE FROM paymenthistory WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($deleteHistoryQuery);
    $stmt->bindParam(':payment_id', $paymentID, PDO::PARAM_INT);
    $stmt->execute();

    // Delete the payment record from the Payment table
    $deletePaymentQuery = "DELETE FROM Payment WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($deletePaymentQuery);
    $stmt->bindParam(':payment_id', $paymentID, PDO::PARAM_INT);
    $stmt->execute();

    // Commit the transaction
    $pdo->commit();

    // Redirect to manage payments page after deletion
    header("Location: manage_payments.php");
    exit;
} catch (Exception $e) {
    // Roll back the transaction if an error occurs
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
