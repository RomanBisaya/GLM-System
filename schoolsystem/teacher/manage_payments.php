<?php
// Include the config.php for database connection
include('../includes/config.php'); // Adjust the path if necessary

// Initialize variables
$schoolLevel = '';
$schoolYearStart = '';
$schoolYearEnd = '';
$semester = '';
$paymentRecords = [];
$schoolLevels = [];

// Fetch distinct school levels from the Enrollment table
$sql = "SELECT DISTINCT SchoolLevel FROM Enrollment";
$stmt = $pdo->query($sql);
$schoolLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build the SQL query to fetch payment records
$sql = "SELECT 
            s.FirstName, 
            s.LastName, 
            e.SchoolLevel, 
            p.school_year, 
            p.semester, 
            p.total_amount, 
            p.total_amount_paid, 
            p.payment_id, 
            p.StudentID, 
            p.EnrollmentID
        FROM Payment p
        JOIN Students s ON p.StudentID = s.StudentID
        JOIN Enrollment e ON p.EnrollmentID = e.EnrollmentID";

// Apply filters if provided
$conditions = [];
$params = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['school_level'])) {
        $schoolLevel = $_POST['school_level'];
        $conditions[] = "e.SchoolLevel = ?";
        $params[] = $schoolLevel;
    }
    if (!empty($_POST['school_year_start']) && !empty($_POST['school_year_end'])) {
        $schoolYearStart = $_POST['school_year_start'];
        $schoolYearEnd = $_POST['school_year_end'];
        // Filter school_year based on the range
        $conditions[] = "p.school_year BETWEEN ? AND ?";
        $params[] = $schoolYearStart;
        $params[] = $schoolYearEnd;
    }
    if (!empty($_POST['semester'])) {
        $semester = $_POST['semester'];
        $conditions[] = "p.semester = ?";
        $params[] = $semester;
    }
}

// Add conditions to the query if filters exist
if ($conditions) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Group by the payment ID to avoid duplicate rows
$sql .= " GROUP BY 
              p.payment_id, 
              s.FirstName, 
              s.LastName, 
              e.SchoolLevel, 
              p.school_year, 
              p.semester, 
              p.total_amount, 
              p.total_amount_paid"; // Remove running_balance from GROUP BY

