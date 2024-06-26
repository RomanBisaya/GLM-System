<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Fetch students from the database
try {
    // Now sorting just by FirstName
    $stmt = $pdo->query("SELECT StudentID, FirstName, MiddleName, LastName, Username, Status FROM Students ORDER BY FirstName");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php include 'sidebar2.php'; ?>

<div class="teacher-content">
    <h2>Manage Students</h2>
    <a href="add_student.php" class="btn">Add New Student</a>
    
    <!-- Table for Student Management -->
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['FirstName']); ?></td>
                <td><?php echo htmlspecialchars($student['MiddleName']); ?></td>
                <td><?php echo htmlspecialchars($student['LastName']); ?></td>
                <td><?php echo htmlspecialchars($student['Username']); ?></td>
                <td><?php echo htmlspecialchars($student['Status']); ?></td>
                <td>
                    <a href="view_student.php?id=<?php echo $student['StudentID']; ?>">View</a> |
                    <a href="edit_student.php?id=<?php echo $student['StudentID']; ?>">Edit</a> |
                    <?php echo ($student['Status'] === 'Active') ? 
                        '<a href="deactivate_student.php?id=' . $student['StudentID'] . '">Deactivate</a>' : 
                        '<a href="activate_student.php?id=' . $student['StudentID'] . '">Activate</a>'; ?> |
                    <a href="delete_student.php?id=<?php echo $student['StudentID']; ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
