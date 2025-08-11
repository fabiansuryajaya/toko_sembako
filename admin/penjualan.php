<div class="page product-page">
    <h1>Penjualan Barang</h1>
    <div class="create-container">
        <button class="createBtn" id="createProductBtn">Form Penjualan Barang</button>
    </div>

    <div class="filter-container">
        <div class="filter">
            <label for="from_date">Dari :</label>
            <input type="date" id="from_date" name="from_date">

            <label for="to_date">Sampai :</label>
            <input type="date" id="to_date" name="to_date">
        </div>
        <button id="filter_btn">Filter</button>
    </div>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal Penjualan</th>
                <th>Jumlah Penjualan</th>
                <th>Nama User</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="PenjualanModal" class="modal" style="display: none;">
        <div class="modal-content" style="position: relative; padding-bottom: 64px;">
            <h2>Buat Barang</h2>
            <div style="margin-bottom: 16px; border-bottom: 1px solid #ccc; padding-bottom: 8px;">
                <label for="product_id">Nama Barang:</label>
                <select id="product_id" name="product_id" required style="width: 100%;"></select>

                <button type="button" id="addProductBtn">Add</button>
                <button type="button" id="editPriceBtn">Ganti Harga</button>
            </div>
            <table border="1" cellspacing="0" cellpadding="8" id="productTable">
                <thead>
                    <tr>
                        <td>Nama</td>
                        <!-- <td>Supplier</td> -->
                        <td>Satuan</td>
                        <td>Harga Beli</td>
                        <td>Quantity</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div style="position: absolute; right: 16px; bottom: 16px; text-align: right;">
                <button type="button" id="closeModalBtn">Batal</button>
                <button type="button" id="savePenjualanBtn">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Popup modal detail penjualan -->
    <div id="DetailModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Detail Penjualan</h2>
            <table border="1" cellspacing="0" cellpadding="8">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Penjualan</th>
                        <th>Harga Penjualan</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody"></tbody>
            </table>
            <div style="text-align: right;">
                <button type="button" id="closeDetailModalBtn">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Select2 CSS dan JS -->
<link href="../assets/css/library/select2.min.css" rel="stylesheet" />
<script src="../assets/js/library/select2.min.js"></script>

