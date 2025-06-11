<div class="page">
    <h1>Daftar Pemasok</h1>
    <button class="createBtn">Buat Pemasok</button>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pemasok</th>
                <th>Deskripsi</th>
                <th>Harga</th>
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
                <input type="text" id="name" name="name" required>

                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>
    // assets/js/admin/supplier.js
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('.suppliers-page tbody');

        // async function fetchSuppliers() {
        //     try {
        //         const data = await apiCall('../api/supplier.php');
        //         // Bersihkan tabel
        //         tableBody.innerHTML = '';

        //         data.forEach(supplier => {
        //             const tr = document.createElement('tr');
        //             tr.innerHTML = `
        //                 <td>${supplier.id}</td>
        //                 <td>${supplier.nama}</td>
        //                 <td>${supplier.deskripsi}</td>
        //                 <td>${supplier.harga}</td>
        //                 <td>
        //                     <button data-id="${supplier.id}" class="edit-btn">Edit</button>
        //                     <button data-id="${supplier.id}" class="delete-btn">Hapus</button>
        //                 </td>
        //             `;
        //             tableBody.appendChild(tr);
        //         });
        //     } catch (error) {
        //         console.error('Gagal memuat supplier:', error);
        //     }
        // }

        // // Panggil fungsi saat halaman dimuat
        // fetchSuppliers();

        async function addSuppliers() {
            try {
                const formData = new FormData();
                formData.append('name', document.getElementById('name').value);

                const data = await apiCall('../api/supplier.php', formData);
                // fetchSuppliers();
            } catch (error) {
                console.error('Gagal menambahkan pemasok:', error);
            }
        }
    });

</script>