<?php
require_once("../connection.php");

// Set header response JSON
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;

        // Ambil semua supplier
        $sql = "SELECT id_supplier as id, nama_supplier as nama, nomor_hp as no_hp FROM supplier WHERE status = 'Y'";
        if (isset($query_data['id'])) {
            // Jika ada ID, ambil supplier berdasarkan ID
            $id = (int)$query_data['id'];
            $sql .= " AND id_supplier = $id";
        }
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
        $no_hp = $conn->real_escape_string($data['no_hp']);

        $sql = "INSERT INTO supplier (nama_supplier, nomor_hp) VALUES ('$nama', '$no_hp')";
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
        $no_hp = $conn->real_escape_string($data['no_hp']);

        $sql = "UPDATE supplier SET nama_supplier='$nama',nomor_hp='$data[no_hp]' WHERE id_supplier=$id";
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
        $sql = "UPDATE supplier SET status = 'N' WHERE id_supplier=$id";
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
