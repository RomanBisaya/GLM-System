<?php
// Include the config.php for database connection
include('../includes/config.php');

// Initialize variables
$studentID = $_GET['StudentID'] ?? '';  // Get the StudentID from the URL
$enrollmentID = $_GET['EnrollmentID'] ?? '';  // Get the EnrollmentID from the URL
$studentDetails = [];
$paymentAdded = false;

// Fetch the student and enrollment details
if ($studentID && $enrollmentID) {
    $sql = "SELECT s.FirstName, s.LastName, e.SchoolLevel, p.payment_id, p.total_amount, 
                   COALESCE(p.total_amount_paid, 0) AS amount_paid, 
                   COALESCE(p.running_balance, p.total_amount) AS running_balance
            FROM Students s
            JOIN Enrollment e ON s.StudentID = e.StudentID
            LEFT JOIN Payment p ON e.EnrollmentID = p.EnrollmentID
            WHERE s.StudentID = ? AND e.EnrollmentID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentID, $enrollmentID]);
    $studentDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Process form submission for adding a payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paymentAmount = $_POST['payment_amount'];
    $paymentDate = $_POST['payment_date'];  // The date from the form

    // Check if the payment amount is valid and student details exist
    if ($paymentAmount > 0 && $studentDetails) {
        // Calculate the new running balance
        $newAmountPaid = $studentDetails['amount_paid'] + $paymentAmount;
        $newRunningBalance = $studentDetails['total_amount'] - $newAmountPaid;

        // Determine the payment status
        if ($newRunningBalance == 0) {
            $status = 'Fully Paid';
        } elseif ($newRunningBalance < $studentDetails['total_amount']) {
            $status = 'Partially Paid';
        } else {
            $status = 'Not Paid';
        }

        // If payment record doesn't exist, insert it
        if (empty($studentDetails['payment_id'])) {
            // Insert new Payment record if it doesn't exist
            $sql = "INSERT INTO Payment (StudentID, EnrollmentID, total_amount, start_date, end_date, date_paid, running_balance, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $startDate = $paymentDate;  // Use the form date as start date
            $endDate = $paymentDate;    // End date can be same for now (adjust if needed)
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $studentID,
                $enrollmentID,
                $studentDetails['total_amount'],
                $startDate,      // Start date
                $endDate,        // End date
                $paymentDate,    // The payment date for `date_paid`
                $newRunningBalance,
                $status
            ]);

            // Get the payment_id of the newly inserted payment
            $paymentID = $pdo->lastInsertId();
        } else {
            // Update the existing Payment record for this student
            $paymentID = $studentDetails['payment_id']; // Use the existing payment_id
            $sql = "UPDATE Payment 
                    SET running_balance = ?, status = ? 
                    WHERE payment_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $newRunningBalance,
                $status,
                $paymentID
            ]);
        }

        // Insert the payment details into the PaymentHistory table
        $changeType = 'Payment Added';
        $sql = "INSERT INTO PaymentHistory (payment_id, StudentID, EnrollmentID, total_amount, amount_paid, running_balance, status, change_type, change_date, date_paid)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        
        // Add `$paymentDate` here to ensure it is inserted into PaymentHistory
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ 
            $paymentID,  // The payment_id from the Payment table
            $studentID,
            $enrollmentID,
            $studentDetails['total_amount'],
            $paymentAmount,  // Record the payment amount in PaymentHistory
            $newRunningBalance,
            $status,
            $changeType,
            $paymentDate  // Insert the payment date here
        ]);

        // Calculate the sum of amount_paid in PaymentHistory for the current payment_id
        $sql = "SELECT SUM(amount_paid) AS total_paid 
                FROM PaymentHistory 
                WHERE payment_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paymentID]);
        $totalPaid = $stmt->fetchColumn();

        // Update the total_amount_paid in the Payment table with the sum of payments
        $sql = "UPDATE Payment 
                SET total_amount_paid = ? 
                WHERE payment_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$totalPaid, $paymentID]);

        // Set a flag to indicate payment was added
        $paymentAdded = true;

        // Redirect to manage_payments.php after successful payment
        header("Location: manage_payments.php");
        exit();
    } else {
        $error = "Invalid payment amount or missing student details.";
    }
}

include 'sidebar.php';  // Include the cashier sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Reset */
body, h1, h2, p, form, input, label, button, a {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Body Styling */
body {
    background-color: #f4f7fc;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Main Content Styling */
.admin-content {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 500px;
    width: 100%;
}

.teacher-content h2 {
    color: #333;
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

/* Success and Error Messages */
.success {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    text-align: center;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    text-align: center;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-weight: bold;
    color: #555;
}

input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    width: 100%;
}

button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

/* Back Button Styling */
.back-btn {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    background-color: #6c757d;
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    text-align: center;
    font-size: 1rem;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #5a6268;
}

    </style>
</head>
<body>
<div class="admin-content">
    <h2>Add Payment for <?php echo $studentDetails['FirstName'] . ' ' . $studentDetails['LastName']; ?></h2>

    <!-- Display success message if payment was added -->
    <?php if ($paymentAdded): ?>
        <p class="success">Payment successfully added!</p>
        <a href="manage_payments.php" class="back-btn">Back to Payment Records</a>
    <?php endif; ?>

    <!-- Display error message if there's an issue with payment -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Form for adding payment -->
    <form method="POST" action="add_payment.php?StudentID=<?php echo $studentID; ?>&EnrollmentID=<?php echo $enrollmentID; ?>">
        <label for="payment_amount">Payment Amount:</label>
        <input type="number" name="payment_amount" id="payment_amount" required step="0.01" value="0.00">

        <label for="payment_date">Payment Date:</label>
        <input type="date" name="payment_date" id="payment_date" required>

        <button type="submit">Add Payment</button>
    </form>

    <a href="manage_payments.php" class="back-btn">Back to Payment Records</a>
</div>
</body>
</html>
