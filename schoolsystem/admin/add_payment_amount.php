<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$paymentId = $_GET['id'] ?? '';
$error = '';
$success = '';
$payment = [];

// Function to determine payment status
function determinePaymentStatus($amountPaid, $totalAmount) {
    if ($amountPaid >= $totalAmount) {
        return 'Fully Paid';
    } elseif ($amountPaid > 0) {
        return 'Partially Paid';
    }
    return 'Not Paid';
}

// Fetch the current payment details
if ($paymentId) {
    try {
        $sql = "SELECT * FROM payments WHERE PaymentID = :paymentId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
        $stmt->execute();
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            $error = "Payment details not found for the provided ID.";
        }
    } catch (PDOException $e) {
        $error = "Failed to fetch payment details: " . $e->getMessage();
    }
} else {
    $error = "No payment ID provided in the URL.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addPayment'])) {
    $amountToAdd = $_POST['amountToAdd'] ?? 0;

    if (!is_numeric($amountToAdd) || $amountToAdd <= 0) {
        $error = "Please enter a valid positive amount.";
    } elseif ($payment) {
        try {
            // Calculate new total paid and payment status
            $newAmountPaid = $payment['AmountPaid'] + $amountToAdd;
            $paymentStatus = determinePaymentStatus($newAmountPaid, $payment['Amount']);
            $datePaid = date('Y-m-d'); // Current date for payment

            // Check if new amount exceeds the total amount
            if ($newAmountPaid > $payment['Amount']) {
                $error = "The amount you're trying to add exceeds the total payment amount.";
            } else {
                // Update the `payments` table
                $updateSql = "UPDATE payments 
                              SET AmountPaid = :newAmountPaid, PaymentStatus = :paymentStatus, DatePaid = :datePaid 
                              WHERE PaymentID = :paymentId";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->bindParam(':newAmountPaid', $newAmountPaid);
                $updateStmt->bindParam(':paymentStatus', $paymentStatus);
                $updateStmt->bindParam(':datePaid', $datePaid);
                $updateStmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
                $updateStmt->execute();

                // Calculate the new running balance
                $runningBalance = $payment['Amount'] - $newAmountPaid;

                // Insert into `payment_history` table
                $historySql = "INSERT INTO payment_history (PaymentID, AmountPaid, RunningBalance, DatePaid, Status, TransactionDate) 
                               VALUES (:paymentId, :amountPaid, :runningBalance, :paymentDate, :status, :transactionDate)";
                $historyStmt = $pdo->prepare($historySql);
                $historyStmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
                $historyStmt->bindParam(':amountPaid', $amountToAdd);
                $historyStmt->bindParam(':runningBalance', $runningBalance);
                $historyStmt->bindParam(':paymentDate', $datePaid);
                $historyStmt->bindParam(':status', $paymentStatus);
                $historyStmt->bindParam(':transactionDate', $datePaid); // Use current date as transaction date
                $historyStmt->execute();

                $success = "Payment amount successfully added.";
                // Redirect to manage_payments.php after successful payment
                header("Location: manage_payments.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "An error occurred while processing the payment: " . $e->getMessage();
        }
    } else {
        $error = "Payment record not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment Amount</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <?php include 'sidebar2.php'; ?>

    <div class="teacher-content">
        <h2>Add Payment Amount</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <?php if ($payment): ?>
            <p><strong>Payment Details:</strong></p>
            <ul>
                <li><strong>Amount:</strong> <?= htmlspecialchars($payment['Amount']); ?></li>
                <li><strong>Amount Paid:</strong> <?= htmlspecialchars($payment['AmountPaid']); ?></li>
                <li><strong>Status:</strong> <?= htmlspecialchars($payment['PaymentStatus']); ?></li>
                <li><strong>Start Date:</strong> <?= htmlspecialchars($payment['StartDate']); ?></li>
                <li><strong>End Date:</strong> <?= htmlspecialchars($payment['EndDate']); ?></li>
            </ul>
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . htmlspecialchars($paymentId)); ?>" method="post">
                <label for="amountToAdd">Amount to Add:</label>
                <input type="number" name="amountToAdd" step="0.01" min="0.01" required>
                <button type="submit" name="addPayment" class="btn">Add Payment</button>
            </form>
        <?php else: ?>
            <p>No payment details available.</p>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
