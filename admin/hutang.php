<div class="page product-page">
    <h1>Hutang Barang</h1>
    <div class="create-container">
        <button class="createBtn" id="createProductBtn">Form Hutang Barang</button>
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
                <th>Tanggal Hutang</th>
                <th>Nama Pembeli</th>
                <th>Jumlah Hutang</th>
                <th>Status Hutang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="HutangModal" class="modal" style="display: none;">
        <div class="modal-content" style="position: relative; padding-bottom: 64px;">
            <h2>Buat Hutang</h2>
            <div style="margin-bottom: 16px; border-bottom: 1px solid #ccc; padding-bottom: 8px;">
                <label for="product_id">Nama Barang:</label>
                <select id="product_id" name="product_id" required style="width: 100%;"></select>

                <!-- nama member -->
                <label for="member_id">Nama Member:</label>
                <select id="member_id" name="member_id" required style="width: 100%;"></select>

                <button type="button" id="addProductBtn">Add</button>
                <button type="button" id="editPriceBtn">Ganti Harga</button>
            </div>
            <table border="1" cellspacing="0" cellpadding="8" id="productTable">
                <thead>
                    <tr>
                        <td style="width: 5%;">Action</td>
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

            <!-- tambahkan input total bayar dan total kembalian -->
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="grand_total" style="margin-bottom:0;font-weight:bold;">Grand Total:</label>
                <input type="text" id="grand_total" name="grand_total" value="0" readonly style="width: 120px; padding: 4px; background: #f5f5f5; font-weight:bold;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="total_bayar" style="margin-bottom:0;">Total Bayar:</label>
                <input type="number" id="total_bayar" name="total_bayar" value="0" style="width: 120px; padding: 4px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="harga_ongkir" style="margin-bottom:0;">Harga Ongkir:</label>
                <input type="number" id="harga_ongkir" name="harga_ongkir" value="0" style="width: 120px; padding: 4px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 8px; align-items: center;">
                <label for="total_kembalian" style="margin-bottom:0;">Total Kembalian:</label>
                <input type="text" id="total_kembalian" name="total_kembalian" value="0" readonly style="width: 120px; padding: 4px; background: #f5f5f5;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 24px;"></div>
                <button type="button" id="closeModalBtn">Batal</button>
                <button type="button" id="saveHutangBtn">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Popup modal detail hutang -->
    <div id="DetailModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Detail Hutang</h2>
            <table border="1" cellspacing="0" cellpadding="8">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Hutang</th>
                        <th>Harga Hutang</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody"></tbody>
            </table>
            <div style="text-align: right;">
                <button type="button" id="closeDetailModalBtn">Tutup</button>
                <button type="button" id="cancelLunasModalBtn">Cancel Lunas</button>
            </div>
        </div>
    </div>

    <!-- Modal Struk -->
    <div id="StrukModal" class="modal" style="display:none;">
        <div class="modal-content" style="width:58mm;min-width:58mm;max-width:58mm;padding:8px;">
            <div id="strukContent" style="font-size:11px;font-family:Calibri;"></div>
            <div style="text-align:right;margin-top:8px;">
                <button type="button" id="printStrukBtn">Cetak</button>
                <button type="button" id="closeStrukModalBtn">Tutup</button>
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
        const start_date = document.getElementById('from_date');
        const to_date    = document.getElementById('to_date');
        edit_price       = false;
        start_date.value = new Date().toISOString().split('T')[0]; // Set to today
        to_date.value    = new Date().toISOString().split('T')[0]; // Set to today

        const grand_total = document.getElementById('grand_total');
        const total_bayar = document.getElementById('total_bayar');
        const total_kembalian = document.getElementById('total_kembalian');
        const harga_ongkir = document.getElementById('harga_ongkir');

        let id_hutang_modal = -1;

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

        // fetch member
        const member_list = {};
        async function fetchMember() {
            try {
                const result = await callAPI({ url: '../api/member.php', method: 'GET' });
                const memberSelect = document.getElementById('member_id');
                memberSelect.innerHTML = '<option value="">Pilih Member</option>';

                result.data.forEach(member => {
                    const option = document.createElement('option');
                    option.value = member.id;
                    option.textContent = member.nama + " - " + member.nomor_hp;
                    memberSelect.appendChild(option);

                    member_list[member.id] = member;
                });

                // Aktifkan Select2 setelah isi data
                $("#member_id").select2({
                    placeholder: "Pilih Member",
                    allowClear: true
                });
            } catch (error) {
                console.error('Gagal memuat member:', error);
            }
        }
        fetchMember();

        // get data hutang
        async function fetchHutang() {
            try {
                const result = await callAPI({ url: '../api/hutang.php', method: 'GET' });
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = ''; // Clear existing rows

                result.data.forEach(item => {
                    const row = document.createElement('tr');
                    const btnLunas = item.status == "Y" ? '' : `<button class="lunasBtn" data-id="${item.id_hutang}">Lunas</button>`;
                    row.innerHTML = `
                        <td>${item.id_hutang}</td>
                        <td>${item.created_at}</td>
                        <td>${item.nama_user}</td>
                        <td>${formatCurrencyIDR(item.jumlah_hutang)}</td>
                        <td>${item.status == "Y" ? "Lunas" : "Belum Lunas"}</td>
                        <td>
                            <button class="detailBtn" data-id="${item.id_hutang}">Detail</button>
                            <button class="strukBtn" data-id="${item.id_hutang}">Struk</button>
                            ${btnLunas}</td>
                    `;
                    tbody.appendChild(row);
                });
                
                document.addEventListener('click', async function(e) {
                    if (e.target.classList.contains('strukBtn')) {
                        const idPenjualan = e.target.getAttribute('data-id');
                        const strukModal = document.getElementById('StrukModal');
                        const strukContent = document.getElementById('strukContent');
                        strukContent.innerHTML = 'Memuat...';

                        // Ambil detail penjualan
                        try {
                            const result = await callAPI({ url: `../api/hutang.php?id_hutang=${idPenjualan}&action=detail`, method: 'GET' });
                            const trx = result.data;
                            const detail = trx.detail || [];

                            const total_trx = detail.reduce((a,b)=>a+b.jumlah_hutang*b.harga_hutang,0);

                            let html = `
                                <div style="text-align:center;font-weight:bold;font-size:12px;letter-spacing:1px;margin-bottom:2mm;">
                                    TK. SIDODADI KEDURUS
                                </div>
                                <div style="text-align:center;font-size:11px;margin-bottom:1mm;">
                                    Jl. Raya Mastrip No.31, Kedurus, Surabaya.<br>
                                    Telp/WA: 0851-1746-6153<br>
                                    Email: son27business@gmail.com
                                </div>
                                <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                                <div style="font-size:11px;margin-bottom:1mm;text-align:left;">
                                    Tanggal Transaksi: ${new Date().toLocaleDateString() + " " + new Date().toLocaleTimeString().padStart(11, "0").substring(0,8)}<br>
                                    Kasir: ${trx.nama_user}<br>
                                    Member: ${trx.nama_member}
                                </div>
                                <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                                <table style="width:100%;font-size:11px;margin-bottom:2mm;text-align:center;">
                                    <tbody style="border:0">
                                        ${detail.map(item => `
                                            <tr>
                                                <td colspan="2" style="border:0;padding-bottom:0.5mm;text-align:left;">
                                                    <span style="font-weight:bold;">${item.nama_product}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border:0;width:60%;text-align:left;">
                                                    ${item.jumlah_hutang} x ${formatCurrencyIDR(item.harga_hutang)}
                                                </td>
                                                <td style="border:0;width:40%;text-align:right;padding-right:1mm;">
                                                    ${formatCurrencyIDR(item.jumlah_hutang * item.harga_hutang)}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                                <div style="font-size:11px;font-weight:bold;text-align:right;margin-bottom:1mm;">
                                    Total Pembelian: ${formatCurrencyIDR(total_trx)}
                                </div>
                                <div style="font-size:11px;font-weight:bold;text-align:right;margin-bottom:1mm;">
                                    Total Ongkir: ${formatCurrencyIDR(trx.total_ongkir)}
                                </div>
                                <div style="font-size:11px;font-weight:bold;text-align:right;margin-bottom:1mm;">
                                    Pembayaran: ${formatCurrencyIDR(trx.total_pembayaran)}
                                </div>
                                <div style="font-size:11px;font-weight:bold;text-align:right;margin-bottom:2mm;">
                                    Kembalian: ${formatCurrencyIDR(trx.total_pembayaran - total_trx)}
                                </div>
                                <div style="font-size:11px;text-align:center;margin-bottom:1mm;">
                                    Barang yang dibeli tidak dapat dikembalikan<br>
                                    Simpan nota ini sebagai bukti transaksi
                                </div>
                                <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                                <div style="text-align:center;font-size:11px;font-weight:bold;margin-top:2mm;">
                                    TERIMA KASIH ATAS KUNJUNGAN ANDA
                                </div>
                                <div style="height:8mm;"></div>
                            `;
                            strukContent.innerHTML = html;
                            strukModal.style.display = 'flex';
                        } catch (err) {
                            strukContent.innerHTML = 'Gagal memuat struk';
                        }
                    }
                });

                // Tutup modal struk
                document.getElementById('closeStrukModalBtn').onclick = function() {
                    document.getElementById('StrukModal').style.display = 'none';
                };

                // Cetak struk
                document.getElementById('printStrukBtn').onclick = function() {
                    const printContents = document.getElementById('strukContent').innerHTML;
                    const height_content = document.getElementById('strukContent').offsetHeight;
                    const win = window.open('', '', 'width=300,height=400');
                    win.document.write(`
                        <html>
                        <head>
                            <title>Struk Penjualan</title>
                            <style>
                                @media print {
                                    @page { size: 58mm ${height_content}px ; margin: 0; }
                                    body { max-width:58mm; margin:0; }
                                }
                            </style>
                        </head>
                        <body>${printContents}</body>
                        </html>
                    `);
                    win.focus();
                };

                // Add event listener for detail buttons
                document.querySelectorAll('.detailBtn').forEach(button => {
                    button.addEventListener('click', function () {
                        const idHutang = this.getAttribute('data-id');
                        const detailModal = document.getElementById('DetailModal');
                        const detailTableBody = document.getElementById('detailTableBody');

                        id_hutang_modal=idHutang;

                        detailTableBody.innerHTML = ''; // Clear existing rows
                        detailModal.style.display = 'flex';

                        callAPI({ url: `../api/hutang.php?id_hutang=${idHutang}&action=detail`, method: 'GET' })
                            .then(result => {
                                const detailData = result.data
                                detailData.forEach(detail => {
                                    const detailRow = document.createElement('tr');
                                    detailRow.innerHTML = `
                                        <td>${detail.id_produk}</td>
                                        <td>${detail.nama_product}</td>
                                        <td>${detail.jumlah_hutang}</td>
                                        <td>${formatCurrencyIDR(detail.harga_hutang)}</td>
                                    `;
                                    detailTableBody.appendChild(detailRow);

                                    // hide btnbelum lunas
                                    const cancelLunasModalBtn = document.getElementById('cancelLunasModalBtn');
                                    if (detail.status === 'N') {
                                        cancelLunasModalBtn.style.display = 'none';
                                    } else {
                                        cancelLunasModalBtn.style.display = 'inline';
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Gagal memuat detail hutang:', error);
                            });
                    });
                });

                // Add event listener for lunas buttons
                document.querySelectorAll('.lunasBtn').forEach(button => {
                    button.addEventListener('click', function () {
                        const idHutang = this.getAttribute('data-id');
                        if (confirm('Apakah Anda yakin ingin menandai hutang ini sebagai lunas?')) {
                            callAPI({ url: `../api/hutang.php?id_hutang=${idHutang}`, method: 'PUT' })
                                .then(result => {
                                    alert(result.message);
                                    fetchHutang(); // Refresh the hutang data
                                })
                                .catch(error => {
                                    console.error('Gagal menandai hutang sebagai lunas:', error);
                                });
                        }
                    });
                });
            } catch (error) {
                console.error('Gagal memuat hutang:', error);
            }
        }

        fetchHutang();

        // editPriceBtn
        const editPriceBtn = document.getElementById('editPriceBtn');
        editPriceBtn.addEventListener('click', () => {
            edit_price = !edit_price;
            const hargaBeliInputs = table.querySelectorAll('.harga_beli');
            hargaBeliInputs.forEach(input => {
                input.disabled = !edit_price; // Toggle disabled state
            });
        });

        // closeDetailModalBtn
        const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
        closeDetailModalBtn.addEventListener('click', () => {
            const detailModal = document.getElementById('DetailModal');
            detailModal.style.display = 'none';
        });

        // cancelLunasModalBtn
        const cancelLunasModalBtn = document.getElementById('cancelLunasModalBtn');
        cancelLunasModalBtn.addEventListener('click', () => {
            const detailModal = document.getElementById('DetailModal');
            detailModal.style.display = 'none';

            if (confirm('Apakah Anda yakin ingin membatalkan pelunasan hutang ini?')) {
                callAPI({ url: `../api/hutang.php?id_hutang=${id_hutang_modal}&status=N`, method: 'PUT' })
                    .then(result => {
                        alert(result.message);
                        fetchHutang(); // Refresh the hutang data
                    })
                    .catch(error => {
                        console.error('Gagal menandai hutang sebagai lunas:', error);
                    });
            }
        });

        function updateTotal(input) {
            const row = input.closest('tr');
            const hargaBeli = parseFloat(row.querySelector('.harga_beli').value);
            const quantity = parseInt(row.querySelector('.quantity').value);
            const totalCell = row.querySelector('.total');
            totalCell.textContent = formatCurrencyIDR(hargaBeli * quantity);
            updateGrandTotal(); // Update grand total after changing quantity or harga beli
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
                <td>
                    <button type="button" class="removeBtn" onclick="deleteRow(this)" style="background: none; border: none; color: red; cursor: pointer;">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <td>${product.nama_product}</td>
                <td>${product.nama_satuan}</td>
                <td><input type="number" ${edit_price ? '' : 'disabled'} class="harga_beli" value="${product.harga_beli_product}" min="0"></td>
                <td><input type="number" class="quantity" value="1" min="1"></td>
                <td class="total">${formatCurrencyIDR(product.harga_beli_product)}</td>
            `;
            table.appendChild(tr);

            updateGrandTotal(); // Update grand total after adding a new product
            document.getElementById('product_id').value = ''; // Clear the select input after
            document.getElementById('product_id').dispatchEvent(new Event('change')); // Trigger change event for Select2
        });

        // closeModalBtn
        const closeModalBtn = document.getElementById('closeModalBtn');
        closeModalBtn.addEventListener('click', () => {
            const modal = document.getElementById('HutangModal');
            modal.style.display = 'none';
        });

        // saveHutangBtn
        const saveHutangBtn = document.getElementById('saveHutangBtn');
        saveHutangBtn.addEventListener('click', async () => {
            const rows = table.querySelectorAll('tr');
            const hutangData = [];

            rows.forEach(row => {
                const productId = row.getAttribute('data-id');
                const hargaBeliInput = row.querySelector('.harga_beli');
                const quantityInput = row.querySelector('.quantity');
                const quantity = parseInt(quantityInput.value);
                if (quantity > 0) {
                    hutangData.push({
                        product_id: productId,
                        harga_beli: parseFloat(hargaBeliInput.value),
                        quantity: quantity
                    });
                }
            });

            if (hutangData.length === 0) {
                alert('Tidak ada barang yang dihutang.');
                return;
            }

            const memberElement = document.getElementById('member_id');
            const memberId = memberElement ? memberElement.value : '';
            if (!memberId) {
                alert('Silakan pilih member terlebih dahulu.');
                return;
            }

            const total_bayar_value = parseFloat(total_bayar.value) || 0;
            const total_ongkir_value = parseFloat(harga_ongkir.value) || 0;

            const body = {
                hutang: hutangData,
                id_member: memberId,
                total_bayar: total_bayar_value,
                total_ongkir: total_ongkir_value
            }

            try {
                const result = await callAPI({
                    url: '../api/hutang.php',
                    method: 'POST',
                    body
                });
                if (result.status !== 0) {
                    alert(result.message);
                    return;
                }
                fetchHutang(); // Refresh the hutang data
                alert('Hutang berhasil!');
                table.innerHTML = ''; // Clear the table after saving
                const modal = document.getElementById('HutangModal');
                modal.style.display = 'none';
            } catch (error) {
                console.error('Gagal menyimpan hutang:', error);
            }
        });

        // createProductBtn
        const createProductBtn = document.getElementById('createProductBtn');
        createProductBtn.addEventListener('click', () => {
            const modal = document.getElementById('HutangModal');
            modal.style.display = 'flex';
        });

        // Update grand total and total kembalian
        updateGrandTotal = () => {
            let grandTotal = 0;
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const totalCell = row.querySelector('.total');
                if (totalCell) {
                    const totalValue = parseFloat(totalCell.textContent.replace(/[Rp. ]+/g, ""));
                    grandTotal += isNaN(totalValue) ? 0 : totalValue;
                }
            });
            grand_total.value = formatCurrencyIDR(grandTotal);
            updateKembalian();
        };

        updateKembalian = () => {
            const totalBayar = parseFloat(total_bayar.value) || 0;
            const grandTotal = parseFloat(grand_total.value.replace(/[Rp. ]+/g, "")) || 0;
            const hargaOngkir = parseFloat(harga_ongkir.value) || 0;
            const kembalian = totalBayar - grandTotal - hargaOngkir;

            total_kembalian.value = formatCurrencyIDR(kembalian);
        };

        // totalBayar change
        total_bayar.addEventListener('input', updateKembalian);
        harga_ongkir.addEventListener('input', updateKembalian);

        // removeBtn
        deleteRow = (btn) => {
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                updateGrandTotal();
            }
        };

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
            const modal = document.getElementById('HutangModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // keyboard esc
        window.addEventListener('keydown', function (event) {
            const modal = document.getElementById('HutangModal');
            if (event.key === 'Escape') {
                modal.style.display = 'none';
            }
        });
    });
</script>