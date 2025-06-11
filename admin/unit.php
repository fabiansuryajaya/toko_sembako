<div class="page">
    <h1>Daftar Produk</h1>
    <button class="createBtn">Buat Produk</button>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="createUnitModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Buat Produk</h2>
            <form id="createProductForm">
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" required>

                <label for="satuan">Satuan:</label>
                <select id="satuan" name="satuan">
                </select>

                <label for="supplier">Supplier:</label>
                <select id="supplier" name="supplier">
                </select>

                <label for="price">Harga:</label>
                <input type="number" id="price" name="price" required>

                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>
