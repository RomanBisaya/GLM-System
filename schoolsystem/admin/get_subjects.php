<?php
require_once '../includes/config.php'; // Ensure correct path

if (isset($_GET['termYear']) && !empty($_GET['termYear'])) {
    list($term, $year) = explode(' || ', $_GET['termYear']);

    $sql = "SELECT s.SubjectID, s.SubjectName
            FROM subjects s
            JOIN offerings o ON s.SubjectID = o.SubjectID
            WHERE o.Term = ? AND o.SchoolYear = ?
            ORDER BY s.SubjectName";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $year]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($subjects) {
            foreach ($subjects as $subject) {
                echo "<div><input type='checkbox' name='subjects[]' value='" . htmlspecialchars($subject['SubjectID']) . "' checked> " . htmlspecialchars($subject['SubjectName']) . "</div>";
            }
        } else {
            echo "No subjects found for the selected term and year.";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "Error fetching subjects.";
    }
} else {
    echo "Term and year not specified.";
}
?>
