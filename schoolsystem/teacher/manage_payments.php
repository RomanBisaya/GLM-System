<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Fetch school levels
$schoolLevels = [];
$stmt = $pdo->query("SELECT DISTINCT SchoolLevel FROM enrollment ORDER BY SchoolLevel");
if ($stmt) {
    $schoolLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$selectedSchoolLevel = $_GET['schoolLevel'] ?? '';
$selectedYear = $_GET['year'] ?? '';
$selectedMonth = $_GET['month'] ?? '';

// Build SQL query
$sql = "SELECT s.StudentID, s.FirstName, s.LastName, p.Amount, p.AmountPaid, p.StartDate, p.EndDate, p.PaymentStatus, p.PaymentID
        FROM (
            SELECT p.PaymentID, p.StudentID, p.Amount, p.AmountPaid, p.StartDate, p.EndDate, p.PaymentStatus,
                   ROW_NUMBER() OVER (PARTITION BY p.StudentID ORDER BY p.StartDate DESC, p.PaymentID DESC) as rn
            FROM payments p
            JOIN enrollment e ON p.StudentID = e.StudentID
            WHERE (:schoolLevel IS NULL OR e.SchoolLevel = :schoolLevel)
              AND (:year IS NULL OR YEAR(p.StartDate) = :year)
              AND (:month IS NULL OR MONTH(p.StartDate) = :month)
        ) p
        JOIN students s ON p.StudentID = s.StudentID
        WHERE p.rn = 1
        ORDER BY p.StartDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':schoolLevel', $selectedSchoolLevel ?: null);
$stmt->bindValue(':year', $selectedYear ?: null);
$stmt->bindValue(':month', $selectedMonth ?: null);

$payments = [];
$error = '';
try {
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching payments: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Payments</title>
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
    overflow-x: auto; /* Allows horizontal scrolling if the content is too wide */
    overflow-y: visible; /* Prevents cutting off vertical content */
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

.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    margin-bottom: 20px;
    transition: background-color 0.3s ease;
    text-align: center;
}

.btn.add {
    background-color: #007BFF; /* Blue for add buttons */
}

.btn.add:hover {
    background-color: #0056b3;
}

.btn.edit {
    background-color: #ffc107; /* Yellow for edit buttons */
}

.btn.edit:hover {
    background-color: #e0a800;
}

.btn.delete {
    background-color: #dc3545; /* Red for delete buttons */
}

.btn.delete:hover {
    background-color: #c82333;
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
    table-layout: auto; /* Allows table to adjust its layout dynamically */
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
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
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Student Payments</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>
        <a href="add_payment.php" class="btn add">Add Payment</a>

        <!-- Dropdown for Filters -->
        <form method="get">
            <select name="schoolLevel" onchange="this.form.submit()">
                <option value="">All School Levels</option>
                <?php foreach ($schoolLevels as $level): ?>
                    <option value="<?= htmlspecialchars($level); ?>" <?= $selectedSchoolLevel == $level ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($level); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="year" onchange="this.form.submit()">
                <option value="">Select Year</option>
                <?php for($y = date('Y'); $y >= date('Y') - 10; $y--): ?>
                    <option value="<?= $y; ?>" <?= $selectedYear == $y ? 'selected' : ''; ?>><?= $y; ?></option>
                <?php endfor; ?>
            </select>
            <select name="month" onchange="this.form.submit()">
                <option value="">Select Month</option>
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m; ?>" <?= $selectedMonth == $m ? 'selected' : ''; ?>><?= date('F', mktime(0, 0, 0, $m, 10)); ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['FirstName'] . ' ' . $payment['LastName']); ?></td>
                    <td><?= htmlspecialchars($payment['Amount']); ?></td>
                    <td><?= htmlspecialchars($payment['AmountPaid']); ?></td>
                    <td><?= date('F-d-Y', strtotime($payment['StartDate'])); ?></td>
                    <td><?= date('F-d-Y', strtotime($payment['EndDate'])); ?></td>
                    <td><?= htmlspecialchars($payment['PaymentStatus']); ?></td>
                    <td>
                        <a href="edit_payment.php?id=<?= $payment['PaymentID']; ?>" class="btn edit">Edit</a>
                        <a href="delete_payment.php?id=<?= $payment['PaymentID']; ?>" onclick="return confirm('Are you sure you want to delete this payment?');" class="btn delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
