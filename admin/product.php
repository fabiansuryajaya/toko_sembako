<div class="page product-page">
    <h1>Daftar Barang</h1>
    <div class="create-container">
        <button class="createBtn" id="createProductBtn">Buat Barang</button>
    </div>

    <div class="filter-container">
        <div class="filter">
            <label for="status">Status:</label>
            <input type="checkbox" id="status" name="status" value="Y">
            <label for="status">Aktif</label>
        </div>
        <button id="filter_btn">Filter</button>
    </div>

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
                <th>Status</th>
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
                <select id="supplier_id" name="supplier_id" style="width: 100%" required>
                    <option value="">Pilih Supplier</option>
                </select>

                <label for="satuan_id">Nama Satuan:</label>
                <select id="satuan_id" name="satuan_id" style="width: 100%" required>
                    <option value="">Pilih Satuan</option>
                </select>

                <label for="harga_beli">Harga Beli:</label>
                <input type="number" id="harga_beli" name="harga_beli" required>

                <label for="harga_jual">Harga Jual:</label>
                <input type="number" id="harga_jual" name="harga_jual" required>
                
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>
                <button type="button" id="closeModalBtn">Batal</button>
                <button type="submit">Simpan</button>
               
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan Select2 CSS dan JS -->
<link href="../assets/css/library/select2.min.css" rel="stylesheet" />
<script src="../assets/js/library/select2.min.js"></script>

<script>
    // on document ready
    $(document).ready(function() {
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

        async function getData() {
            try {
                const status = document.getElementById('status').checked ? 'Y' : 'N';
                let url = '../api/product.php?status=' + status;
                const result = await callAPI({ url, method: 'GET' });
                productTable.innerHTML = '';
                result.data.forEach(product => {
                    const tr = document.createElement('tr');
                    let status = product.status === 'Y' ? 'Aktif' : 'Tidak Aktif';

                    tr.innerHTML = `
                        <td>${product.id_product}</td>
                        <td>${product.nama_product}</td>
                        <td>${product.nama_supplier}</td>
                        <td>${product.nama_satuan}</td>
                        <td>${product.harga_beli_product}</td>
                        <td>${product.harga_jual_product}</td>
                        <td>${product.stok_product}</td>
                        <td>${status}</td>
                        <td>
                            <button data-id="${product.id_product}" class="edit-btn">Edit</button>
                            <button data-id="${product.id_product}" class="delete-btn">Hapus</button>
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

            // delete-btn
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const id = this.getAttribute('data-id');
                    await deleteData(id);
                });
            });
        }

        getData();

        // filter_btn onclick getData
        document.getElementById('filter_btn').addEventListener('click', () => {
            getData();
        });

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
                const result = await callAPI({ url: '../api/product.php', method, body });
                if (result.status !== 0) {
                    alert(result.message);
                    return;
                }
                getData();
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
                const result = await callAPI({ url: `../api/product.php?id_product=${id}&status=ALL`, method: 'GET' });
                if (result.data.length > 0) {
                    openModal("edit", id);
                    document.getElementById('product_name').value = result.data[0].nama_product;
                    document.getElementById('supplier_id').value  = result.data[0].id_supplier;
                    document.getElementById('satuan_id').value    = result.data[0].id_satuan;
                    document.getElementById('harga_beli').value   = result.data[0].harga_beli_product;
                    document.getElementById('harga_jual').value   = result.data[0].harga_jual_product;
                    document.getElementById('stok').value         = result.data[0].stok_product;

                    $("#supplier_id").change();
                    $("#satuan_id").change();
                } else {
                    console.error('Barang tidak ditemukan');
                }
            } catch (error) {
                console.error('Gagal mendapatkan data barang:', error);
            }
        }

        async function deleteData(id) {
            try {
                await callAPI({ url: `../api/product.php?id=${id}`, method: 'DELETE' });
                getData();
            } catch (error) {
                
            }
        }

        async function fetchSuppliers() {
            try {
                const result = await callAPI({ url: '../api/supplier.php', method: 'GET' });
                const supplierSelect = document.getElementById('supplier_id');
                supplierSelect.innerHTML = '<option value="">Pilih Supplier</option>';
                result.data.forEach(supplier => {
                    const option = document.createElement('option');
                    option.value = supplier.id;
                    option.textContent = supplier.nama;
                    supplierSelect.appendChild(option);
                });
                $("#supplier_id").select2({
                    placeholder: "Pilih Supplier",
                    allowClear: true
                });
            } catch (error) {
                console.error('Gagal memuat pemasok:', error);
            }
        }
        fetchSuppliers();   

        async function fetchSatuan() {
            try {
                const result = await callAPI({ url: '../api/unit.php', method: 'GET' });
                const satuanSelect = document.getElementById('satuan_id');
                satuanSelect.innerHTML = '<option value="">Pilih Satuan</option>';
                result.data.forEach(satuan => {
                    const option = document.createElement('option');
                    option.value = satuan.id;
                    option.textContent = satuan.nama;
                    satuanSelect.appendChild(option);
                });
                $("#satuan_id").select2({
                    placeholder: "Pilih Satuan",
                    allowClear: true
                });
            } catch (error) {
                console.error('Gagal memuat satuan:', error);
            }
        }
        fetchSatuan();
    });
</script>