<?php
require_once("../connection.php");

// Set header response JSON
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil semua supplier
        $sql = "SELECT id_supplier as id, nama_supplier as nama FROM supplier";
        $result = $conn->query($sql);

        $suppliers = [];
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }

        echo json_encode($suppliers);
        break;

    case 'POST':
        // Tambah supplier baru
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nama wajib diisi']);
            exit;
        }

        $nama = $conn->real_escape_string($data['nama']);

        $sql = "INSERT INTO supplier (nama_supplier) VALUES ('$nama')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah supplier']);
        }
        break;

    case 'PUT':
        // Update supplier
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            exit;
        }

        $id = (int)$data['id'];
        $nama = $conn->real_escape_string($data['nama']);

        $sql = "UPDATE suppliers SET nama='$nama' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal update supplier']);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID tidak disertakan']);
            exit;
        }

        $id = (int)$_GET['id'];
        $sql = "DELETE FROM suppliers WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal hapus supplier']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
