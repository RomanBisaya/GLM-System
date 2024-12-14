<?php
// Include the database configuration file
require_once '../includes/config.php';

// Check if the school level is set in the GET request
$schoolLevel = isset($_GET['schoolLevel']) ? $_GET['schoolLevel'] : '';

// Initialize an array to store the options
$options = [];

if ($schoolLevel) {
    // Prepare the SQL statement to fetch the offerings
    $stmt = $pdo->prepare("
        SELECT o.OfferingID, s.SubjectName, o.Term, o.SchoolYear 
        FROM offerings AS o
        JOIN subjects AS s ON o.SubjectID = s.SubjectID
        WHERE s.SubjectSchoolLevel = :schoolLevel
        ORDER BY s.SubjectName, o.Term, o.SchoolYear
    ");

    // Bind the school level parameter to the prepared statement
    $stmt->bindParam(':schoolLevel', $schoolLevel);

    // Execute the statement and fetch the results
    $stmt->execute();
    $offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop over the results and create the option tags
    foreach ($offerings as $offering) {
        $optionValue = htmlspecialchars($offering['OfferingID']);
        $optionText = htmlspecialchars($offering['SubjectName'] . " - " . $offering['Term'] . " - " . $offering['SchoolYear']);
        $options[] = "<option value='{$optionValue}'>{$optionText}</option>";
    }
}

// Join the options array into a single string of HTML
$optionsHtml = join('', $options);

// Close the database connection
unset($pdo);

// Return the options HTML
echo $optionsHtml;
?>
