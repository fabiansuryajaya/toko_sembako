-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2025 at 05:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_sembako`
--
CREATE DATABASE IF NOT EXISTS `toko_sembako` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `toko_sembako`;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

DROP TABLE IF EXISTS `detail_pembelian`;
CREATE TABLE `detail_pembelian` (
  `id_detail_pembelian` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah_pembelian` int(11) NOT NULL,
  `harga_pembelian` int(11) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail_pembelian`, `id_pembelian`, `id_produk`, `jumlah_pembelian`, `harga_pembelian`, `status`) VALUES
(1, 1, 1, 3, 23423, 'Y'),
(2, 1, 3, 2, 8000, 'Y'),
(3, 2, 3, 100, 8000, 'Y'),
(4, 3, 3, 100, 8000, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

DROP TABLE IF EXISTS `detail_penjualan`;
CREATE TABLE `detail_penjualan` (
  `id_detail_penjualan` int(11) NOT NULL,
  `id_penjualan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah_penjualan` int(11) NOT NULL,
  `harga_penjualan` int(11) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id_detail_penjualan`, `id_penjualan`, `id_produk`, `jumlah_penjualan`, `harga_penjualan`, `status`) VALUES
(1, 1, 3, 10, 8000, 'Y'),
(2, 2, 1, 2, 100000, 'Y'),
(3, 3, 3, 2, 8000, 'Y'),
(4, 4, 3, 5, 8000, 'Y'),
(5, 5, 1, 1, 100000, 'Y'),
(6, 5, 3, 1, 8000, 'Y'),
(7, 6, 3, 3, 8000, 'Y'),
(8, 7, 3, 1, 8000, 'Y'),
(9, 7, 2, 1, 345345, 'Y'),
(10, 8, 3, 12, 8000, 'Y'),
(11, 8, 2, 1, 345345, 'Y'),
(12, 9, 3, 1, 8000, 'Y'),
(13, 10, 2, 1, 345345, 'Y'),
(14, 10, 3, 1, 8000, 'Y'),
(15, 11, 3, 5, 8000, 'Y'),
(16, 11, 2, 10, 345345, 'Y'),
(17, 12, 2, 10, 345345, 'Y'),
(18, 12, 3, 2, 8000, 'Y'),
(19, 13, 2, 1, 345345, 'Y'),
(20, 13, 3, 1, 8000, 'Y'),
(21, 14, 2, 5, 345345, 'Y'),
(22, 14, 3, 5, 8000, 'Y'),
(23, 15, 2, 1, 345345, 'Y'),
(24, 15, 3, 1, 8000, 'Y'),
(25, 16, 2, 1, 345345, 'Y'),
(26, 17, 2, 1, 345345, 'Y'),
(27, 17, 3, 1, 8000, 'Y'),
(28, 18, 2, 1, 345345, 'Y'),
(29, 19, 2, 1, 345345, 'Y'),
(30, 19, 4, 10, 400, 'Y'),
(31, 20, 2, 1, 345345, 'Y'),
(32, 20, 4, 1, 400, 'Y'),
(33, 21, 3, 1, 8000, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id_member` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nomor_hp` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id_member`, `nama`, `nomor_hp`) VALUES
(1, 'Amelia', '085102615372');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

DROP TABLE IF EXISTS `pembelian`;
CREATE TABLE `pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `jumlah_pembelian` int(11) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `id_user`, `id_supplier`, `jumlah_pembelian`, `status`, `created_by`, `created_at`) VALUES
(1, 1, 0, 5, 'Y', 1, '2025-07-02 15:04:00'),
(2, 1, 0, 100, 'Y', 1, '2025-07-07 13:12:32'),
(3, 1, 0, 100, 'Y', 1, '2025-08-12 12:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

DROP TABLE IF EXISTS `penjualan`;
CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_member` int(11) DEFAULT NULL,
  `jumlah_penjualan` int(11) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `id_produk`, `id_user`, `id_member`, `jumlah_penjualan`, `status`, `created_by`, `created_at`) VALUES
(1, 0, 1, NULL, 10, 'Y', 1, '2025-07-02 15:31:03'),
(2, 0, 1, NULL, 2, 'Y', 1, '2025-07-02 15:31:24'),
(3, 0, 1, NULL, 2, 'Y', 1, '2025-07-02 15:41:09'),
(4, 0, 1, NULL, 5, 'Y', 1, '2025-07-02 15:41:56'),
(5, 0, 1, NULL, 2, 'Y', 1, '2025-07-02 16:08:11'),
(6, 0, 1, NULL, 3, 'Y', 1, '2025-07-07 13:11:54'),
(7, 0, 1, NULL, 2, 'Y', 1, '2025-07-07 13:13:40'),
(8, 0, 1, NULL, 13, 'Y', 1, '2025-07-07 13:14:38'),
(9, 0, 1, NULL, 1, 'Y', 1, '2025-08-06 14:26:17'),
(10, 0, 1, NULL, 2, 'Y', 1, '2025-08-06 14:49:56'),
(11, 0, 1, NULL, 15, 'Y', 1, '2025-08-06 14:50:31'),
(12, 0, 1, NULL, 12, 'Y', 1, '2025-08-06 15:06:16'),
(13, 0, 1, 1, 2, 'Y', 1, '2025-08-08 13:50:42'),
(14, 0, 1, NULL, 10, 'Y', 1, '2025-08-08 14:40:25'),
(15, 0, 1, 1, 2, 'Y', 1, '2025-08-08 14:41:34'),
(16, 0, 1, 1, 1, 'Y', 1, '2025-08-08 14:48:01'),
(17, 0, 1, 1, 353345, 'N', 1, '2025-08-18 14:18:54'),
(18, 0, 1, 1, 345345, 'N', 1, '2025-08-12 12:08:59'),
(19, 0, 1, NULL, 11, 'Y', 1, '2025-08-12 12:41:56'),
(20, 0, 1, 1, 345745, 'N', 1, '2025-08-17 13:41:47'),
(21, 0, 1, 1, 8000, 'N', 1, '2025-08-12 13:10:09');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id_product` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_satuan` int(11) NOT NULL,
  `nama_product` varchar(50) NOT NULL,
  `harga_beli_product` int(11) NOT NULL,
  `harga_jual_product` int(11) NOT NULL,
  `stok_product` int(11) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `descripion` text DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id_product`, `id_supplier`, `id_satuan`, `nama_product`, `harga_beli_product`, `harga_jual_product`, `stok_product`, `status`, `descripion`) VALUES
(1, 1, 1, 'Beras Kepompong', 100000, 234234, 23423, 'N', ''),
(2, 1, 1, 'Beras Rojolele', 345345, 345345, 345310, 'Y', ''),
(3, 2, 2, 'kopi ABC', 8000, 10000, 250, 'Y', ''),
(4, 2, 4, 'Kopi ABC', 400, 1000, 989, 'Y', '');

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

DROP TABLE IF EXISTS `satuan`;
CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`, `status`) VALUES
(1, 'Sak', 'Y'),
(2, 'Renteng', 'Y'),
(3, 'Biji', 'Y'),
(4, 'Dus', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(50) NOT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `nomor_hp` varchar(16) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `status`, `nomor_hp`) VALUES
(1, 'Padi Sejati', 'Y', '08988871289'),
(2, 'Indofood', 'Y', '087857096777'),
(3, 'So Good', 'Y', '081256789012'),
(4, 'Philips', 'Y', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('admin','pegawai') DEFAULT 'pegawai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'pegawai', 'pegawai', 'pegawai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail_pembelian`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_detail_penjualan`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id_member`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_detail_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
