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
    body {
    font-family: Arial, sans-serif;
    background-color: #e9f2fa;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.teacher-content {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    flex-grow: 1;
    overflow-x: auto;
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

form {
    margin-bottom: 20px;
    text-align: center;
}

form select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
    display: block;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 16px;
    table-layout: auto;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
    word-wrap: break-word;
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

td a {
    display: inline-block;
    padding: 5px 10px;
    text-align: center;
    text-decoration: none;
    border-radius: 3px;
    margin-right: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-size: 14px;
    color: white; /* Default to white text */
}

td a.view {
    background-color: #007BFF;
}

td a.view:hover {
    background-color: #0056b3;
}

td a.edit {
    background-color: #ffc107;
    color: #333; /* Dark text for contrast */
}

td a.edit:hover {
    background-color: #e0a800;
    color: white; /* White text on hover */
}

td a.deactivate {
    background-color: #dc3545;
}

td a.deactivate:hover {
    background-color: #c82333;
}

td a.activate {
    background-color: #28a745;
}

td a.activate:hover {
    background-color: #218838;
}

td a.delete {
    background-color: #6c757d;
}

td a.delete:hover {
    background-color: #5a6268;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: 100%;
    position: relative;
    bottom: 0;
    left: 0;
    clear: both;
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
                <option value="">All School Level Subjects</option>
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
                    <a href="view_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="view">View</a>
                    <a href="edit_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="edit">Edit</a>
                    <?php if ($subject['IsActive']): ?>
                        <a href="deactivate_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="deactivate">Deactivate</a>
                    <?php else: ?>
                        <a href="activate_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="activate">Activate</a>
                    <?php endif; ?>
                    <a href="delete_subject.php?id=<?php echo $subject['SubjectID']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this subject? Deletion is not allowed if there are offerings associated with it.');">Delete</a>
                </td>

            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  
    <?php include '../includes/footer.php'; ?>

</body>

</html>
