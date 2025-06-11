DROP DATABASE IF EXISTS toko_sembako;
CREATE DATABASE toko_sembako;
CREATE TABLE produk(
    id_produk INT NOT NULL AUTO_INCREMENT,
    id_supplier INT NOT NULL,
    id_satuan INT NOT NULL,
    nama_produk VARCHAR(50) NOT NULL,
    harga_beli_produk INT NOT NULL,
    harga_jual_produk INT NOT NULL,
    stok_produk INT NOT NULL,
    PRIMARY KEY (id_produk)
);

CREATE TABLE satuan(
    id_satuan INT NOT NULL AUTO_INCREMENT,
    nama_satuan VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_satuan)
);

CREATE TABLE supplier(
    id_supplier INT NOT NULL AUTO_INCREMENT,
    nama_supplier VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_supplier)
);

CREATE TABLE user(
    id_user INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_user)
);

CREATE TABLE penjualan(
    id_penjualan INT NOT NULL AUTO_INCREMENT,
    id_produk INT NOT NULL,
    id_user INT NOT NULL,
    jumlah_penjualan INT NOT NULL,
    PRIMARY KEY (id_penjualan)
);

CREATE TABLE detail_penjualan(
    id_detail_penjualan INT NOT NULL AUTO_INCREMENT,
    id_penjualan INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah_penjualan INT NOT NULL,
    harga_penjualan INT NOT NULL,
    PRIMARY KEY (id_detail_penjualan)
);

CREATE TABLE pembelian (
    id_pembelian INT NOT NULL AUTO_INCREMENT,
    id_produk INT NOT NULL,
    id_user INT NOT NULL,
    id_supplier INT NOT NULL,
    jumlah_pembelian INT NOT NULL,
    PRIMARY KEY (id_pembelian)
);

CREATE TABLE detail_pembelian (
    id_detail_pembelian INT NOT NULL AUTO_INCREMENT,
    id_pembelian INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah_pembelian INT NOT NULL,
    harga_pembelian INT NOT NULL,
    PRIMARY KEY (id_detail_pembelian)
);