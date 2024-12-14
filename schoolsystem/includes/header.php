<header>
    <link rel="stylesheet" href="/schoolsystem/css/style.css">
    <h1>Welcome to God's Little Miracles Learning Center</h1>
    <nav>
        <ul>
            <li><a href="/schoolsystem/index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="/schoolsystem/student/login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php' && strpos($_SERVER['PHP_SELF'], '/student/') !== false) ? 'active' : ''; ?>">Students</a></li>
            <li><a href="/schoolsystem/teacher/login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php' && strpos($_SERVER['PHP_SELF'], '/teacher/') !== false) ? 'active' : ''; ?>">Teachers</a></li>
            <li><a href="/schoolsystem/admin/login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php' && strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'active' : ''; ?>">Admin Login</a></li>
        </ul>
    </nav>
</header>

<style>
    a.active {
    font-weight: bold;
    color: #FDA831; /* Or any color of your choice */
}
</style>
