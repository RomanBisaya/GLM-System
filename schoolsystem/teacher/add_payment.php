<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$message = '';

// Fetch school levels
$schoolLevels = [];
$stmt = $pdo->query("SELECT DISTINCT SchoolLevel FROM enrollment ORDER BY SchoolLevel");
if ($stmt) {
    $schoolLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$students = [];

// Handle the payment submission and school level change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_payment'])) {
        $studentID = $_POST['studentID'];
        $totalAmount = $_POST['totalAmount'];
        $amountPaid = $_POST['amountPaid'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $paymentStatus = determinePaymentStatus($amountPaid, $totalAmount);

        $sql = "INSERT INTO payments (StudentID, Amount, AmountPaid, StartDate, EndDate, PaymentStatus) VALUES (:studentID, :totalAmount, :amountPaid, :startDate, :endDate, :paymentStatus)
                ON DUPLICATE KEY UPDATE Amount = :totalAmount, AmountPaid = :amountPaid, StartDate = :startDate, EndDate = :endDate, PaymentStatus = :paymentStatus";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':totalAmount', $totalAmount);
        $stmt->bindParam(':amountPaid', $amountPaid);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':paymentStatus', $paymentStatus);
        if ($stmt->execute()) {
            // Redirect to manage_payments.php after successful update
            header("location: manage_payments.php");
            exit;
        } else {
            $message = "An error occurred while updating the payment.";
        }
    }

    // Filter students by school level
    if (!empty($_POST['schoolLevel'])) {
        $schoolLevel = $_POST['schoolLevel'];
        $stmt = $pdo->prepare("SELECT DISTINCT students.StudentID, students.FirstName, students.LastName FROM enrollment JOIN students ON enrollment.StudentID = students.StudentID WHERE enrollment.SchoolLevel = :schoolLevel GROUP BY students.StudentID");
        $stmt->bindParam(':schoolLevel', $schoolLevel);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Payment</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
}

.teacher-content {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

h2 {
    text-align: center;
    color: #007BFF;
    font-size: 24px;
    margin-bottom: 20px;
}

p {
    color: red;
    text-align: center;
}

form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

button[type="submit"] {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    background-color: #007BFF;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: 100%;
    position: relative;
    bottom: 0;
    left: 0;
    clear: both;
    font-size: 14px;
}

footer a {
    color: white; /* Ensures links in the footer are also white */
    text-decoration: none;
}

footer a:hover {
    color: #dcdcdc; /* Lightens the color when hovered over */
}
    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Add Student Payment</h2>
        <p><?php echo $message; ?></p>

        <form action="add_payment.php" method="post">
            <div class="form-group">
                <label for="schoolLevel">School Level:</label>
                <select name="schoolLevel" id="schoolLevel" onchange="this.form.submit()">
                    <option value="">Select School Level</option>
                    <?php foreach ($schoolLevels as $level): ?>
                        <option value="<?= htmlspecialchars($level); ?>" <?= (isset($_POST['schoolLevel']) && $_POST['schoolLevel'] == $level) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="studentID">Student:</label>
                <select name="studentID" id="studentID" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['StudentID']; ?>">
                            <?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="totalAmount">Total Amount:</label>
                <input type="number" name="totalAmount" id="totalAmount" required>
            </div>

            <div class="form-group">
                <label for="amountPaid">Amount Paid:</label>
                <input type="number" name="amountPaid" id="amountPaid" required>
            </div>

            <div class="form-group">
                <label for="startDate">Start Date:</label>
                <input type="date" name="startDate" id="startDate" required>
            </div>

            <div class="form-group">
                <label for="endDate">End Date:</label>
                <input type="date" name="endDate" id="endDate" required>
            </div>

            <button type="submit" name="submit_payment">Submit Payment</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
