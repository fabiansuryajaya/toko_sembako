<?php
require_once("../connection.php");

// Set header response JSON
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;

        // Ambil semua member
        $sql = "SELECT id_member as id, nama as nama, nomor_hp as nomor_hp FROM member";
        if (isset($query_data['id'])) {
            // Jika ada ID, ambil member berdasarkan ID
            $id = (int)$query_data['id'];
            $sql .= " WHERE id_member = $id";
        }
        $result = $conn->query($sql);

        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }

        echo json_encode($members);
        break;

    case 'POST':
        // Tambah member baru
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nama wajib diisi']);
            exit;
        }

        $nama = $conn->real_escape_string($data['nama']);
        $nomor_hp = $conn->real_escape_string($data['nomor_hp']);
        if (!isset($nomor_hp)) {
            http_response_code(400);
            echo json_encode(['error' => 'Nomor HP wajib diisi']);
            exit;
        }
        $sql = "INSERT INTO member (nama, nomor_hp) VALUES ('$nama', '$nomor_hp')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah member']);
        }
        break;

    case 'PUT':
        // Update member
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            exit;
        }

        $id = (int)$data['id'];
        $nama = $conn->real_escape_string($data['nama']);
        $nomor_hp = $conn->real_escape_string($data['nomor_hp']);

        $sql = "UPDATE member SET nama='$nama', nomor_hp='$nomor_hp' WHERE id_member=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal update member']);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID tidak disertakan']);
            exit;
        }

        $id = (int)$_GET['id'];
        $sql = "UPDATE member SET status = 'N' WHERE id_member=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal hapus member']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
