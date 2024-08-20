<?php
session_start();

// Access control checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php"); // Make sure this path is correct
    exit;
}

// Include your database configuration script
require_once '../includes/config.php'; // Correct path to your database configuration file

// Initialize the variable to store teachers
$teachers = [];

// Fetch teachers from the database
try {
    // Assuming $pdo is your PDO instance variable in config.php
    $stmt = $pdo->query("SELECT TeacherID, FirstName, MiddleName, LastName, Username FROM Teachers");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database connection error
    die("ERROR: Could not connect. " . $e->getMessage());
}

require_once 'sidebar.php';
?>

<link rel="stylesheet" href="../css/style_admin.css">
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    min-height: 100vh; /* Ensure body takes full height */
    flex-direction: column;
    background-color: #f8f9fa;
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

h2 {
    text-align: center;
    color: #007bff;
    font-size: 24px;
    margin-bottom: 20px;
}

form {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 10px;
}

form input[type="text"],
form input[type="submit"] {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

form input[type="submit"] {
    background-color: #007bff; /* Blue for submit buttons */
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
    table-layout: fixed; /* Ensures the table stays within the container */
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
    word-wrap: break-word; /* Prevents content from overflowing */
}

th {
    background-color: #007bff;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f8ff;
}

tr:hover {
    background-color: #e1ecff;
}

td a {
    color: #007bff;
    text-decoration: none;
    margin-right: 10px;
    transition: color 0.3s ease;
}

td a:hover {
    color: #0056b3;
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
    margin-top: auto; /* Ensures the footer stays at the bottom */
}

form {
    display: flex;
    flex-wrap: wrap; /* Allow the form elements to wrap to the next line if necessary */
    justify-content: space-between; /* Space elements evenly */
    margin-bottom: 20px;
    gap: 10px;
}

form input[type="text"] {
    flex: 1 1 calc(50% - 10px); /* Each text input will take up 50% of the width, minus the gap */
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box; /* Ensures padding and border are included in the element's total width and height */
    min-width: 100px; /* Ensures input fields don’t get too narrow */
}

form input[type="submit"] {
    flex: 1 1 100%; /* Submit button takes full width */
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #007bff;
    background-color: #007bff; /* Blue for submit buttons */
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
    box-sizing: border-box; /* Ensures padding and border are included in the element's total width and height */
}

form input[type="submit"]:hover {
    background-color: #0056b3;
}

</style>
<div class="admin-content">
    <h2>Manage Teachers</h2>
    
    <!-- Form for Adding a New Teacher -->
    <form method="post" action="add_teacher.php">
        <input type="text" name="FirstName" placeholder="First Name" required>
        <input type="text" name="MiddleName" placeholder="Middle Name">
        <input type="text" name="LastName" placeholder="Last Name" required>
        <input type="text" name="Username" placeholder="Username" required>
        <input type="text" name="Password" placeholder="Password" required>
        <input type="submit" value="Add Teacher">
    </form>

    <!-- Table of Teachers -->
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- PHP loop to dynamically create rows from the teachers' database -->
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['MiddleName']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['Username']); ?></td>
                    <td>
                        <!-- Edit link sends the user to a page to edit the teacher's info -->
                        <a href="edit_teacher.php?TeacherID=<?php echo $teacher['TeacherID']; ?>">Edit</a>
                        <!-- Delete link sends the teacher's ID to a backend script to delete the record -->
                        <a href="delete_teacher.php?TeacherID=<?php echo $teacher['TeacherID']; ?>" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
