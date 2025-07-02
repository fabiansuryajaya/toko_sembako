<?php
require_once("../connection.php");

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;
        $action = isset($query_data['action']) ? $query_data['action'] : '';

        // data pembelian
        $sql = "SELECT p.id_pembelian, p.jumlah_pembelian, p.status, p.created_at, u.username as nama_user
                FROM pembelian p
                JOIN user u ON p.created_by = u.id_user
                WHERE p.status = 'Y'";

        if ($action === 'detail') {
            // Ambil detail pembelian berdasarkan ID pembelian
            if (isset($query_data['id_pembelian'])) {
                $id_pembelian = (int)$query_data['id_pembelian'];
                $sql = "SELECT dp.id_detail_pembelian, dp.id_produk, p.nama_product, dp.jumlah_pembelian, dp.harga_pembelian
                        FROM detail_pembelian dp
                        JOIN product p ON dp.id_produk = p.id_product
                        WHERE dp.id_pembelian = $id_pembelian";
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID pembelian tidak diberikan']);
                exit;
            }
        }
        $result = $conn->query($sql);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal mengambil data']);
            exit;
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;

    case 'POST':
        // Tambah product baru
        $data = json_decode(file_get_contents('php://input'), true);

        $stock = $data['restock'];

        if (!is_array($stock) || empty($stock)) {
            http_response_code(400);
            echo json_encode(['error' => 'Data restock tidak valid']);
            exit;
        }

        // insert pembelian
        $id_user = 1; // Ganti dengan ID user yang sesuai
        $jumlah_pembelian = array_reduce($stock, function($carry, $item) {
            return $carry + (int)$item['quantity'];
        }, 0);
        $sql = "INSERT INTO pembelian (id_user, jumlah_pembelian, status, created_by, created_at) VALUES ($id_user, $jumlah_pembelian, 'Y', $id_user, NOW())";
        if ($conn->query($sql) === FALSE) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah pembelian']);
            exit;
        }
        $id_pembelian = $conn->insert_id; // Ambil ID pembelian yang baru saja dimasukkan

        for ($i=0; $i < count($stock); $i++) { 
            $item = $stock[$i];
            $id_produk = (int)$item['product_id'];
            $jumlah = (int)$item['quantity'];
            $harga = (float)$item['harga_beli'];

            $sql = "INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah_pembelian, harga_pembelian) VALUES ($id_pembelian, $id_produk, $jumlah, $harga)";
            if ($conn->query($sql) === FALSE) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal menambah detail pembelian']);
                exit;
            }

            // Update stok product
            $sql = "UPDATE product SET stok_product = stok_product + $jumlah, harga_beli_product = $harga WHERE id_product = $id_produk";
            if ($conn->query($sql) === FALSE) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal update stok product']);
                exit;
            }
        }

        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
