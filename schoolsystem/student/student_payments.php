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
