<?php
require_once("../connection.php");

// Set header response JSON
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;

        // Ambil semua product
        $sql = "SELECT id_product, p.nama_product, s.nama_supplier, u.nama_satuan, p.harga_beli_product, harga_jual_product,stok_product, p.status FROM product p JOIN supplier s on (p.id_supplier = s.id_supplier) JOIN satuan u on (p.id_satuan = u.id_satuan) WHERE p.status = 'Y'";
        if (isset($query_data['id_product'])) {
            // Jika ada ID, ambil product berdasarkan ID
            $id = (int)$query_data['id_product'];
            $sql .= " AND id_product = $id";
        }
        $result = $conn->query($sql);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode($products);
        break;

    case 'POST':
        // Tambah product baru
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nama wajib diisi']);
            exit;
        }

        $nama        = $conn->real_escape_string($data['nama']);
        $supplier_id = $conn->real_escape_string($data['supplier_id']);
        $satuan_id   = $conn->real_escape_string($data['satuan_id']);
        $harga_beli  = $conn->real_escape_string($data['harga_beli']);
        $harga_jual  = $conn->real_escape_string($data['harga_jual']);
        $stok        = $conn->real_escape_string($data['stok']);

        $sql = "INSERT INTO product (nama_product,id_supplier,id_satuan,harga_beli_product, harga_jual_product,stok_product) VALUES ('$nama', '$supplier_id', '$satuan_id', '$harga_beli', '$harga_jual', '$stok')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah product']);
        }
        break;

    case 'PUT':
        // Update product
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['nama'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            exit;
        }

        $id = (int)$data['id'];
        $nama = $conn->real_escape_string($data['nama']);

        $sql = "UPDATE product SET nama_product='$nama' WHERE id_product=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal update product']);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID tidak disertakan']);
            exit;
        }

        $id = (int)$_GET['id'];
        $sql = "UPDATE product SET status = 'N' WHERE id_product=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal hapus product']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