// Sort the results for consistent display
$sql .= " ORDER BY p.school_year DESC, p.semester ASC";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paymentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'sidebar2.php'; // Include the cashier sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="styles.css">

    <script>
        // JavaScript function to automatically update the second input based on the first
        function updateEndYear() {
            var startYear = document.getElementById("school_year_start").value;
            if (startYear.length === 4 && !isNaN(startYear)) {
                var endYear = parseInt(startYear) + 1;
                document.getElementById("school_year_end").value = endYear;
            } else {
                document.getElementById("school_year_end").value = ''; // Clear the second field if invalid
            }
        }
    </script>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

   

    h2, h3 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    form label {
        margin-right: 5px;
        font-weight: bold;
    }

    form select, 
    form input[type="text"], 
    form button {
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    form button {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #0056b3;
    }

    .add-student-btn {
        display: inline-block;
        margin-bottom: 15px;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .add-student-btn:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #f2f2f2;
        color: #333;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .action-buttons a {
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 12px;
        font-weight: bold;
        margin: 0 5px;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .action-buttons a:nth-child(1) {
        background-color: #28a745; /* Green - Edit */
        color: white;
    }

    .action-buttons a:nth-child(1):hover {
        background-color: #218838;
    }

    .action-buttons a:nth-child(2) {
        background-color: #dc3545; /* Red - Delete */
        color: white;
    }

    .action-buttons a:nth-child(2):hover {
        background-color: #c82333;
    }

    .action-buttons a:nth-child(3) {
        background-color: #ffc107; /* Yellow - Add Payment */
        color: black;
    }

    .action-buttons a:nth-child(3):hover {
        background-color: #e0a800;
    }

    .action-buttons a:nth-child(4) {
        background-color: #17a2b8; /* Cyan - View Status */
        color: white;
    }

    .action-buttons a:nth-child(4):hover {
        background-color: #117a8b;
    }

    .no-records {
        text-align: center;
        color: #888;
        font-size: 16px;
        margin-top: 20px;
    }
    /* General button styling */
.btn {
    display: inline-block;
    padding: 8px 12px;
    margin: 2px;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    color: white;
}

/* Specific button colors */
.edit-btn {
    background-color: #4CAF50; /* Green */
}

.delete-btn {
    background-color: #f44336; /* Red */
}

.add-btn {
    background-color: #2196F3; /* Blue */
}

.view-btn {
    background-color: #FF9800; /* Orange */
}

/* Button hover effects */
.btn:hover {
    opacity: 0.9;
}

    </style>
</head>
<body>
<div class="teacher-content">
    <h2>Manage Payments</h2>

    <!-- Add Student Button -->
    <a href="add_student_payment.php" class="add-student-btn">Add Student to Payment List</a>

    <!-- Filter Form -->
    <form method="POST" action="manage_payments.php">
        <label for="school_level">School Level:</label>
        <select name="school_level" id="school_level">
            <option value="">All Levels</option>
            <?php foreach ($schoolLevels as $level): ?>
                <option value="<?php echo $level['SchoolLevel']; ?>" <?php echo ($schoolLevel == $level['SchoolLevel']) ? "selected" : ""; ?>>
                    <?php echo $level['SchoolLevel']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="school_year_start">School Year Start:</label>
        <input type="text" name="school_year_start" id="school_year_start" value="<?php echo $schoolYearStart; ?>" maxlength="4" oninput="updateEndYear()">

        <label for="school_year_end">-</label>
        <input type="text" name="school_year_end" id="school_year_end" maxlength="4" value="<?php echo $schoolYearEnd; ?>" readonly>

        <label for="semester">Semester:</label>
        <select name="semester" id="semester">
            <option value="">All Semesters</option>
            <option value="First Semester" <?php echo ($semester == "First Semester") ? "selected" : ""; ?>>First Semester</option>
            <option value="Second Semester" <?php echo ($semester == "Second Semester") ? "selected" : ""; ?>>Second Semester</option>
        </select>

        <button type="submit" name="filter">Filter</button>
    </form>

    <!-- Display Payment Records -->
    <h3>Payment Records</h3>
    <?php if (!empty($paymentRecords)): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>School Level</th>
                    <th>School Year</th>
                    <th>Semester</th>
                    <th>Total Amount</th>
                    <th>Total Amount Paid</th>
                    <th>Running Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentRecords as $record): ?>
                    <?php 
                    // Calculate the running_balance as total_amount - total_amount_paid
                    $runningBalance = $record['total_amount'] - $record['total_amount_paid'];
                    ?>
                    <tr>
                        <td><?php echo $record['FirstName'] . ' ' . $record['LastName']; ?></td>
                        <td><?php echo $record['SchoolLevel']; ?></td>
                        <td><?php echo $record['school_year']; ?></td>
                        <td><?php echo $record['semester']; ?></td>
                        <td><?php echo number_format($record['total_amount'], 2); ?></td>
                        <td><?php echo number_format($record['total_amount_paid'], 2); ?></td>
                        <td><?php echo number_format($runningBalance, 2); ?></td>
                        <td>
                            <?php
                            // Display the payment status
                            if ($runningBalance == 0) {
                                echo 'Paid';
                            } elseif ($runningBalance < $record['total_amount']) {
                                echo 'Partially Paid';
                            } else {
                                echo 'Not Paid';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="edit_payment.php?payment_id=<?php echo $record['payment_id']; ?>&StudentID=<?php echo $record['StudentID']; ?>&EnrollmentID=<?php echo $record['EnrollmentID']; ?>" class="btn edit-btn">Edit</a>
                            <a href="delete_payment.php?payment_id=<?php echo $record['payment_id']; ?>&StudentID=<?php echo $record['StudentID']; ?>&EnrollmentID=<?php echo $record['EnrollmentID']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this payment record?')">Delete</a>
                            <a href="add_payment.php?StudentID=<?php echo $record['StudentID']; ?>&EnrollmentID=<?php echo $record['EnrollmentID']; ?>" class="btn add-btn">Add Payment</a>
                            <a href="view_payment_status.php?payment_id=<?php echo $record['payment_id']; ?>" class="btn view-btn">View Status</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payment records found.</p>
    <?php endif; ?>
</div>
</body>
</html>
