<div class="page supplier-page">
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
    <div id="SupplierModal" class="modal" style="display: none;">
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
let action = "create";
let editSupplierId = null;

function openModal(act = "create", id = null) {
    action = act;
    editSupplierId = id;
    document.getElementById('SupplierModal').style.display = 'flex';
}

document.getElementById('createSupplierBtn').addEventListener('click', () => openModal("create"));

document.getElementById('closeModalBtn').addEventListener('click', () => {
    document.getElementById('SupplierModal').style.display = 'none';
    document.getElementById('createSupplierForm').reset();
    editSupplierId = null;
});

const supplierTable = document.querySelector('.supplier-page tbody');

async function fetchSupplier() {
    try {
        const result = await callAPI({ url: '../api/supplier.php', method: 'GET' });
        supplierTable.innerHTML = '';
        result.data.forEach(supplier => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${supplier.id}</td>
                <td>${supplier.nama}</td>
                <td>
                    <button data-id="${supplier.id}" class="edit-btn">Edit</button>
                    <button data-id="${supplier.id}" class="delete-btn">Hapus</button>
                </td>
            `;
            supplierTable.appendChild(tr);
        });
        addTableEventListeners();
    } catch (error) {
        console.error('Gagal memuat supplier:', error);
    }
}

function addTableEventListeners() {
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.getAttribute('data-id');
            await getEditData(id);
        });
    });
    // Tambahkan event listener untuk tombol hapus jika diperlukan
}

fetchSupplier();

async function submitSupplier() {
    try {
        const method = action === "create" ? 'POST' : 'PUT';
        const body = {
            nama: document.getElementById('supplier_name').value
        };
        if (action === "edit" && editSupplierId) {
            body.id = editSupplierId;
        }
        await callAPI({ url: '../api/supplier.php', method, body });
        fetchSupplier();
    } catch (error) {
        console.error('Gagal menyimpan pemasok:', error);
    }
}

document.getElementById('createSupplierForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitSupplier();
    document.getElementById('SupplierModal').style.display = 'none';
    document.getElementById('createSupplierForm').reset();
    editSupplierId = null;
});

async function getEditData(id) {
    try {
        const result = await callAPI({ url: `../api/supplier.php?id=${id}`, method: 'GET' });
        if (result.data.length > 0) {
            openModal("edit", id);
            document.getElementById('supplier_name').value = result.data[0].nama;
        } else {
            console.error('Pemasok tidak ditemukan');
        }
    } catch (error) {
        console.error('Gagal mendapatkan data pemasok:', error);
    }
}
</script>