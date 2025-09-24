<?php
require_once("../connection.php");

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;
        $action = isset($query_data['action']) ? $query_data['action'] : '';
        $id_hutang = isset($query_data['id_hutang']) ? (int)$query_data['id_hutang'] : 0;
        $from_date = isset($query_data['from_date']) ? $conn->real_escape_string($query_data['from_date']) : '';
        $to_date = isset($query_data['to_date']) ? $conn->real_escape_string($query_data['to_date']) : '';  
       
        // data penjualan
        $sql = "SELECT p.id_penjualan as id_hutang, p.jumlah_penjualan as jumlah_hutang, p.total_pembayaran, p.total_ongkir, p.status, p.created_at, m.nama as nama_member, u.username as nama_user
                FROM penjualan p
                JOIN user u ON p.created_by = u.id_user
                JOIN member m ON p.id_member = m.id_member
                WHERE p.id_member IS NOT NULL";

        if ($id_hutang > 0) {
            $sql .= " AND p.id_penjualan = $id_hutang";
        }
         if (!empty($from_date)) {
            $sql .= " AND p.created_at >= '$from_date'";
        }

        if (!empty($to_date)) {
            $sql .= " AND p.created_at <= '$to_date'";
        }
        $sql .= " ORDER BY p.id_penjualan DESC";

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
            if (isset($query_data['id_hutang'])) {
                $sql = "SELECT dp.id_detail_penjualan as id_detail_hutang, s.nama_satuan, dp.id_produk,pe.status, p.nama_product, dp.jumlah_penjualan as jumlah_hutang, dp.harga_penjualan as harga_hutang
                        FROM detail_penjualan dp
                        JOIN product p ON dp.id_produk = p.id_product
                        JOIN satuan s ON p.id_satuan = s.id_satuan
                        JOIN penjualan pe ON dp.id_penjualan = pe.id_penjualan
                        WHERE dp.id_penjualan = $id_hutang
                        ORDER BY p.nama_product asc";
                        
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
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID penjualan tidak diberikan']);
                exit;
            }
        }

        echo json_encode($data);
        break;

    case 'POST':
        // Tambah product baru
        $data = json_decode(file_get_contents('php://input'), true);

        $stock = $data['hutang'];

        if (!is_array($stock) || empty($stock)) {
            http_response_code(400);
            echo json_encode(['error' => 'Data penjualan tidak valid']);
            exit;
        }

        // insert penjualan
        $id_user = 1; // Ganti dengan ID user yang sesuai
        if (isset($data['id_member'])) {
            $id_member = (int)$data['id_member'];
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID member tidak diberikan']);
            exit;
        }

        $harga_penjualan = array_reduce($stock, function($carry, $item) {
            return $carry + ((int)$item['quantity'] * (float)$item['harga_beli']);
        }, 0);
        $total_bayar  = isset($data['total_bayar'])  ? (float)$data['total_bayar'] : 0;
        $total_ongkir  = isset($data['total_ongkir'])  ? (float)$data['total_ongkir'] : 0;

        $sql = "INSERT INTO penjualan (id_user, id_member, jumlah_penjualan, total_pembayaran, total_ongkir, status, created_by, created_at) VALUES ($id_user, $id_member, $harga_penjualan, $total_bayar, $total_ongkir, 'N', $id_user, NOW())";
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
    case 'PUT':
        // Update status hutang menjadi lunas
        $query_data = $_GET;
        if (!isset($query_data['id_hutang'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID hutang tidak diberikan']);
            exit;
        }
        $id_hutang = (int)$query_data['id_hutang'];
        $status = isset($query_data['status']) ? $query_data['status'] : 'Y';

        $sql = "UPDATE penjualan SET status = '$status' WHERE id_penjualan = $id_hutang";
        if ($conn->query($sql) === FALSE) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal mengupdate status hutang']);
            exit;
        }
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
