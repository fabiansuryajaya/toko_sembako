alter table detail_penjualan add column harga_pembelian int default 0 after harga_penjualan;
alter table penjualan add column nama_pembeli varchar(100) default '' after id_member;