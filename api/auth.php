<?php
require_once("../connection.php");

// Default response
$return = array('status' => '0', 'message' => '', 'data' => array());

// Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query user dengan prepared statement
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $return['status'] = '401';
        $return['message'] = 'Username atau Password Salah';
        echo json_encode($return);
        exit;
    }

    $user = $result->fetch_assoc();
    // Cek password hash
    if ($user['password'] != $password) {
        $return['status'] = '401';
        $return['message'] = 'Username atau Password Salah';
        echo json_encode($return);
        exit;
    }

    // Sukses login
    $return['status']  = '200';
    $return['message'] = 'Login Berhasil';
    $return['data']    = array(
        "role" => $user['role']
    );

    echo json_encode($return);
    exit;
}

// Jika request tidak valid
$return['status'] = '400';
$return['message'] = 'Permintaan tidak valid';
echo json_encode($return);
exit;