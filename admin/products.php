<div class="page">
    <h1>Daftar Produk</h1>
    <button class="createBtn" id="createProductBtn">Create Product</button>

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
<script>
    const btn = document.getElementById('createProductBtn');
    btn.addEventListener('click', () => {
        const modal = document.getElementById('createProductModal');
        modal.style.display = 'flex';
    }); 

    // assets/js/admin/product.js
    const tableBody = document.querySelector('.Products-page tbody');

    async function fetchProducts() {
        try {
            const data = await callAPI('../api/product.php');
            // Bersihkan tabel
            tableBody.innerHTML = '';

            data.forEach(product => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.nama}</td>
                    <td>${product.deskripsi}</td>
                    <td>${product.harga}</td>
                    <td>
                        <button data-id="${product.id}" class="edit-btn">Edit</button>
                        <button data-id="${product.id}" class="delete-btn">Hapus</button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } catch (error) {
            console.error('Gagal memuat product:', error);
        }
    }

    // Panggil fungsi saat halaman dimuat
    fetchProducts();

    async function addProducts() {
        try {
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);

            const data = await callAPI('../api/product.php', formData);
            fetchProducts();
        } catch (error) {
            console.error('Gagal menambahkan pemasok:', error);
        }
    }

    document.getElementById('createProductForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await addProducts();
        document.getElementById('createProductModal').style.display = 'none';
    }); 

</script>