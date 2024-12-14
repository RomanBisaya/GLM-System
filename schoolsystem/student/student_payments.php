<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Adjust the path as needed
    exit;
}

require_once '../includes/config.php'; // Ensure this path is correct

$studentID = $_SESSION['student_id']; // Get student ID from session
$schoolYears = [];
$semesters = ['First Semester', 'Second Semester']; // Set semesters available
$selectedYear = $_POST['schoolYear'] ?? '';
$selectedSemester = $_POST['semester'] ?? '';
$paymentRecords = [];

// Fetch school years from the payment table
$yearQuery = "SELECT DISTINCT school_year
              FROM payment
              WHERE StudentID = :studentID ORDER BY school_year";
$stmt = $pdo->prepare($yearQuery);
$stmt->bindParam(':studentID', $studentID);
$stmt->execute();
$schoolYears = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payment records based on selected year and semester
if (!empty($selectedYear) && !empty($selectedSemester)) {
    $paymentQuery = "SELECT p.*, ph.amount_paid, ph.running_balance, ph.date_paid, ph.status 
                     FROM payment p
                     LEFT JOIN paymenthistory ph ON p.payment_id = ph.payment_id
                     WHERE p.StudentID = :studentID 
                     AND p.school_year = :schoolYear 
                     AND p.semester = :semester 
                     ORDER BY ph.date_paid ASC";
    $stmt = $pdo->prepare($paymentQuery);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->bindParam(':schoolYear', $selectedYear);
    $stmt->bindParam(':semester', $selectedSemester);
    $stmt->execute();
    $paymentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'sidebar.php'; // Include the student sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Payment History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
          /* General Styles */
          body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            color: #007bff;
        }

        .main-content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #007bff;
            color: #fff;
        }

        thead th {
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        tbody td {
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2>View Payment History</h2>
    <form method="POST" action="student_payments.php">
        <label for="schoolYear">School Year:</label>
        <select name="schoolYear" id="schoolYear" required>
            <option value="">Select School Year</option>
            <?php foreach ($schoolYears as $year): ?>
                <option value="<?= htmlspecialchars($year['school_year']); ?>" <?= $selectedYear == $year['school_year'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($year['school_year']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="semester">Semester:</label>
        <select name="semester" id="semester" required>
            <option value="">Select Semester</option>
            <?php foreach ($semesters as $semester): ?>
                <option value="<?= $semester; ?>" <?= $selectedSemester == $semester ? 'selected' : ''; ?>><?= $semester; ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="proceed">Proceed</button>
    </form>

    <?php if (!empty($paymentRecords)): ?>
        <h3>Payment History for <?= $selectedYear . ' - ' . $selectedSemester; ?></h3>
        <table>
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
                    $totalAmount = $paymentRecords[0]['total_amount'] ?? 0; // Use the first record to get total amount
                    $runningBalance = $totalAmount;

                    foreach ($paymentRecords as $record): 
                        // Deduct the amount paid from the running balance
                        $runningBalance -= $record['amount_paid']; 
                        
                        // Cap the running balance at zero
                        $runningBalance = max(0, $runningBalance);

                        // Determine the payment status
                        if ($record['amount_paid'] == 0) {
                            $status = 'Not Paid';
                        } elseif ($runningBalance > 0) {
                            $status = 'Partially Paid';
                        } else {
                            $status = 'Fully Paid';
                        }
                        
                        // Format date_paid or set to N/A if it's invalid
                        $formattedDate = ($record['date_paid'] === '0000-00-00' || strtotime($record['date_paid']) === false || $record['date_paid'] === '1970-01-01') 
                            ? 'N/A' 
                            : date('F d, Y', strtotime($record['date_paid']));
                ?>
                    <tr>
                        <td><?php echo number_format($totalAmount, 2); ?></td>
                        <td><?php echo number_format($record['amount_paid'], 2); ?></td>
                        <td><?php echo $formattedDate; ?></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo number_format($runningBalance, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found for the selected school year and semester.</p>
    <?php endif; ?>
</div>
</body>
</html>
