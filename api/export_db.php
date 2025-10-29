<?php
// Export database to SQL file
require_once("../connection.php");
$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// select product
$product_id = [];
$query = "SELECT * FROM product where nama_product like '%beras%'";
$product_result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_row($product_result)) {
    $product_id[$row[0]] = 1;
}

// delete database
$sqlScript = "";
foreach ($tables as $table) {
    $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE $table"));
    $create_table = $row2[1];
    $create_table = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $create_table);

    if ($table == "product"){
        $sqlScript .= "\n" . $create_table . ";\n";
        while ($row = mysqli_fetch_row($result)) {
            $sqlScript .= "INSERT INTO $table VALUES ('" . implode("', '", array_map('addslashes', $row)) . "') ON DUPLICATE KEY UPDATE id_product = LAST_INSERT_ID(id_product);\n";
        }
        continue;
    }
    $sqlScript .= "DROP TABLE IF EXISTS $table;\n";
    $sqlScript .= "\n" . $create_table . ";\n";

    $result = mysqli_query($conn, "SELECT * FROM $table");
    while ($row = mysqli_fetch_row($result)) {
        if ($table == "detail_penjualan") {
            if (!isset($product_id[$row[2]])) continue;
        }
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