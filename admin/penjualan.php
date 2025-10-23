<div class="page product-page">
    <h1>Penjualan Barang</h1>
    <div class="create-container">
        <button class="createBtn" id="createProductBtn">Form Penjualan Barang</button>
    </div>

    <div class="filter-container">
        <div class="filter">
            <label for="from_date">Dari :</label>
            <input type="date" id="from_date" name="from_date">

            <label for="to_date">Sampai :</label>
            <input type="date" id="to_date" name="to_date">
        </div>
        <button id="filter_btn">Filter</button>
    </div>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal Penjualan</th>
                <th>Jumlah Penjualan</th>
                <th>Nama User</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="PenjualanModal" class="modal" style="display: none;">
        <div class="modal-content" style="position: relative; padding-bottom: 64px;">
            <input type="hidden" id="edit_penjualan_id" />
            <h2>Buat Barang</h2>
            <div style="margin-bottom: 16px; border-bottom: 1px solid #ccc; padding-bottom: 8px;">
                <!-- pilih user -->
                <label for="user_id">User:</label>
                <select id="user_id" name="user_id" required style="width: 100%;"></select>

                <label for="product_id">Nama Barang:</label>
                <select id="product_id" name="product_id" required style="width: 100%;"></select>

                <button type="button" id="addProductBtn">Add</button>
                <button type="button" id="editPriceBtn">Ganti Harga</button>
            </div>

            <table border="1" cellspacing="0" cellpadding="8" id="productTable">
                <thead>
                    <tr>
                        <td style="width: 5%;">Action</td>
                        <td>Nama</td>
                        <td>Satuan</td>
                        <td>Harga Beli</td>
                        <td>Quantity</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <!-- tambahkan input total bayar dan total kembalian -->
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="grand_total" style="margin-bottom:0;font-weight:bold;">Grand Total:</label>
                <input type="text" id="grand_total" name="grand_total" value="0" readonly style="width: 120px; padding: 4px; background: #f5f5f5; font-weight:bold;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="total_bayar" style="margin-bottom:0;">Total Bayar:</label>
                <input type="number" id="total_bayar" name="total_bayar" value="0" style="width: 120px; padding: 4px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="total_kembalian" style="margin-bottom:0;">Total Kembalian:</label>
                <input type="text" id="total_kembalian" name="total_kembalian" value="0" readonly style="width: 120px; padding: 4px; background: #f5f5f5;">
            </div>
            <div style="right: 16px; bottom: 16px; text-align: right;">
                <button type="button" id="closeModalBtn">Batal</button>
                <button type="button" id="savePenjualanBtn">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Popup modal detail penjualan -->
    <div id="DetailModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Detail Penjualan</h2>
            <table border="1" cellspacing="0" cellpadding="8">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Penjualan</th>
                        <th>Harga Penjualan</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody"></tbody>
            </table>
            <div style="text-align: right;">
                <button type="button" id="closeDetailModalBtn">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Struk -->
    <div id="StrukModal" class="modal" style="display:none;">
        <div class="modal-content" style="width:80mm;min-width:80mm;max-width:80mm;padding:8px;">
            <div id="strukContent" style="font-size:14px;font-family:calibri;max-height:80vh;overflow-y:auto;"></div>
            <div style="text-align:right;margin-top:8px;">
                <button type="button" id="printStrukBtn">Cetak</button>
                <button type="button" id="closeStrukModalBtn">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Select2 CSS dan JS -->
<link href="../assets/css/library/select2.min.css" rel="stylesheet" />
<script src="../assets/js/library/select2.min.js"></script>

<!-- update versi -->
<script src="../assets/js/admin/penjualan.js?v=20252109"></script>
