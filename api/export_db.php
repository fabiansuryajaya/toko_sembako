<?php
// Export database to SQL file
require_once("../connection.php");
$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// delete database
$sqlScript = "DROP DATABASE IF EXISTS " . $db . "; CREATE DATABASE " . $db . "; USE " . $db . ";";
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SELECT * FROM $table");
    $numFields = mysqli_num_fields($result);

    $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE $table"));
    $sqlScript .= "\n" . $row2[1] . ";\n";

    while ($row = mysqli_fetch_row($result)) {
        $sqlScript .= "INSERT INTO $table VALUES ('" . implode("', '", array_map('addslashes', $row)) . "');\n";
    }
}
if (!empty($sqlScript)) {
    $backupFile = 'db-backup-' . date('Y-m-d-H-i-s') . '.sql';
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename=' . $backupFile);
    echo $sqlScript;
} else {
    echo "No data found to export.";
}