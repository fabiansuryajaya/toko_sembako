<div class="page">
    <h1>Daftar Pemasok</h1>
    <button class="createBtn" id="createSupplierBtn">Buat Pemasok</button>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pemasok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="createSupplierModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Buat Pemasok</h2>
            <form id="createSupplierForm">
                <label for="name">Nama Pemasok:</label>
                <input type="text" id="supplier_name" name="name" required>

                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>

    const supplier_btn = document.getElementById('createSupplierBtn');
    supplier_btn.addEventListener('click', () => {
        const modal = document.getElementById('createSupplierModal');
        modal.style.display = 'flex';
    }); 

    // assets/js/admin/supplier.js
    const supplier_table = document.querySelector('.suppliers-page tbody');

    async function fetchSuppliers() {
        try {
            const data = await callAPI({url : '../api/suppliers.php', method: 'GET'});
            // Bersihkan tabel
            supplier_table.innerHTML = '';

            data.forEach(supplier => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${supplier.id}</td>
                    <td>${supplier.nama}</td>
                    <td>${supplier.deskripsi}</td>
                    <td>${supplier.harga}</td>
                    <td>
                        <button data-id="${supplier.id}" class="edit-btn">Edit</button>
                        <button data-id="${supplier.id}" class="delete-btn">Hapus</button>
                    </td>
                `;
                supplier_table.appendChild(tr);
            });
        } catch (error) {
            console.error('Gagal memuat supplier:', error);
        }
    }

    // Panggil fungsi saat halaman dimuat
    fetchSuppliers();

    async function addSuppliers() {
        try {
            const body = {
                nama: document.getElementById('supplier_name').value
            }
            const data = await callAPI({url : '../api/suppliers.php', method: 'POST', body: body});
            fetchSuppliers();
        } catch (error) {
            console.error('Gagal menambahkan pemasok:', error);
        }
    }

    document.getElementById('createSupplierForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await addSuppliers();
        document.getElementById('createSupplierModal').style.display = 'none';
    });

</script>