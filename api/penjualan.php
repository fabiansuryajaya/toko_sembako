<?php
require_once("../connection.php");

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;
        $action = isset($query_data['action']) ? $query_data['action'] : '';
        $id_penjualan = isset($query_data['id_penjualan']) ? (int)$query_data['id_penjualan'] : 0;

        // data penjualan
        $sql = "SELECT p.id_penjualan, p.jumlah_penjualan, p.total_pembayaran, p.status, p.created_at, u.username as nama_user
                FROM penjualan p
                JOIN user u ON p.created_by = u.id_user
                WHERE p.status = 'Y'";

        if ($id_penjualan > 0) {
            $sql .= " AND p.id_penjualan = $id_penjualan";
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

        if ($action === 'detail') {
            $data = isset($data[0]) ? $data[0] : null;
            // Ambil detail penjualan berdasarkan ID penjualan
            if (isset($query_data['id_penjualan'])) {
                $id_penjualan = (int)$query_data['id_penjualan'];
                $sql = "SELECT dp.id_detail_penjualan, dp.id_produk, p.nama_product, s.nama_satuan, dp.jumlah_penjualan, dp.harga_penjualan
                        FROM detail_penjualan dp
                        JOIN product p ON dp.id_produk = p.id_product
                        JOIN satuan s ON p.id_satuan = s.id_satuan
                        WHERE dp.id_penjualan = $id_penjualan";
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID penjualan tidak diberikan']);
                exit;
            }

            $result = $conn->query($sql);
            if (!$result) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal mengambil data']);
                exit;
            }

            $data['detail'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['detail'][] = $row;
            }

        }
        echo json_encode($data);
        break;

    case 'POST':
        // Tambah product baru
        $data = json_decode(file_get_contents('php://input'), true);

        $stock = $data['penjualan'];

        if (!is_array($stock) || empty($stock)) {
            http_response_code(400);
            echo json_encode(['error' => 'Data penjualan tidak valid']);
            exit;
        }

        // insert penjualan
        $id_user = 1; // Ganti dengan ID user yang sesuai
        $jumlah_penjualan = array_reduce($stock, function($carry, $item) {
            return $carry + ((int)$item['harga_beli'] * (int)$item['quantity']);
        }, 0);
        $total_bayar  = isset($data['total_bayar'])  ? (float)$data['total_bayar'] : 0;

        $sql = "INSERT INTO penjualan (id_user, jumlah_penjualan, total_pembayaran, status, created_by, created_at) VALUES ($id_user, $jumlah_penjualan, $total_bayar, 'Y', $id_user, NOW())";
        if ($conn->query($sql) === FALSE) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambah penjualan']);
            exit;
        }
        $id_penjualan = $conn->insert_id; // Ambil ID penjualan yang baru saja dimasukkan

        for ($i=0; $i < count($stock); $i++) { 
            $item = $stock[$i];
            $id_produk = (int)$item['product_id'];
            $jumlah = (int)$item['quantity'];
            $harga = (float)$item['harga_beli'];

            $sql = "INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah_penjualan, harga_penjualan) VALUES ($id_penjualan, $id_produk, $jumlah, $harga)";
            if ($conn->query($sql) === FALSE) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal menambah detail penjualan']);
                exit;
            }

            // Update stok product
            $sql = "UPDATE product SET stok_product = stok_product - $jumlah, harga_beli_product = $harga WHERE id_product = $id_produk";
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
