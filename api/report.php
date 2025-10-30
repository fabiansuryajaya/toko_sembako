<?php
require_once("../connection.php");

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Tambah product baru
        $data = json_decode(file_get_contents('php://input'), true);
    
        $product_id = isset($data['product_id']) ? explode( ',',$data['product_id']) : [];
        $from_date  = isset($data['from_date'])  ? $conn->real_escape_string($data['from_date']) : ''; // YYYY-MM-DD
        $to_date    = isset($data['to_date'])    ? $conn->real_escape_string($data['to_date'])   : ''; // YYYY-MM-DD
        
        // data penjualan
        $sql = "SELECT pr.nama_product, sum(dp.jumlah_penjualan) as total_jumlah, sum(dp.harga_penjualan * dp.jumlah_penjualan) as total_pembayaran, s.nama_satuan, dp.harga_penjualan, dp.harga_pembelian, su.nama_supplier
                FROM penjualan p
                JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                JOIN product pr ON dp.id_produk = pr.id_product
                JOIN satuan s ON pr.id_satuan = s.id_satuan
                JOIN supplier su ON pr.id_supplier = su.id_supplier
                WHERE p.status = 'Y'";
        if (!empty($product_id)) $sql .= " AND dp.id_produk IN (" . implode(',', $product_id) . ")";
        if (!empty($from_date))  $sql .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d') >= '$from_date'"; // YYYY-MM-DD
        if (!empty($to_date))    $sql .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d') <= '$to_date'"; // YYYY-MM-DD
        $sql .= " GROUP BY pr.nama_product, dp.harga_penjualan, dp.harga_pembelian ORDER BY pr.nama_product ASC";

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
    case 'GET':
    case 'PUT':
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
