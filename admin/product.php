<div class="page product-page">
    <h1>Daftar Barang</h1>
    <button class="createBtn" id="createProductBtn">Buat Barang</button>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Nama Supplier</th>
                <th>Nama Satuan</th>
                <th>Harga Jual</th>
                <th>Harga Beli</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="ProductModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Buat Barang</h2>
            <form id="createProductForm">
                <label for="name">Nama Barang:</label>
                <input type="text" id="product_name" name="name" required>

                <label for="supplier_id">Nama Supplier:</label>
                <select id="supplier_id" name="supplier_id" required>
                    <option value="">Pilih Supplier</option>
                </select>

                <label for="satuan_id">Nama Satuan:</label>
                <select id="satuan_id" name="satuan_id" required>
                    <option value="">Pilih Satuan</option>
                </select>

                <label for="harga_beli">Harga Beli:</label>
                <input type="number" id="harga_beli" name="harga_beli" required>

                <label for="harga_jual">Harga Jual:</label>
                <input type="number" id="harga_jual" name="harga_jual" required>
                
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>
                
                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>
let action = "create";
let editProductId = null;

function openModal(act = "create", id = null) {
    action = act;
    editProductId = id;
    document.getElementById('ProductModal').style.display = 'flex';
}

document.getElementById('createProductBtn').addEventListener('click', () => openModal("create"));

document.getElementById('closeModalBtn').addEventListener('click', () => {
    document.getElementById('ProductModal').style.display = 'none';
    document.getElementById('createProductForm').reset();
    editProductId = null;
});

const productTable = document.querySelector('.product-page tbody');

async function fetchProduct() {
    try {
        const data = await callAPI({ url: '../api/product.php', method: 'GET' });
        productTable.innerHTML = '';
        data.forEach(product => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${product.id_product}</td>
                <td>${product.nama_product}</td>
                <td>${product.nama_supplier}</td>
                <td>${product.nama_unit}</td>
                <td>${product.harga_beli_product}</td>
                <td>${product.harga_jual_product}</td>
                <td>${product.stok_product}</td>
                <td>
                    <button data-id="${product.id}" class="edit-btn">Edit</button>
                    <button data-id="${product.id}" class="delete-btn">Hapus</button>
                </td>
            `;
            productTable.appendChild(tr);
        });
        addTableEventListeners();
    } catch (error) {
        console.error('Gagal memuat product:', error);
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

fetchProduct();

async function submitProduct() {
    try {
        const method = action === "create" ? 'POST' : 'PUT';
        const body = {
            nama: document.getElementById('product_name').value,
            supplier_id: document.getElementById('supplier_id').value,
            satuan_id: document.getElementById('satuan_id').value,
            harga_beli: document.getElementById('harga_beli').value,
            harga_jual: document.getElementById('harga_jual').value,
            stok: document.getElementById('stok').value
        };
        if (action === "edit" && editProductId) {
            body.id = editProductId;
        }
        await callAPI({ url: '../api/product.php', method, body });
        fetchProduct();
    } catch (error) {
        console.error('Gagal menyimpan barang:', error);
    }
}

document.getElementById('createProductForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitProduct();
    document.getElementById('ProductModal').style.display = 'none';
    document.getElementById('createProductForm').reset();
    editProductId = null;
});

async function getEditData(id) {
    try {
        const data = await callAPI({ url: `../api/product.php?id=${id}`, method: 'GET' });
        if (data.length > 0) {
            openModal("edit", id);
            document.getElementById('product_name').value = data[0].nama;
            document.getElementById('supplier_id').value = data[0].supplier_id;
            document.getElementById('satuan_id').value = data[0].satuan_id;
            document.getElementById('harga_beli').value = data[0].harga_beli;
            document.getElementById('harga_jual').value = data[0].harga_jual;
            document.getElementById('stok').value = data[0].stok;
        } else {
            console.error('Barang tidak ditemukan');
        }
    } catch (error) {
        console.error('Gagal mendapatkan data barang:', error);
    }
}

async function fetchSuppliers() {
    try {
        const data = await callAPI({ url: '../api/supplier.php', method: 'GET' });
        const supplierSelect = document.getElementById('supplier_id');
        supplierSelect.innerHTML = '<option value="">Pilih Supplier</option>';
        data.forEach(supplier => {
            const option = document.createElement('option');
            option.value = supplier.id;
            option.textContent = supplier.nama;
            supplierSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Gagal memuat pemasok:', error);
    }
}
fetchSuppliers();   

async function fetchSatuan() {
    try {
        const data = await callAPI({ url: '../api/unit.php', method: 'GET' });
        const satuanSelect = document.getElementById('satuan_id');
        satuanSelect.innerHTML = '<option value="">Pilih Satuan</option>';
        data.forEach(satuan => {
            const option = document.createElement('option');
            option.value = satuan.id;
            option.textContent = satuan.nama;
            satuanSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Gagal memuat satuan:', error);
    }
}
fetchSatuan();
</script>