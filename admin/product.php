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
                <th id="header_id">ID</th>
                <th id="header_nama_barang">Nama Barang</th>
                <th id="header_nama_supplier">Nama Supplier</th>
                <th id="header_nama_satuan">Nama Satuan</th>
                <th id="header_harga_beli">Modal</th>
                <th id="header_harga_jual">Harga Jual</th>
                <th id="header_stok">Stok</th>
                <th id="header_status">Status</th>
                <th id="header_aksi">Aksi</th>
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

                <label for="harga_beli">Modal:</label>
                <input type="number" id="harga_beli" name="harga_beli" required>

                <label for="harga_jual">Harga Jual:</label>
                <input type="number" id="harga_jual" name="harga_jual" required>
                
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>

                <!-- add description textarea -->
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>

                <!-- checkbox status -->
                <div style="display: flex; align-items: center;">
                    <label for="status">Status:</label>
                    <input type="checkbox" id="cb_status" name="status">
                </div>

                <button type="button" id="closeModalBtn">Batal</button>
                <button type="submit">Simpan</button>
               
            </form>
        </div>
    </div>

    <div id="DetailModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Detail Barang</h2>
            <div id="detailContent"></div>
            <button type="button" id="closeDetailModalBtn">Tutup</button>
        </div>
    </div>
</div>

<!-- Tambahkan Select2 CSS dan JS -->
<link href="../assets/css/library/select2.min.css" rel="stylesheet" />
<script src="../assets/js/library/select2.min.js"></script>

<script>
    // #status on change
    $('#status').on('change', function() {
        // jika checked maka value = 'Y' else 'N'
        if ($(this).is(':checked')) {
            // save to localStorage
            localStorage.setItem('product_status', 'Y');
        } else {
            localStorage.setItem('product_status', 'N');
        }
    });

    // on document ready
    $(document).ready(function() {
        let action = "create";
        let editProductId = null;
        const role = getRole();
        const product_status = localStorage.getItem('product_status') || 'Y';
        $('#status').prop('checked', product_status === 'Y');

        if (role !== 'admin') {
            $('#createProductBtn').hide();
        }

        function hide_columns() {
            if (role !== 'admin') {
                // document.getElementById('header_aksi').style.display = 'none';
                document.getElementById('header_nama_supplier').style.display = 'none';
                document.getElementById('header_harga_beli').style.display = 'none';

                const body_aksi = document.querySelectorAll('.body_aksi');
                body_aksi.forEach(td => {
                    td.style.display = 'none';
                });
                const body_nama_supplier = document.querySelectorAll('.body_nama_supplier');
                body_nama_supplier.forEach(td => {
                    td.style.display = 'none';
                });
                const body_harga_beli = document.querySelectorAll('.body_harga_beli');
                body_harga_beli.forEach(td => {
                    td.style.display = 'none';
                });
            }
        }
        hide_columns();

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

        document.getElementById('closeDetailModalBtn').addEventListener('click', () => {
            document.getElementById('DetailModal').style.display = 'none';
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
                        <td class="body_id">${product.id_product}</td>
                        <td class="body_nama_barang">${product.nama_product}</td>
                        <td class="body_nama_supplier">${product.nama_supplier}</td>
                        <td class="body_nama_satuan">${product.nama_satuan}</td>
                        <td class="body_harga_beli">${formatCurrencyIDR(product.harga_beli_product)}</td>
                        <td class="body_harga_jual">${formatCurrencyIDR(product.harga_jual_product)}</td>
                        <td class="body_stok">${formatNumber(product.stok_product)}</td>
                        <td class="body_status">${status}</td>
                        <td class="body_aksi">
                            <button data-id="${product.id_product}" class="detail-btn">Detail</button>
                            <button data-id="${product.id_product}" class="edit-btn">Edit</button>
                            <button data-id="${product.id_product}" class="delete-btn">Hapus</button>
                        </td>
                    `;
                    productTable.appendChild(tr);
                });
                hide_columns();
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

            // detail-btn
            document.querySelectorAll('.detail-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const id = this.getAttribute('data-id');
                    const result = await callAPI({ url: `../api/product.php?id_product=${id}&status=ALL`, method: 'GET' });
                    if (result.data.length > 0) {
                        const product = result.data[0];
                        document.getElementById('detailContent').innerHTML = `
                            <div style="padding: 10px;">
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">ID</span>
                                    <span>: ${product.id_product}</span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Nama Barang</span>
                                    <span>: ${product.nama_product}</span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Supplier</span>
                                    <span>: ${product.nama_supplier}</span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Satuan</span>
                                    <span>: ${product.nama_satuan}</span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Modal</span>
                                    <span>: <span >${formatCurrencyIDR(product.harga_beli_product)}</span></span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Harga Jual</span>
                                    <span>: <span>${formatCurrencyIDR(product.harga_jual_product)}</span></span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Stok</span>
                                    <span>: <span>${formatNumber(product.stok_product)}</span></span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block;">Status</span>
                                    <span>: <span style="font-weight:bold; color:${product.status === 'Y' ? '#28a745' : '#dc3545'};">
                                        ${product.status === 'Y' ? 'Aktif' : 'Tidak Aktif'}
                                    </span></span>
                                </div>
                                
                                <div style="margin-bottom: 10px;">
                                    <span style="font-weight:bold; width:140px; display:inline-block; vertical-align:top;">Deskripsi</span>
                                    <span>: <div style="background:#f8f9fa; border-radius:5px; padding:8px; margin-top:2px; display:inline-block;">
                                        ${product.description ? product.description.replace(/\n/g, "<br>") : '<em>Tidak ada deskripsi</em>'}
                                    </div></span>
                                </div>
                            </div>
                        `;
                        document.getElementById('DetailModal').style.display = 'flex';
                    } else {
                        console.error('Product tidak ditemukan');
                    }
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
                    stok: document.getElementById('stok').value,
                    deskripsi: document.getElementById('deskripsi').value,
                    status: document.getElementById('cb_status').checked ? 'Y' : 'N'
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
                    document.getElementById('deskripsi').value    = result.data[0].description;
                    if (result.data[0].status === 'Y') {
                        document.getElementById('cb_status').checked = true;
                    } else {
                        document.getElementById('cb_status').checked = false;
                    }

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