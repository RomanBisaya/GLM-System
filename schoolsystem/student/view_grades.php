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

<link rel="stylesheet" href="../css/student_style.css">

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
                    <tr>
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
