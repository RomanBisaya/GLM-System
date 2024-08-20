<link rel="stylesheet" href="../css/sidebar2_style.css">
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    min-height: 100vh; /* Ensure body takes full height */
    flex-direction: column;
}

.teacher-sidebar {
    width: 250px;
    background-color: #343a40;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    padding-top: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.teacher-sidebar ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.teacher-sidebar ul li {
    margin: 15px 0;
}

.teacher-sidebar ul li a {
    display: block;
    color: #ffffff;
    text-decoration: none;
    font-size: 16px;
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.teacher-sidebar ul li a:hover {
    background-color: #495057;
    border-radius: 5px;
}

.teacher-sidebar ul li a.active {
    background-color: #007bff;
    border-radius: 5px;
}

.teacher-content {
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

.teacher-sidebar ul li a.active {
    background-color: #007bff; /* Highlight color */
    border-radius: 5px;
}

</style>

<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="teacher-sidebar">
    <ul>
        <li><a href="teacher_index.php" class="<?php echo $current_page == 'teacher_index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="manage_students.php" class="<?php echo $current_page == 'manage_students.php' ? 'active' : ''; ?>">Students</a></li>
        <li><a href="manage_subjects.php" class="<?php echo $current_page == 'manage_subjects.php' ? 'active' : ''; ?>">Subjects</a></li>
        <li><a href="manage_offerings.php" class="<?php echo $current_page == 'manage_offerings.php' ? 'active' : ''; ?>">Offerings</a></li>
        <li><a href="manage_enrollment.php" class="<?php echo $current_page == 'manage_enrollment.php' ? 'active' : ''; ?>">Enrollment</a></li>
        <li><a href="manage_grades.php" class="<?php echo $current_page == 'manage_grades.php' ? 'active' : ''; ?>">Grades</a></li>
        <li><a href="manage_payments.php" class="<?php echo $current_page == 'manage_payments.php' ? 'active' : ''; ?>">Payments</a></li>
        <li><a href="teacher_logout.php" class="<?php echo $current_page == 'teacher_logout.php' ? 'active' : ''; ?>">Logout</a></li>
    </ul>
</div>

