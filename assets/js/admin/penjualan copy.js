// on document ready
const role = getRole();

if (role !== 'admin') {
    $('#cancelLunasModalBtn').hide();
}

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
                option.textContent = product.nama_product + " - " + product.nama_satuan + " - " + formatCurrencyIDR(product.harga_jual_product);
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
    document.getElementById('filter_btn').addEventListener('click', fetchHutang);

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
                    <div style="text-align:center;font-weight:bold;font-size:16px;letter-spacing:1px;margin-bottom:2mm;">
                        TK. SIDODADI KEDURUS
                    </div>
                    <div style="text-align:center;font-size:13px;margin-bottom:1mm;">
                        Jl. Raya Mastrip No.31, Kedurus, Surabaya.<br>
                        Telp/WA: 0851-1746-6153<br>
                        Email: son27business@gmail.com
                    </div>
                    <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                    <div style="font-size:13px;margin-bottom:1mm;text-align:left;">
                        Tanggal: ${new Date().toLocaleDateString()}<br>
                        Kasir: ${trx.nama_user}<br>
                        Member: ${trx.nama_member}
                    </div>
                    <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                    <table style="width:100%;font-size:14px;margin-bottom:2mm;text-align:center;margin-top:0px">
                        <tbody style="border:0;">
                            ${detail.map(item => `
                                <tr>
                                    <td colspan="2" style="border:0;padding:0;padding-bottom:0.5mm;text-align:left;">
                                        <span style="font-weight:bold;">${item.nama_product}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0;padding:0;width:60%;text-align:left;">
                                        ${item.jumlah_hutang} ${item.nama_satuan} x ${formatCurrencyIDR(item.harga_hutang)}
                                    </td>
                                    <td style="border:0;padding:0;width:40%;text-align:right;padding-right:2mm;">
                                        ${formatCurrencyIDR(item.jumlah_hutang * item.harga_hutang)}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <hr style="border:0;border-top:2px dashed #333;margin:2mm 0;">
                    <table style="width:100%;font-size:16px;margin-bottom:2mm;text-align:right;">
                        <tr>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Total:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">${formatCurrencyIDR(total_trx)}</td>
                        </tr>
                        <tr>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Total Ongkir:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;"></td>
                        </tr>
                        <tr>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Pembayaran:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;"></td>
                        </tr>
                        <tr>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Kembalian:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;"></td>
                        </tr>
                    </table>
                    <div style="font-size:14px;text-align:center;margin-bottom:1mm;">
                        Barang yang dibeli tidak dapat dikembalikan<br>
                        Simpan nota ini sebagai bukti transaksi
                    </div>
                    <hr style="border:0;border-top:2px dashed #333;margin:2mm 0;">
                    <div style="text-align:center;font-size:14px;font-weight:bold;margin-top:2mm;">
                        TERIMA KASIH ATAS PESANANNYA! DITUNGGU ORDER BERIKUTNYA
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

    // get data hutang
    async function fetchHutang() {
        try {
            const params = {};
            const start_date = document.getElementById('from_date').value;
            const to_date = document.getElementById('to_date').value;
            if (start_date) params.from_date = start_date;
            if (to_date) params.to_date = to_date;
            const queryParams = new URLSearchParams(params).toString();

            const result = await callAPI({ url: '../api/hutang.php?' + queryParams, method: 'GET' });
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = ''; // Clear existing rows

            result.data.forEach(item => {
                const row = document.createElement('tr');
                const btnLunas = item.status == "Y"  || role !== 'admin' ? '' : `<button class="lunasBtn" data-id="${item.id_hutang}">Lunas</button>`;
                row.innerHTML = `
                    <td>${item.id_hutang}</td>
                    <td>${item.created_at}</td>
                    <td>${item.nama_member}</td>
                    <td>${formatCurrencyIDR(item.jumlah_hutang)}</td>
                    <td>${item.status == "Y" ? "Lunas" : "Belum Lunas"}</td>
                    <td>
                        <button class="detailBtn" data-id="${item.id_hutang}">Detail</button>
                        <button class="strukBtn" data-id="${item.id_hutang}">Struk</button>
                        <button class="editBtn" data-id="${item.id_hutang}">Edit</button>
                        ${btnLunas}</td>
                `;
                tbody.appendChild(row);
            });
            
            // Tutup modal struk
            document.getElementById('closeStrukModalBtn').onclick = function() {
                document.getElementById('StrukModal').style.display = 'none';
            };

            // Cetak struk
            document.getElementById('printStrukBtn').onclick = function() {
                const printContents = document.getElementById('strukContent').innerHTML;
                const win = window.open('', '', 'width=400,height=600');
                win.document.write(`
                    <html>
                    <head>
                        <title>Struk Hutang</title>
                        <style>
                            @media print {
                                /* Mengatur ukuran kertas menjadi 80mm dengan tinggi otomatis */
                                @page {
                                    size: 80mm auto;
                                    margin: 0;
                                    padding:0;
                                    font-family:calibri;
                                }

                                /* Memastikan body memiliki lebar yang sama dengan kertas */
                                body {
                                    width: 80mm;
                                    margin: 0;
                                }

                                /* Mengatur ulang margin pada setiap elemen untuk menghindari whitespace */
                                * {
                                    margin: 0;
                                    padding: 0;
                                }
                            }
                        </style>
                    </head>
                    <body>${printContents}</body>
                    </html>
                `);
                win.document.close();
                win.focus();
                setTimeout(function() {
                    win.print();
                }, 500);
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
                            const data = result.data;
                            const detailData = data.detail;

                            detailData.forEach(detail => {
                                const detailRow = document.createElement('tr');
                                detailRow.innerHTML = `
                                    <td>${detail.id_produk}</td>
                                    <td>${detail.nama_product}</td>
                                    <td>${detail.jumlah_hutang} ${detail.nama_satuan}</td>
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

            // editBtn
            document.querySelectorAll('.editBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const idHutang = this.getAttribute('data-id');
                    document.getElementById('edit_penjualan_id').value = idHutang;

                    table.innerHTML = '';
                    try {
                        // Fetch detail hutang
                        const params = { id_hutang: idHutang, action: 'detail' };
                        const queryParams = new URLSearchParams(params).toString();
                        callAPI({ url: `../api/hutang.php?${queryParams}`, method: 'GET' })
                            .then(result => {
                                const detailData = result.data.detail;
                                // show in trx table
                                detailData.forEach(detail => {
                                    const detailRow = document.createElement('tr');
                                    // add to table
                                    detailRow.setAttribute('data-id', detail.id_produk);
                                    detailRow.innerHTML = `
                                        <td>
                                            <button type="button" class="removeBtn" onclick="deleteRow(this)" style="background: none; border: none; color: red; cursor: pointer;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>${detail.nama_product}</td>
                                        <td>${detail.nama_satuan}</td>
                                        <td><input type="number" ${edit_price ? '' : 'disabled'} class="harga_beli" value="${detail.harga_hutang}" min="0"></td>
                                        <td><input type="number" class="quantity" value="${detail.jumlah_hutang}" min="1"></td>
                                        <td class="total">${formatCurrencyIDR(detail.harga_hutang * detail.jumlah_hutang)}</td>
                                    `;
                                    table.appendChild(detailRow);
                                });
                                document.getElementById('total_bayar').value  = (result.data.total_pembayaran);
                                document.getElementById('harga_ongkir').value = (result.data.total_ongkir);
                                document.getElementById('member_id').value    = (result.data.id_member);
                                document.getElementById('member_id').dispatchEvent(new Event('change')); // Trigger change event for Select2
                                updateGrandTotal(); // Update grand total after adding a new product

                                const modal = document.getElementById('HutangModal');
                                modal.style.display = 'flex';
                            })
                            .catch(error => {
                                console.error('Gagal memuat detail hutang:', error);
                            });
                    } catch (err) {
                        console.error(err);
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
            <td><input type="number" ${edit_price ? '' : 'disabled'} class="harga_beli" value="${product.harga_jual_product}" min="0"></td>
            <td><input type="number" class="quantity" value="1" min="1"></td>
            <td class="total">${formatCurrencyIDR(product.harga_jual_product)}</td>
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

        let method = 'POST';
        const edit_penjualan_id = document.getElementById('edit_penjualan_id').value;
        if (edit_penjualan_id) {
            body.id_hutang = edit_penjualan_id;
            body.action = 'edit';
            method = 'PUT';
        }

        try {
            const result = await callAPI({
                url: '../api/hutang.php',
                method: method,
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