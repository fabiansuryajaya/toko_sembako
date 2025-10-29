<?php
// Import database from file
require_once("../connection.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['importFile']) && $_FILES['importFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['importFile']['tmp_name'];
        $fileName = $_FILES['importFile']['name'];
        $fileSize = $_FILES['importFile']['size'];
        $fileType = $_FILES['importFile']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'sql') {
            $sqlContent = file_get_contents($fileTmpPath);
            if (mysqli_multi_query($conn, $sqlContent)) {
                do {
                    // flush multi_queries
                } while (mysqli_more_results($conn) && mysqli_next_result($conn));
                echo "Database imported successfully.";
            } else {
                http_response_code(500);
                echo "Error importing database: " . mysqli_error($conn);
            }
        } else {
            http_response_code(400);
            echo "Invalid file format. Please upload a .sql file.";
        }
    } else {
        http_response_code(400);
        echo "No file uploaded or upload error.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
