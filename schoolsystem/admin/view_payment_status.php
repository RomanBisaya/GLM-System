<?php
// Include the config.php for database connection
include('../includes/config.php');  // Adjust the path if necessary

// Initialize variables
$paymentID = isset($_GET['payment_id']) ? $_GET['payment_id'] : ''; // Get the payment_id from the URL
$paymentDetails = [];
$paymentHistory = [];

// Check if the payment_id exists in the URL
if ($paymentID) {
    // Fetch student and payment details based on the payment_id
    $sql = "SELECT 
                s.FirstName, s.LastName, 
                e.SchoolLevel, 
                p.school_year, p.semester, 
                p.total_amount
            FROM Payment p
            JOIN Students s ON p.StudentID = s.StudentID
            JOIN Enrollment e ON p.EnrollmentID = e.EnrollmentID
            WHERE p.payment_id = ?";

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paymentID]);
    $paymentDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch all payment history for this payment_id
    $historySql = "SELECT 
                        ph.amount_paid, ph.date_paid, ph.running_balance, ph.status
                   FROM PaymentHistory ph
                   WHERE ph.payment_id = ?
                   ORDER BY ph.date_paid ASC";
    $historyStmt = $pdo->prepare($historySql);
    $historyStmt->execute([$paymentID]);
    $paymentHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Include the sidebar for consistency across pages
include 'sidebar.php';  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Reset */
body, h1, h2, h3, p, ul, ol, table, th, td {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    color: #333;
}

/* Body Styling */
body {
    background-color: #f9f9f9;
    padding: 20px;
    line-height: 1.6;
}

/* Teacher Content Styling */
.admin-content {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Header Styling */
.admin-content h2 {
    color: #007BFF;
    margin-bottom: 20px;
    text-align: center;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table thead {
    background-color: #007BFF;
    color: white;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

table th {
    font-weight: bold;
    text-transform: uppercase;
}

table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tbody tr:hover {
    background-color: #e8f4ff;
}

/* Button Styling */
.back-btn {
    display: inline-block;
    text-decoration: none;
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-align: center;
    font-size: 16px;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #0056b3;
}

/* Status Column Styling */
td {
    vertical-align: middle;
}

td:nth-child(4) {
    font-weight: bold;
    color: #0056b3;
}

    </style>
</head>
<body>
<div class="admin-content">
    <h2>Payment Status for <?php echo isset($paymentDetails['FirstName']) ? $paymentDetails['FirstName'] . ' ' . $paymentDetails['LastName'] : 'Unknown Student'; ?></h2>

    <!-- Display Payment History -->
    <?php if ($paymentDetails): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                    <th>Running Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Start by getting the total amount and initialize the running balance
                $totalAmount = $paymentDetails['total_amount'];
                $runningBalance = $totalAmount;  // Initial running balance is the total amount
                
                // If there is any payment history, loop through it and display it
                if (!empty($paymentHistory)) {
                    foreach ($paymentHistory as $record):
                        $amountPaid = $record['amount_paid'];
                        $runningBalance -= $amountPaid; // Subtract the amount paid from the running balance

                        // Cap the running balance at zero to avoid negative numbers
                        $runningBalance = max(0, $runningBalance);

                        // Determine the payment status based on the running balance
                        if ($amountPaid == 0) {
                            $status = 'Not Paid';  // If no payment has been made yet
                        } elseif ($runningBalance > 0) {
                            $status = 'Partially Paid';  // If balance is still remaining
                        } else {
                            $status = 'Paid';  // If the balance is zero or less, it's fully paid
                        }

                        // Format the date_paid or set it to "N/A" if it is NULL
                        $datePaid = ($record['date_paid'] == NULL) ? 'N/A' : date('F-d-Y', strtotime($record['date_paid']));
                    ?>
                        <tr>
                            <td><?php echo number_format($totalAmount, 2); ?></td>
                            <td><?php echo number_format($amountPaid, 2); ?></td>
                            <td><?php echo $datePaid; ?></td>
                            <td><?php echo $status; ?></td>
                            <td><?php echo number_format($runningBalance, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No payment history available.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="manage_payments.php" class="back-btn">Back to Payment Records</a>
    <?php else: ?>
        <p>Payment record not found.</p>
    <?php endif; ?>
</div>
</body>
</html>
