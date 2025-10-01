<?php
require_once("../connection.php");

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query_data = $_GET;
        $product_id = isset($query_data['product_id']) ? (int)$query_data['product_id'] : 0;
        $from_date  = isset($query_data['from_date'])  ? $conn->real_escape_string($query_data['from_date']) : ''; // YYYY-MM-DD
        $to_date    = isset($query_data['to_date'])    ? $conn->real_escape_string($query_data['to_date'])   : ''; // YYYY-MM-DD
        
        // data penjualan
        $sql = "SELECT pr.nama_product, sum(dp.jumlah_penjualan) as total_jumlah, sum(dp.harga_penjualan * dp.jumlah_penjualan) as total_pembayaran, s.nama_satuan, dp.harga_penjualan
                FROM penjualan p
                JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                JOIN product pr ON dp.id_produk = pr.id_product
                JOIN satuan s ON pr.id_satuan = s.id_satuan
                WHERE p.status = 'Y'";
        if ($product_id > 0)    $sql .= " AND dp.id_produk = $product_id";
        if (!empty($from_date)) $sql .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d') >= '$from_date'"; // YYYY-MM-DD
        if (!empty($to_date))   $sql .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d') <= '$to_date'"; // YYYY-MM-DD
        $sql .= " GROUP BY pr.nama_product, dp.harga_penjualan ORDER BY pr.nama_product ASC";

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
    case 'PUT':
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode tidak diizinkan']);
        break;
}
