<?php
    $host = "192.168.18.94";
    $user = "root";
    $pass = "";
    $db   = "toko_sembako";

    $conn = mysqli_connect($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }   