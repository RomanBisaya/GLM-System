<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

// Initialize variables
$subjects = [];
$schoolLevelFilter = '';

// Fetch school levels for filter dropdown
$schoolLevels = [
    "Junior Nursery", "Senior Nursery", "YS 1: Kindergarten", "YS 2: Grade 1", "YS 3: Grade 2",
    "YS 4: Grade 3", "YS 5: Grade 4", "YS 6: Grade 5", "YS 7: Grade 6"
];

// Handle school level filter
if (isset($_POST['schoolLevelFilter']) && in_array($_POST['schoolLevelFilter'], $schoolLevels)) {
    $schoolLevelFilter = $_POST['schoolLevelFilter'];
}

// Prepare SQL based on filter
$sql = "SELECT SubjectID, SubjectName, SubjectSchoolLevel, IsActive FROM subjects"; // Correct column name used here
if (!empty($schoolLevelFilter)) {
    $sql .= " WHERE SubjectSchoolLevel = :schoolLevelFilter"; // Correct column name used here
}
$sql .= " ORDER BY SubjectSchoolLevel, SubjectName"; // Adding ORDER BY clause for sorting

if ($stmt = $pdo->prepare($sql)) {
    if (!empty($schoolLevelFilter)) {
        $stmt->bindParam(":schoolLevelFilter", $schoolLevelFilter, PDO::PARAM_STR);
    }
    
    if ($stmt->execute()) {
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

unset($stmt);
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="../css/teacher_style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse; /* Collapse borders */
        }
        th, td {
            border: 1px solid #ddd; /* Add borders to cells */
            padding: 8px; /* Add padding */
            text-align: left; /* Align text to the left */
        }
        th {
            background-color: #f2f2f2; /* Light grey background for headers */
        }
    </style>
</head>
<body>
    <?php include 'sidebar2.php'; ?>
    <div class="teacher-content">
        <h2>Manage Subjects</h2>
        <a href="add_subject.php" class="btn">Add Subject</a>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <select name="schoolLevelFilter" onchange="this.form.submit()">
                <option value="">Filter by School Level</option>
                <?php foreach ($schoolLevels as $level): ?>
                    <option value="<?php echo $level; ?>" <?php echo ($schoolLevelFilter == $level) ? 'selected' : ''; ?>><?php echo $level; ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>School Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?php echo htmlspecialchars($subject['SubjectName']); ?></td>
                <td><?php echo htmlspecialchars($subject['SubjectSchoolLevel']); ?></td>
                <td>
                    <a href="view_subject.php?id=<?php echo $subject['SubjectID']; ?>">View</a> |
                    <a href="edit_subject.php?id=<?php echo $subject['SubjectID']; ?>">Edit</a> |
                    <?php if ($subject['IsActive']): ?>
                        <a href="deactivate_subject.php?id=<?php echo $subject['SubjectID']; ?>">Deactivate</a> |
                    <?php else: ?>
                        <a href="activate_subject.php?id=<?php echo $subject['SubjectID']; ?>">Activate</a> |
                    <?php endif; ?>
                    <a href="delete_subject.php?id=<?php echo $subject['SubjectID']; ?>" onclick="return confirm('Are you sure you want to delete this subject? Deletion is not allowed if there are offerings associated with it.');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
