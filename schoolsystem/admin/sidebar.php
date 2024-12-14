<link rel="stylesheet" href="../css/admin_style.css">

<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    min-height: 100vh; /* Ensure body takes full height */
    flex-direction: column;
}

.admin-sidebar {
    width: 250px;
    background-color: #343a40;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    padding-top: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.admin-sidebar ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.admin-sidebar ul li {
    margin: 15px 0;
}

.admin-sidebar ul li a {
    display: block;
    color: #ffffff;
    text-decoration: none;
    font-size: 16px;
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.admin-sidebar ul li a:hover {
    background-color: #495057;
    border-radius: 5px;
}

.admin-sidebar ul li a.active {
    background-color: #007bff; /* Highlight color */
    border-radius: 5px;
}

.admin-content {
    margin-left: 250px; /* Ensures content is not hidden behind the sidebar */
    padding: 20px;
    flex-grow: 1; /* Makes content area grow to fill the remaining space */
    background-color: #f8f9fa;
    min-height: calc(100vh - 40px); /* Adjusts content area to leave space for the footer */
    width: calc(100% - 250px); /* Adjusts content width based on sidebar width */
}

footer {
    margin-left: 250px;
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: calc(100% - 250px); /* Adjusts footer width based on sidebar width */
}

</style>
<div class="admin-sidebar">
    <ul>
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="manage_teachers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_teachers.php' ? 'active' : ''; ?>">Manage Teachers</a></li>
        <li><a href="manage_students.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_students.php' ? 'active' : ''; ?>">Students</a></li>
        <li><a href="manage_subjects.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_subjects.php' ? 'active' : ''; ?>">Subjects</a></li>
        <li><a href="manage_offerings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_offerings.php' ? 'active' : ''; ?>">Offerings</a></li>
        <li><a href="manage_enrollment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_enrollment.php' ? 'active' : ''; ?>">Enrollment</a></li>
        <li><a href="manage_grades.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_grades.php' ? 'active' : ''; ?>">Grades</a></li>
        <li><a href="manage_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_payments.php' ? 'active' : ''; ?>">Payments</a></li>
        <li><a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
        <li><a href="logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Log Out</a></li>
    </ul>
</div>
