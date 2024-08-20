<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

$studentID = $_SESSION['student_id'];
$selectedYear = $_GET['year'] ?? date('Y');
$selectedMonth = $_GET['month'] ?? date('m');

// Fetch payments for the logged-in student based on selected year and month
$payments = [];
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
} catch (PDOException $e) {
    $error = "Error fetching payments: " . $e->getMessage();
}

// Get years and months for filter
$yearsMonths = $pdo->query("SELECT DISTINCT YEAR(StartDate) AS Year, MONTH(StartDate) AS Month FROM payments WHERE StudentID = $studentID ORDER BY Year DESC, Month DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Payments</title>
    <link rel="stylesheet" href="../css/student_style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
}

.main-content {
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

.error {
    color: #FF0000;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}

form {
    margin-bottom: 20px;
    text-align: center;
}

select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
    width: 150px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
    table-layout: fixed; /* Ensures the table fits within the container */
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
    word-wrap: break-word; /* Ensures content breaks into a new line if it's too long */
}

th {
    background-color: #007BFF;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f8ff;
}

tr:hover {
    background-color: #e1ecff;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: 100%;
    clear: both; /* Ensures the footer stays below the content */
    position: relative;
    bottom: 0;
    left: 0;
}

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Your Payment History</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Year and Month Filter Form -->
        <form action="" method="get">
            <select name="year" onchange="this.form.submit()">
                <?php foreach (array_unique(array_column($yearsMonths, 'Year')) as $year): ?>
                <option value="<?= $year; ?>" <?= $year == $selectedYear ? 'selected' : ''; ?>><?= $year; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="month" onchange="this.form.submit()">
                <?php foreach (array_unique(array_column($yearsMonths, 'Month')) as $month): ?>
                <option value="<?= $month; ?>" <?= $month == $selectedMonth ? 'selected' : ''; ?>><?= date('F', mktime(0, 0, 0, $month, 1)); ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['Amount']); ?></td>
                    <td><?= htmlspecialchars($payment['AmountPaid']); ?></td>
                    <td><?= date('F-d-Y', strtotime($payment['StartDate'])); ?></td>
                    <td><?= date('F-d-Y', strtotime($payment['EndDate'])); ?></td>
                    <td><?= htmlspecialchars($payment['PaymentStatus']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
