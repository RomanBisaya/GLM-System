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

// Initialize variables for form fields
$amountPaid = '';
$datePaid = '';
$status = '';
$runningBalance = 0;

// Fetch payment details
$query = "SELECT p.*, ph.amount_paid, ph.date_paid, ph.running_balance, s.FirstName, s.LastName 
          FROM Payment p
          LEFT JOIN paymenthistory ph ON p.payment_id = ph.payment_id
          JOIN Students s ON p.StudentID = s.StudentID
          WHERE p.payment_id = :payment_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['payment_id' => $paymentID]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if payment record exists
if (!$payment) {
    die('Payment record not found.');
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $amountPaid = $_POST['amount_paid'];
    $datePaid = $_POST['date_paid'];
    $status = $_POST['status'];

    // Update the total_amount_paid directly
    $updateQuery = "UPDATE Payment 
                    SET total_amount_paid = :amount_paid 
                    WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':amount_paid', $amountPaid, PDO::PARAM_STR);
    $stmt->bindParam(':payment_id', $paymentID, PDO::PARAM_INT);
    $stmt->execute();

    // Recalculate the running_balance after updating total_amount_paid
    $runningBalance = $payment['total_amount'] - $amountPaid;

    // Insert a new record into the paymenthistory table
    $historyQuery = "INSERT INTO paymenthistory (payment_id, StudentID, EnrollmentID, amount_paid, running_balance, date_paid, status) 
                     VALUES (:payment_id, :student_id, :enrollment_id, :amount_paid, :running_balance, :date_paid, :status)";
    $stmt = $pdo->prepare($historyQuery);
    $stmt->execute([
        'payment_id' => $paymentID,
        'student_id' => $studentID,
        'enrollment_id' => $enrollmentID,
        'amount_paid' => $amountPaid,
        'running_balance' => $runningBalance,
        'date_paid' => $datePaid,
        'status' => $status
    ]);

    // Redirect to manage payments
    header("Location: manage_payments.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
}

.admin-content {
    margin: 20px auto;
    padding: 20px;
    max-width: 600px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

p {
    font-size: 16px;
    line-height: 1.5;
    margin-bottom: 10px;
    color: #555;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-weight: bold;
    color: #333;
}

input, select {
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
}

input:focus, select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.button-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

button, .cancel-btn {
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button {
    background-color: #007bff;
    color: #fff;
}

button:hover {
    background-color: #0056b3;
}

.cancel-btn {
    text-decoration: none;
    background-color: #dc3545;
    color: #fff;
    text-align: center;
}

.cancel-btn:hover {
    background-color: #c82333;
}

    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="admin-content">
    <h2>Edit Payment</h2>

    <!-- Display payment details -->
    <p><strong>Student Name:</strong> <?php echo $payment['FirstName'] . ' ' . $payment['LastName']; ?></p>
    <p><strong>Total Amount:</strong> <?php echo number_format($payment['total_amount'], 2); ?></p>
    <p><strong>Total Amount Paid:</strong> <?php echo number_format($payment['total_amount_paid'], 2); ?></p>
    <p><strong>Running Balance:</strong> <?php echo number_format($payment['total_amount'] - $payment['total_amount_paid'], 2); ?></p>

    <!-- Edit payment form -->
    <form method="POST" action="">
        <label for="amount_paid">Amount Paid:</label>
        <input type="text" id="amount_paid" name="amount_paid" required>
        
        <label for="date_paid">Date Paid:</label>
        <input type="date" id="date_paid" name="date_paid" required>
        
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Paid">Paid</option>
            <option value="Partially Paid">Partially Paid</option>
            <option value="Not Paid">Not Paid</option>
        </select>
        
        <div class="button-group">
            <button type="submit">Update Payment</button>
            <a href="manage_payments.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
