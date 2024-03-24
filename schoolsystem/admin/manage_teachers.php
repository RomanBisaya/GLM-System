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

<?php include 'includes/footer.php'; ?>

