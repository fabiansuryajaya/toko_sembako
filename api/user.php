<?php
header('Content-Type: application/json');
require_once("../connection.php");

function response($data, $status = 0, $message = 'OK') {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// GET all or by id
if ($method === 'GET') {
    if ($id) {
        $stmt = $conn->prepare("SELECT id_user as id, username, '' as password FROM `user` WHERE id_user=? AND role != 'admin'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        response($result);
    } else {
        $result = $conn->query("SELECT id_user as id, username, '' as password FROM `user` WHERE role != 'admin' ORDER BY id_user DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        response($data);
    }
}

// CREATE
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    if (!$username || !$password) response(null, 1, 'Username dan password wajib diisi');

    // Cek username unik
    $stmt = $conn->prepare("SELECT id_user FROM `user` WHERE username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        response(null, 1, 'Username sudah digunakan');
    }

    $stmt = $conn->prepare("INSERT INTO `user` (username, password, role) VALUES (?, ?, 'pegawai')");
    $stmt->bind_param('ss', $username, $password);
    if ($stmt->execute()) {
        response(['id' => $conn->insert_id], 0, 'User berhasil ditambah');
    } else {
        response(null, 1, 'Gagal menambah user');
    }
}

// UPDATE
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    if (!$id || !$username) response(null, 1, 'ID dan username wajib diisi');

    // Cek username unik (kecuali user ini sendiri)
    $stmt = $conn->prepare("SELECT id_user FROM `user` WHERE username=? AND id_user<>?");
    $stmt->bind_param('si', $username, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        response(null, 1, 'Username sudah digunakan');
    }

    if ($password) {
        $stmt = $conn->prepare("UPDATE `user` SET username=?, password=? WHERE id_user=?");
        $stmt->bind_param('ssi', $username, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE `user` SET username=? WHERE id_user=?");
        $stmt->bind_param('si', $username, $id);
    }
    if ($stmt->execute()) {
        response(null, 0, 'User berhasil diupdate');
    } else {
        response(null, 1, 'Gagal update user');
    }
}

// DELETE
if ($method === 'DELETE') {
    if (!$id) response(null, 1, 'ID user tidak ditemukan');
    $stmt = $conn->prepare("DELETE FROM `user` WHERE id_user=?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        response(null, 0, 'User berhasil dihapus');
    } else {
        response(null, 1, 'Gagal menghapus user');
    }
}

response(null, 1, 'Metode tidak didukung');