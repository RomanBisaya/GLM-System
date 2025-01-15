<?php
// Include the config.php for database connection
include('../includes/config.php');  // Adjust the path if necessary

// Initialize variables
$enrollmentID = '';
$totalAmount = '';
$schoolYearStart = '';
$schoolYearEnd = '';
$semester = '';
$students = [];

// Fetch unique students with their EnrollmentID, ensuring no duplicates
$sql = "SELECT MIN(e.EnrollmentID) AS EnrollmentID, s.StudentID, s.FirstName, s.LastName
        FROM Enrollment e
        JOIN Students s ON e.StudentID = s.StudentID
        WHERE NOT EXISTS (
            SELECT 1 
            FROM Payment p 
            WHERE p.EnrollmentID = e.EnrollmentID
        )
        GROUP BY s.StudentID, s.FirstName, s.LastName
        ORDER BY s.LastName, s.FirstName";
$stmt = $pdo->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to add a payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $enrollmentID = $_POST['enrollment_id'];
    $totalAmount = $_POST['total_amount'];
    $schoolYearStart = $_POST['school_year_start'];
    $schoolYearEnd = $_POST['school_year_end'];
    $semester = $_POST['semester'];

    // Retrieve the StudentID corresponding to the selected EnrollmentID
    $sql = "SELECT StudentID FROM Enrollment WHERE EnrollmentID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$enrollmentID]);
    $studentID = $stmt->fetchColumn();

    if (!$studentID) {
        die("Error: Invalid EnrollmentID selected.");
    }

    // Insert the new payment record into the Payment table
    $sql = "INSERT INTO Payment (EnrollmentID, StudentID, total_amount, school_year, semester, running_balance, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Calculate the running balance and set initial status
    $runningBalance = $totalAmount;
    $status = "Not Paid";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$enrollmentID, $studentID, $totalAmount, $schoolYearStart . '-' . $schoolYearEnd, $semester, $runningBalance, $status]);

    // Optionally insert into PaymentHistory to track changes
    $paymentID = $pdo->lastInsertId(); // Get the last inserted payment_id
    $changeType = 'Initial Payment';
    $sql = "INSERT INTO PaymentHistory (payment_id, StudentID, EnrollmentID, total_amount, amount_paid, running_balance, status, change_type, change_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paymentID, $studentID, $enrollmentID, $totalAmount, 0.00, $runningBalance, $status, $changeType]);

    // Redirect to manage payments page after successful insertion
    header("Location: manage_payments.php");
    exit;
}

include 'sidebar2.php'; // Include the cashier sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment for Student</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General reset and box-sizing */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

/* Admin content container */
.teacher-content {
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

.teacher-content h2 {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

/* Form styles */
form {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Space between form elements */
}

/* Label styles */
label {
    font-size: 14px;
    color: #555;
    margin-bottom: 5px;
}

/* Input field styles */
input, select {
    padding: 10px;
    font-size: 16px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
}

/* School year container to align both textboxes inline */
.school-year-container {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between the two textboxes */
}

/* School year textboxes (specific styling) */
#school_year_start, #school_year_end {
    width: 80px; /* Shorter width for the school year input */
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Button styles */
button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049;
}

/* Adjust form container to ensure proper inline layout */
form label,
form input,
form select {
    margin-bottom: 12px;
}

/* Responsive styling */
@media (max-width: 768px) {
    .teacher-content {
        width: 90%;
    }
}

    </style>
</head>
<body>
<div class="teacher-content">
    <h2>Add Payment for Student</h2>

    <!-- Form for adding a new payment -->
    <form method="POST" action="add_student_payment.php">
        <label for="enrollment_id">Student:</label>
        <select name="enrollment_id" id="enrollment_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo $student['EnrollmentID']; ?>">
                    <?php echo $student['FirstName'] . ' ' . $student['LastName']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="total_amount">Total Amount:</label>
        <input type="number" name="total_amount" id="total_amount" step="0.01" required>

        <label for="school_year_start">School Year:</label>
        <div class="school-year-container">
            <input type="text" name="school_year_start" id="school_year_start" maxlength="4" required oninput="updateEndYear()">
            <span>-</span>
            <input type="text" name="school_year_end" id="school_year_end" maxlength="4" readonly required>
        </div>

        <label for="semester">Semester:</label>
        <select name="semester" id="semester" required>
            <option value="">Select Semester</option>
            <option value="First Semester">First Semester</option>
            <option value="Second Semester">Second Semester</option>
        </select>

        <button type="submit">Add Payment</button>
    </form>
</div>

<script>
    // JavaScript function to automatically update the end year
    function updateEndYear() {
        var startYear = document.getElementById("school_year_start").value;
        if (startYear.length === 4 && !isNaN(startYear)) {
            var endYear = parseInt(startYear) + 1;
            document.getElementById("school_year_end").value = endYear;
        }
    }
</script>

</body>
</html>
