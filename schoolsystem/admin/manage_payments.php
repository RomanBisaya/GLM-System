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
              p.total_amount_paid";

// Sort the results for consistent display
$sql .= " ORDER BY p.school_year DESC, p.semester ASC";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paymentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'sidebar.php'; // Include the cashier sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="styles.css">
    <style>
                /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        .admin-content {
            margin-left: 250px; /* Ensures content is not hidden behind the sidebar */
            padding: 20px;
            flex-grow: 1; /* Makes content area grow to fill the remaining space */
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 1200px;
            margin-top: 50px; /* Adjust for any top spacing */
            margin-bottom: 50px; /* Adjust for any bottom spacing */
        }

        /* Add Student Button */
        .add-student-btn {
            display: inline-block;
            margin: 10px 0 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }

        .add-student-btn:hover {
            background-color: #45a049;
        }

        /* Filter Form */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 180px;
            box-sizing: border-box;
        }

        form button {
            padding: 10px 20px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            text-decoration: none;
        }

        table .edit-btn {
            background-color: #FFA500;
        }

        table .edit-btn:hover {
            background-color: #e69500;
        }

        table .delete-btn {
            background-color: #FF6347;
        }

        table .delete-btn:hover {
            background-color: #e5533d;
        }

        table .add-btn {
            background-color: #4CAF50;
        }

        table .add-btn:hover {
            background-color: #45a049;
        }

        table .view-btn {
            background-color: #007BFF;
        }

        table .view-btn:hover {
            background-color: #0056b3;
        }

        /* No Records Found */
        p {
            text-align: center;
            color: #666;
            font-size: 16px;
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Action Buttons */
        table .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            text-decoration: none;
            display: inline-block; /* Ensure buttons are inline */
            margin-right: 5px; /* Add spacing between buttons */
        }

        table .btn:last-child {
            margin-right: 0; /* Remove margin from the last button */
        }

        table .edit-btn {
            background-color: #FFA500;
        }

        table .edit-btn:hover {
            background-color: #e69500;
        }

        table .delete-btn {
            background-color: #FF6347;
        }

        table .delete-btn:hover {
            background-color: #e5533d;
        }

        table .add-btn {
            background-color: #4CAF50;
        }

        table .add-btn:hover {
            background-color: #45a049;
        }

        table .view-btn {
            background-color: #007BFF;
        }

        table .view-btn:hover {
            background-color: #0056b3;
        }

        /* Responsive Adjustments for Narrow Screens */
        @media (max-width: 768px) {
            table td {
                font-size: 14px;
            }

            table .btn {
                margin-bottom: 5px; /* Add margin between rows for narrow screens */
                width: 100%; /* Make buttons full width */
                text-align: center;
            }

            table .btn:last-child {
                margin-bottom: 0; /* Remove bottom margin for the last button */
            }
        }
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        /* Add Student Button */
        .add-student-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .add-student-btn:hover {
            background-color: #0056b3;
        }

        /* Form Labels and Inputs */
        form label {
            font-weight: bold;
            margin-right: 10px;
            display: inline-block;
            margin-bottom: 5px;
        }

        form select,
        form input[type="text"],
        form button {
            padding: 8px;
            font-size: 14px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        form input[type="text"] {
            box-sizing: border-box;
        }

        form input[type="text"]:focus,
        form select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        /* Shorten the School Year Textbox */
        form input#school_year_start,
        form input#school_year_end {
            width: 80px; /* Adjust width for shorter input */
            text-align: center; /* Center align year input for better UX */
        }

        /* Form Buttons */
        form button[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        form button[type="submit"]:hover {
            background-color: #218838;
        }

        /* Dropdown Styling */
        form select {
            cursor: pointer;
            background-color: #f9f9f9;
        }

        form select:hover {
            border-color: #007BFF;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            form select,
            form input[type="text"],
            form button {
                width: 100%;
                margin-bottom: 10px; /* Stack items with some spacing */
            }

            .add-student-btn {
                display: block;
                width: 100%; /* Full width for narrow screens */
                text-align: center;
            }


        }
        </style>

    <script>
        function updateEndYear() {
            var startYear = document.getElementById("school_year_start").value;
            if (startYear.length === 4 && !isNaN(startYear)) {
                var endYear = parseInt(startYear) + 1;
                document.getElementById("school_year_end").value = endYear;
            } else {
                document.getElementById("school_year_end").value = ''; // Clear if invalid
            }
        }
    </script>
</head>
<body>
<div class="admin-content">
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

        <button type="submit" name="filter">Proceed</button>
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
                    // Calculate the running balance
                    $runningBalance = $record['total_amount'] - $record['total_amount_paid'];
                    $runningBalanceDisplay = max(0, $runningBalance); // Prevent negative values
                    ?>
                    <tr>
                        <td><?php echo $record['FirstName'] . ' ' . $record['LastName']; ?></td>
                        <td><?php echo $record['SchoolLevel']; ?></td>
                        <td><?php echo $record['school_year']; ?></td>
                        <td><?php echo $record['semester']; ?></td>
                        <td><?php echo number_format($record['total_amount'], 2); ?></td>
                        <td><?php echo number_format($record['total_amount_paid'], 2); ?></td>
                        <td><?php echo number_format($runningBalanceDisplay, 2); ?></td>
                        <td>
                            <?php
                            // Display the payment status
                            if ($runningBalance <= 0) {
                                echo 'Paid';
                            } elseif ($runningBalance < $record['total_amount']) {
                                echo 'Partially Paid';
                            } else {
                                echo 'Not Paid';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="edit_payment.php?payment_id=<?php echo $record['payment_id']; ?>" class="btn edit-btn">Edit</a>
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
