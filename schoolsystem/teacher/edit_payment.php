<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$paymentId = $_GET['id'] ?? '';
$payment = [];
$error = '';

// Fetch the current payment details
if ($paymentId) {
    $sql = "SELECT * FROM payments WHERE PaymentID = :paymentId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Error fetching payment details: " . implode(", ", $stmt->errorInfo());
    }
}

function determinePaymentStatus($amountPaid, $totalAmount) {
    if ($amountPaid >= $totalAmount) {
        return 'Fully Paid';
    } elseif ($amountPaid > 0) {
        return 'Partially Paid';
    }
    return 'Not Paid';
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Get form data
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $paymentStatus = determinePaymentStatus($amountPaid, $amount);

    // Update payment details
    $sql = "UPDATE payments SET Amount = :amount, AmountPaid = :amountPaid, StartDate = :startDate, EndDate = :endDate, PaymentStatus = :paymentStatus WHERE PaymentID = :paymentId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':amountPaid', $amountPaid);
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
    $stmt->bindParam(':paymentStatus', $paymentStatus);
    $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("location: manage_payments.php"); // Redirect after update
        exit;
    } else {
        $error = "Error updating payment details: " . implode(", ", $stmt->errorInfo());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>

    <div class="teacher-content">
        <h2>Edit Payment</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $paymentId); ?>" method="post">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" value="<?= $payment['Amount'] ?? ''; ?>" required>

            <label for="amountPaid">Amount Paid:</label>
            <input type="number" name="amountPaid" value="<?= $payment['AmountPaid'] ?? ''; ?>" required>

            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" value="<?= $payment['StartDate'] ?? ''; ?>" required>

            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" value="<?= $payment['EndDate'] ?? ''; ?>" required>

            <button type="submit" name="update" class="btn">Update Payment</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
