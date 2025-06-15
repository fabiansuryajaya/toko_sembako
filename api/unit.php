<?php
require_once("../connection.php");

// Set header response JSON
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;

        // Ambil semua satuan
        $sql = "SELECT id_satuan as id, nama_satuan as nama FROM satuan WHERE status = 'Y'";
        if (isset($query_data['id'])) {
            // Jika ada ID, ambil satuan berdasarkan ID
            $id = (int)$query_data['id'];
            $sql .= " AND id_satuan = $id";
        }
        $result = $conn->query($sql);

        $satuans = [];
        while ($row = $result->fetch_assoc()) {
            $satuans[] = $row;
        }

        echo json_encode($satuans);
        break;

    case 'POST':
        // Tambah satuan baru
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nama wajib diisi']);
            exit;
        }

        $nama = $conn->real_escape_string($data['nama']);

        $sql = "INSERT INTO satuan (nama_satuan) VALUES ('$nama')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah satuan']);
        }
        break;

    case 'PUT':
        // Update satuan
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            exit;
        }

        $id = (int)$data['id'];
        $nama = $conn->real_escape_string($data['nama']);

        $sql = "UPDATE satuan SET nama_satuan='$nama' WHERE id_satuan=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal update satuan']);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID tidak disertakan']);
            exit;
        }

        $id = (int)$_GET['id'];
        $sql = "UPDATE satuan SET status = 'N' WHERE id_satuan=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal hapus satuan']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