<script>
    // on document ready
    $(document).ready(function () {
        // init
        const start_date =document.getElementById('from_date');
        const to_date    =document.getElementById('to_date');
        start_date.value = new Date().toISOString().split('T')[0]; // Set to today
        to_date.value    = new Date().toISOString().split('T')[0]; // Set to today

        const product_list = {};
        async function fetchProduct() {
            try {
                const result = await callAPI({ url: '../api/product.php', method: 'GET' });
                const productSelect = document.getElementById('product_id');;
                productSelect.innerHTML = '<option value="">Pilih Barang</option>';

                result.data.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id_product;
                    option.textContent = product.nama_product + " - " + product.nama_satuan + " - " + formatCurrencyIDR(product.harga_beli_product);
                    productSelect.appendChild(option);

                    product_list[product.id_product] = product;
                });

                // Aktifkan Select2 setelah isi data
                $("#product_id").select2({
                    placeholder: "Pilih Barang",
                    allowClear: true
                });
            } catch (error) {
                console.error('Gagal memuat barang:', error);
            }
        }

        fetchProduct();

        // get data penjualan
        async function fetchPenjualan() {
            try {
                const result = await callAPI({ url: '../api/penjualan.php', method: 'GET' });
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = ''; // Clear existing rows

                result.data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id_penjualan}</td>
                        <td>${item.created_at}</td>
                        <td>${item.jumlah_penjualan}</td>
                        <td>${item.nama_user}</td>
                        <td><button class="detailBtn" data-id="${item.id_penjualan}">Detail</button></td>
                    `;
                    tbody.appendChild(row);
                });

                // Add event listener for detail buttons
                document.querySelectorAll('.detailBtn').forEach(button => {
                    button.addEventListener('click', function () {
                        const idPenjualan = this.getAttribute('data-id');
                        const detailModal = document.getElementById('DetailModal');
                        const detailTableBody = document.getElementById('detailTableBody');

                        detailTableBody.innerHTML = ''; // Clear existing rows
                        detailModal.style.display = 'flex';

                        callAPI({ url: `../api/penjualan.php?id_penjualan=${idPenjualan}&action=detail`, method: 'GET' })
                            .then(result => {
                                const detailData = result.data
                                detailData.forEach(detail => {
                                    const detailRow = document.createElement('tr');
                                    detailRow.innerHTML = `
                                        <td>${detail.id_produk}</td>
                                        <td>${detail.nama_product}</td>
                                        <td>${detail.jumlah_penjualan}</td>
                                        <td>${formatCurrencyIDR(detail.harga_penjualan)}</td>
                                    `;
                                    detailTableBody.appendChild(detailRow);
                                });
                            })
                            .catch(error => {
                                console.error('Gagal memuat detail penjualan:', error);
                            });
                    });
                });
            } catch (error) {
                console.error('Gagal memuat penjualan:', error);
            }
        }

        fetchPenjualan();

        // closeDetailModalBtn
        const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
        closeDetailModalBtn.addEventListener('click', () => {
            const detailModal = document.getElementById('DetailModal');
            detailModal.style.display = 'none';
        });

        function updateTotal(input) {
            const row = input.closest('tr');
            const hargaBeli = parseFloat(row.querySelector('.harga_beli').value);
            const quantity = parseInt(row.querySelector('.quantity').value);
            const totalCell = row.querySelector('.total');
            console.log(formatCurrencyIDR(hargaBeli * quantity));

            totalCell.textContent = formatCurrencyIDR(hargaBeli * quantity);
        }

        // addProductButton
        const table = document.querySelector('#productTable tbody');
        const addProductButton = document.getElementById('addProductBtn');
        addProductButton.addEventListener('click', async () => {
            const product_id = document.getElementById('product_id').value;

            const product = product_list[product_id];
            if (!product) {
                alert('Silakan pilih barang terlebih dahulu.');
                return;
            }

            // Cek apakah produk sudah ada di tabel
            const existingRow = table.querySelector(`tr[data-id="${product.id_product}"]`);
            if (existingRow) {
                // tambah quantity jika produk sudah ada
                const quantityInput = existingRow.querySelector('.quantity');
                quantityInput.value = parseInt(quantityInput.value) + 1;
                updateTotal(quantityInput);
                return;
            }

            // Gunakan createElement agar tidak overwrite innerHTML dan kehilangan event/input value
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', product.id_product);
            tr.innerHTML = `
                <td>${product.nama_product}</td>
                <td>${product.nama_satuan}</td>
                <td><input type="number"  disabled class="harga_beli" value="${product.harga_beli_product}" min="0"></td>
                <td><input type="number" class="quantity" value="1" min="1"></td>
                <td class="total">${formatCurrencyIDR(product.harga_beli_product)}</td>
            `;
            table.appendChild(tr);
        });
        // editPriceBtn
        const editPriceBtn = document.getElementById('editPriceBtn');
        editPriceBtn.addEventListener('click', () => {
            const hargaBeliInputs = table.querySelectorAll('.harga_beli');
            hargaBeliInputs.forEach(input => {
                input.disabled = !input.disabled; // Toggle disabled state
            });
        });

        // closeModalBtn
        const closeModalBtn = document.getElementById('closeModalBtn');
        closeModalBtn.addEventListener('click', () => {
            const modal = document.getElementById('PenjualanModal');
            modal.style.display = 'none';
        });

        // savePenjualanBtn
        const savePenjualanBtn = document.getElementById('savePenjualanBtn');
        savePenjualanBtn.addEventListener('click', async () => {
            const rows = table.querySelectorAll('tr');
            const penjualanData = [];

            rows.forEach(row => {
                const productId = row.getAttribute('data-id');
                const hargaBeliInput = row.querySelector('.harga_beli');
                const quantityInput = row.querySelector('.quantity');
                const quantity = parseInt(quantityInput.value);
                if (quantity > 0) {
                    penjualanData.push({
                        product_id: productId,
                        harga_beli: parseFloat(hargaBeliInput.value),
                        quantity: quantity
                    });
                }
            });

            if (penjualanData.length === 0) {
                alert('Tidak ada barang yang dipenjualan.');
                return;
            }

            const body = {
                penjualan: penjualanData
            }

            try {
                const result = await callAPI({
                    url: '../api/penjualan.php',
                    method: 'POST',
                    body
                });
                if (result.status !== 0) {
                    alert(result.message);
                    return;
                }
                fetchPenjualan(); // Refresh the penjualan data
                alert('Penjualan berhasil!');
                table.innerHTML = ''; // Clear the table after saving
                const modal = document.getElementById('PenjualanModal');
                modal.style.display = 'none';
            } catch (error) {
                console.error('Gagal menyimpan penjualan:', error);
            }
        });

        // createProductBtn
        const createProductBtn = document.getElementById('createProductBtn');
        createProductBtn.addEventListener('click', () => {
            const modal = document.getElementById('PenjualanModal');
            modal.style.display = 'flex';
        });

        // Delegate quantity input change event to update total
        table.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('quantity')) {
                updateTotal(e.target);
            }
            if (e.target && e.target.classList.contains('harga_beli')) {
                updateTotal(e.target);
            }
        });

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('PenjualanModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // keyboard esc
        window.addEventListener('keydown', function (event) {
            const modal = document.getElementById('PenjualanModal');
            if (event.key === 'Escape') {
                modal.style.display = 'none';
            }
        });
    });
</script>
<!-- 
<style>
.select2-container {
    width: 100% !important;
}

.select2-dropdown {
    z-index: 9999;
}

.modal {
    z-index: 9990;
}
</style> -->
