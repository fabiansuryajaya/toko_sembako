<div class="page">
    <h1>Daftar Produk</h1>
    <button class="createBtn">Create Product</button>

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
    <div id="createProductModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Create Product</h2>
            <form id="createProductForm">
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" required>

                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" required></textarea>

                <label for="price">Harga:</label>
                <input type="number" id="price" name="price" required>

                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>