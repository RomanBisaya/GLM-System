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

        body {
            font-family: Arial, sans-serif;
            background-color: #e9f2fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .admin-content {
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

        .btn {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 16px;
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
            background-color: #d9e9ff;
        }

        .actions a {
            color: #f7f9fa;
            text-decoration: none;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #0056b3;
            color: white;
        }

        /* Specific styles for different actions */
        .actions a.view {
            background-color: #17a2b8;
        }

        .actions a.view:hover {
            background-color: #117a8b;
            color: white;
        }

        .actions a.edit {
            background-color: #ffc107;
        }

        .actions a.edit:hover {
            background-color: #e0a800;
            color: white;
        }

        .actions a.deactivate {
            background-color: #dc3545;
        }

        .actions a.deactivate:hover {
            background-color: #c82333;
            color: white;
        }

        .actions a.delete {
            background-color: #6c757d;
        }

        .actions a.delete:hover {
            background-color: #5a6268;
            color: white;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #007BFF;
            text-decoration: none;
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin: 0 5px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .pagination a.active {
            background-color: #007BFF;
            color: white;
            border: 1px solid #007BFF;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="admin-content">
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
                <td class="actions">
                    <a href="view_student.php?id=<?php echo $student['StudentID']; ?>" class="view">View</a> |
                    <a href="edit_student.php?id=<?php echo $student['StudentID']; ?>" class="edit">Edit</a> |
                    <?php echo ($student['Status'] === 'Active') ? 
                        '<a href="deactivate_student.php?id=' . $student['StudentID'] . '" class="deactivate">Deactivate</a>' : 
                        '<a href="activate_student.php?id=' . $student['StudentID'] . '" class="deactivate">Activate</a>'; ?> 
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
