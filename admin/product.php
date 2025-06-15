<div class="page product-page">
    <h1>Daftar Barang</h1>
    <button class="createBtn" id="createProductBtn">Buat Barang</button>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
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
                <td>${product.id}</td>
                <td>${product.nama}</td>
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
            nama: document.getElementById('product_name').value
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
        } else {
            console.error('Barang tidak ditemukan');
        }
    } catch (error) {
        console.error('Gagal mendapatkan data barang:', error);
    }
}
</script>