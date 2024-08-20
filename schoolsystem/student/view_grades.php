<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Adjust the path as needed
    exit;
}

require_once '../includes/config.php'; // Ensure this path is correct

$studentID = $_SESSION['student_id'];
$schoolLevels = [];
$termsYears = [];
$selectedLevel = $_POST['schoolLevel'] ?? '';
$selectedTermYear = $_POST['termYear'] ?? '';
$grades = [];

// Fetch school levels
$levelQuery = "SELECT DISTINCT SchoolLevel FROM enrollment WHERE StudentID = :studentID ORDER BY SchoolLevel";
$stmt = $pdo->prepare($levelQuery);
$stmt->bindParam(':studentID', $studentID);
$stmt->execute();
$schoolLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch terms and years if a school level is selected
if (!empty($selectedLevel)) {
    $termsQuery = "SELECT DISTINCT CONCAT(o.Term, ' || ', o.SchoolYear) AS TermYear
                    FROM offerings o
                    INNER JOIN enrollment e ON o.OfferingID = e.OfferingID
                    WHERE e.StudentID = :studentID AND e.SchoolLevel = :schoolLevel";
    $stmt = $pdo->prepare($termsQuery);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->bindParam(':schoolLevel', $selectedLevel);
    $stmt->execute();
    $termsYears = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch grades if a term and year is selected
if (!empty($selectedTermYear)) {
    list($term, $schoolYear) = explode(' || ', $selectedTermYear);
    $gradesQuery = "SELECT s.SubjectName, g.Grade
                    FROM grades g
                    INNER JOIN enrollment e ON g.EnrollmentID = e.EnrollmentID
                    INNER JOIN offerings o ON e.OfferingID = o.OfferingID
                    INNER JOIN subjects s ON g.SubjectID = s.SubjectID
                    WHERE e.StudentID = :studentID AND e.SchoolLevel = :schoolLevel AND o.Term = :term AND o.SchoolYear = :schoolYear";
    $stmt = $pdo->prepare($gradesQuery);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->bindParam(':schoolLevel', $selectedLevel);
    $stmt->bindParam(':term', $term);
    $stmt->bindParam(':schoolYear', $schoolYear);
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


include 'sidebar.php'; // Include the student sidebar
?>

<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

.main-content {
    margin-left: 250px; /* Adjust based on sidebar width */
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    max-width: 1200px;
    margin: 50px auto;
}

h1 {
    color: #007BFF;
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
    text-align: center;
}

select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
    width: 200px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
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

tbody tr td:last-child {
    text-align: center;
}

/* Grade-specific colors */
tbody tr td:last-child:contains('Ok') {
    background-color: #f2f2f2; /* Light Grey */
    color: #333;
}

tbody tr td:last-child:contains('Good') {
    background-color: #ffc107; /* Yellow */
    color: #333;
}

tbody tr td:last-child:contains('Very Good') {
    background-color: #28a745; /* Green */
    color: white;
}

tbody tr td:last-child:contains('Excellent') {
    background-color: #FFD700; /* Gold */
    color: black;
}

p {
    text-align: center;
    font-size: 18px;
    color: #666;
}

footer {
    background-color: #343a40;
    color: white;
    padding: 10px 20px;
    text-align: center;
    width: calc(100% - 250px); /* Adjusts based on sidebar width */
    margin-left: 250px; /* Ensures it aligns with the content */
    position: relative;
    bottom: 0;
    left: 0;
}

</style>

<div class="main-content">
    <h1>View Grades</h1>
    <form action="view_grades.php" method="post">
        <div class="form-group">
            <label for="schoolLevel">School Level:</label>
            <select name="schoolLevel" id="schoolLevel" onchange="this.form.submit()">
                <option value="">Select School Level</option>
                <?php foreach ($schoolLevels as $level): ?>
                    <option value="<?= htmlspecialchars($level); ?>" <?= $selectedLevel == $level ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($level); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="termYear">Term and School Year:</label>
            <select name="termYear" id="termYear" onchange="this.form.submit()">
                <option value="">Select Term and School Year</option>
                <?php foreach ($termsYears as $ty): ?>
                    <option value="<?= htmlspecialchars($ty); ?>" <?= $selectedTermYear == $ty ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($ty); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <?php if (!empty($grades)): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                    <tr style="background-color: 
                    <?php 
                        switch($grade['Grade']) {
                            case 'Excellent':
                                echo '#FFD700'; /* Blue for Excellent */
                                break;
                            case 'Very Good':
                                echo '#28a745'; /* Green for Very Good */
                                break;
                            case 'Good':
                                echo '#ffc107'; /* Yellow for Good */
                                break;
                            case 'Ok':
                            default:
                                echo '#f2f2f2'; /* Light Grey for Ok and default */
                                break;
                        }
                    ?>; color: 
                    <?php echo ($grade['Grade'] == 'Excellent' || $grade['Grade'] == 'Very Good') ? 'white' : '#333'; ?>">
                        <td><?= htmlspecialchars($grade['SubjectName']); ?></td>
                        <td><?= htmlspecialchars($grade['Grade']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No grades available for the selected filters.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<?php include '../includes/footer.php'; ?>
