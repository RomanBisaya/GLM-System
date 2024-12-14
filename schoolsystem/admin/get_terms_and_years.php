<?php
require_once '../includes/config.php'; // Ensure this includes the correct path to your database configuration file

if (isset($_GET['schoolLevel']) && !empty($_GET['schoolLevel'])) {
    $schoolLevel = $_GET['schoolLevel'];

    // Prepare SQL to fetch terms and years based on the school level
    $sql = "SELECT DISTINCT CONCAT(o.Term, ' || ', o.SchoolYear) AS TermYear
            FROM offerings o
            JOIN subjects s ON s.SubjectID = o.SubjectID
            WHERE s.SubjectSchoolLevel = ?
            ORDER BY o.SchoolYear DESC, o.Term";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$schoolLevel]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            foreach ($results as $row) {
                echo '<option value="' . htmlspecialchars($row['TermYear']) . '">' . htmlspecialchars($row['TermYear']) . '</option>';
            }
        } else {
            echo '<option value="">No terms available</option>';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo '<option value="">Error fetching terms</option>';
    }
}
?>
