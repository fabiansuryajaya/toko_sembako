$(document).ready(function () {
    
    const role = getRole();
    if (role !== 'admin') $('#editPriceBtn').hide();

    // init
    const start_date = document.getElementById('from_date');
    const to_date    = document.getElementById('to_date');
    edit_price       = false;
    start_date.value = new Date().toISOString().split('T')[0]; // Set to today
    to_date.value    = new Date().toISOString().split('T')[0]; // Set to today

    const grand_total = document.getElementById('grand_total');
    const total_bayar = document.getElementById('total_bayar');
    const total_kembalian = document.getElementById('total_kembalian');

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

    document.getElementById('filter_btn').addEventListener('click', fetchPenjualan);

    // Handle tombol Struk
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('strukBtn')) {
            const idPenjualan = e.target.getAttribute('data-id');
            const strukModal = document.getElementById('StrukModal');
            const strukContent = document.getElementById('strukContent');
            strukContent.innerHTML = 'Memuat...';

            // Ambil detail penjualan
            try {
                const result = await callAPI({ url: `../api/penjualan.php?id_penjualan=${idPenjualan}&action=detail`, method: 'GET' });
                const trx = result.data;
                const detail = trx.detail || [];

                const total_trx = detail.reduce((a,b)=>a+b.jumlah_penjualan*b.harga_penjualan,0);

                // let html = `
                //     <div style="text-align:center;font-weight:bold;font-size:16px;letter-spacing:1px;margin-bottom:2mm;">
                //         TK. SIDODADI KEDURUS
                //     </div>
                //     <div style="text-align:center;font-size:13px;margin-bottom:1mm;">
                //         Jl. Raya Mastrip No.31, Kedurus, Surabaya.<br>
                //         Telp/WA: 0851-1746-6153<br>
                //         Email: son27business@gmail.com
                //     </div>
                //     <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                //     <div style="font-size:13px;margin-bottom:1mm;text-align:left;">
                //         Tanggal: ${new Date().toLocaleDateString() + " " + new Date().toLocaleTimeString().padStart(11, "0").substring(0,5)}<br>
                //         Kasir: ${trx.nama_user}
                //     </div>
                //     <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                //     <table style="width:100%;font-size:14px;margin-bottom:2mm;text-align:center;margin-top:0px">
                //         <tbody style="border:0;">
                //             ${detail.map(item => `
                //                 <tr>
                //                     <td colspan="2" style="border:0;padding:0;padding-bottom:0.5mm;text-align:left;">
                //                         <span style="font-weight:bold;">${item.nama_product}</span>
                //                     </td>
                //                 </tr>
                //                 <tr>
                //                     <td style="border:0;padding:0;width:60%;text-align:left;">
                //                         ${item.jumlah_penjualan} x ${formatCurrencyIDR(item.harga_penjualan)}
                //                     </td>
                //                     <td style="border:0;padding:0;width:40%;text-align:right;padding-right:2mm;">
                //                         ${formatCurrencyIDR(item.jumlah_penjualan * item.harga_penjualan)}
                //                     </td>
                //                 </tr>
                //             `).join('')}
                //         </tbody>
                //     </table>
                //     <hr style="border:0;border-top:2px dashed #333;margin:2mm 0;">
                //     <div style="font-size:13px;font-weight:bold;text-align:right;margin-bottom:1mm;padding-right:2mm;">
                //         Total: ${formatCurrencyIDR(total_trx)}
                //     </div>
                //     <div style="font-size:13px;font-weight:bold;text-align:right;margin-bottom:1mm;padding-right:2mm;">
                //         Pembayaran: ${formatCurrencyIDR(trx.total_pembayaran)}
                //     </div>
                //     <div style="font-size:13px;font-weight:bold;text-align:right;margin-bottom:2mm;padding-right:2mm;">
                //         Kembalian: ${formatCurrencyIDR(trx.total_pembayaran - total_trx)}
                //     </div>
                //     <div style="font-size:14px;text-align:center;margin-bottom:1mm;">
                //         Barang yang dibeli tidak dapat dikembalikan<br>
                //         Simpan nota ini sebagai bukti transaksi
                //     </div>
                //     <hr style="border:0;border-top:2px dashed #333;margin:2mm 0;">
                //     <div style="text-align:center;font-size:14px;font-weight:bold;margin-top:2mm;">
                //         TERIMA KASIH ATAS KUNJUNGAN ANDA
                //     </div>
                //     <div style="height:8mm;"></div>
                // `;

                const total_bayar     = (trx.total_pembayaran != 0) ? formatCurrencyIDR(trx.total_pembayaran)             : "";
                const total_kembalian = (trx.total_pembayaran != 0) ? formatCurrencyIDR(trx.total_pembayaran - total_trx) : "";
                const nama_pembeli = trx.nama_pembeli ? `<br>Pembeli: ${trx.nama_pembeli}<br>` : '';

                let html = `
                    <div style="text-align:center;font-weight:bold;font-size:15px;letter-spacing:1px;margin-bottom:2mm;">
                        TK. SIDODADI SURABAYA
                    </div>
                    <div style="text-align:center;font-size:15px;margin-bottom:1mm;">
                        Jl. Raya Mastrip No.31, Kedurus, Surabaya.<br>
                        Telp/WA: 0851-1746-6153<br>
                    </div>
                    <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                    <div style="font-size:16px;margin-bottom:1mm;text-align:left;">
                        Tanggal: ${new Date().toLocaleDateString()}<br>
                        Kasir: ${trx.nama_user}
                        ${nama_pembeli}
                    </div>
                    <hr style="border:0;border-top:1px dashed #333;margin:2mm 0;">
                    <table style="width:100%;font-size:16px;margin-bottom:2mm;text-align:center;margin-top:0px">
                        <tbody style="border:0;">
                            ${detail.map(item => `
                                <tr>
                                    <td colspan="2" style="border:0;padding:0;padding-bottom:0.5mm;text-align:left;">
                                        <span style="font-weight:bold;">${item.nama_product}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0;padding:0;width:60%;text-align:left;">
                                        ${item.jumlah_penjualan} ${item.nama_satuan} x ${formatCurrencyIDR(item.harga_penjualan)}
                                    </td>
                                    <td style="border:0;padding:0;width:40%;text-align:right;padding-right:5mm;">
                                        ${formatCurrencyIDR(item.jumlah_penjualan * item.harga_penjualan)}
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
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Pembayaran:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">${total_bayar}</td>
                        </tr>
                        <tr>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">Kembalian:</td>
                            <td style="border:0;font-weight:bold;padding-right:5mm;">${total_kembalian}</td>
                        </tr>
                    </table>
                    <div style="font-size:14px;text-align:center;margin-bottom:1mm;">
                        Barang yang dibeli tidak dapat dikembalikan
                    </div>
                    <hr style="border:0;border-top:2px dashed #333;margin:2mm 0;">
                    <div style="text-align:center;font-size:14px;font-weight:bold;margin-top:2mm;">
                        TERIMA KASIH ATAS KUNJUNGAN ANDA
                    </div>
                    <div style="height:8mm;"></div>
                `;
                strukContent.innerHTML = html;
                strukModal.style.display = 'flex';
            } catch (err) {
                strukContent.innerHTML = 'Gagal memuat struk';
            }
        }else if (e.target.classList.contains('editBtn')) {
            const idPenjualan = e.target.getAttribute('data-id');
            document.getElementById('edit_penjualan_id').value = idPenjualan;

            table.innerHTML = '';
            try {
                // Fetch detail penjualan
                const params = { id_penjualan: idPenjualan, action: 'detail' };
                const queryParams = new URLSearchParams(params).toString();
                callAPI({ url: `../api/penjualan.php?${queryParams}`, method: 'GET' })
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
                                <td><input type="number" ${edit_price ? '' : 'disabled'} class="harga_beli" value="${detail.harga_penjualan}" min="0"></td>
                                <td><input type="number" class="quantity" value="${detail.jumlah_penjualan}" min="1"></td>
                                <td class="total">${formatCurrencyIDR(detail.harga_penjualan * detail.jumlah_penjualan)}</td>
                            `;
                            table.appendChild(detailRow);
                        });
                        document.getElementById('total_bayar').value = (result.data.total_pembayaran);
                        updateGrandTotal(); // Update grand total after adding a new product

                        const modal = document.getElementById('PenjualanModal');
                        modal.style.display = 'flex';
                    })
                    .catch(error => {
                        console.error('Gagal memuat detail penjualan:', error);
                    });
            } catch (err) {
                console.error(err);
            }
        }
    });

    // get data penjualan
    async function fetchPenjualan() {
        try {
            const params = {};
            const start_date = document.getElementById('from_date').value;
            const to_date = document.getElementById('to_date').value;
            if (start_date) params.from_date = start_date;
            if (to_date) params.to_date = to_date;
            const queryParams = new URLSearchParams(params).toString();

            const result = await callAPI({ url: '../api/penjualan.php?' + queryParams, method: 'GET' });
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = ''; // Clear existing rows

            result.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id_penjualan}</td>
                    <td>${item.created_at}</td>
                    <td>${item.jumlah_penjualan}</td>
                    <td>${item.nama_user}</td>
                    <td>
                        <button class="detailBtn" data-id="${item.id_penjualan}">Detail</button>
                        <button class="strukBtn"  data-id="${item.id_penjualan}">Struk</button>
                        <button class="editBtn"   data-id="${item.id_penjualan}">Edit</button>
                    </td>
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

                // Buka jendela baru dengan pengaturan yang lebih sesuai untuk printer
                // 'width' dan 'height' pada window.open adalah dalam pixel.
                // Atur ukuran jendela yang cukup lebar untuk menampilkan konten
                const win = window.open('', '_blank');

                win.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Struk Penjualan</title>
                        <style>
                            /* Gaya untuk mode tampilan */
                            body {
                                font-size: 16px;
                            }

                            /* Pengaturan utama untuk cetak */
                            @media print {
                                /* Mengatur ukuran kertas menjadi 80mm dengan tinggi otomatis */
                                @page {
                                    size: 80mm auto;
                                    margin: 0mm;
                                    padding:0;
                                    font-family:calibri;
                                }

                                /* Memastikan body memiliki lebar yang sama dengan kertas */
                                body {
                                    width: 80mm;
                                    margin: 0mm;
                                    font-family:calibri;
                                }

                                /* Mengatur ulang margin pada setiap elemen untuk menghindari whitespace */
                                * {
                                    margin: 0mm;
                                    padding: 0;
                                    font-family:calibri;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                    </html>
                `);

                win.document.close();
                win.focus();

                // Gunakan setTimeout untuk memberi waktu browser merender konten
                // sebelum memanggil fungsi cetak.
                setTimeout(function() {
                    win.print();
                }, 500); // Penundaan 500ms (0.5 detik)
            };

            // Add event listener for detail buttons
            document.querySelectorAll('.detailBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const idPenjualan = this.getAttribute('data-id');
                    const detailModal = document.getElementById('DetailModal');
                    const detailTableBody = document.getElementById('detailTableBody');

                    detailTableBody.innerHTML = ''; // Clear existing rows
                    detailModal.style.display = 'flex';
                    const params = { id_penjualan: idPenjualan, action: 'detail' };
                
                    const queryParams = new URLSearchParams(params).toString();

                    // Fetch detail penjualan
                    callAPI({ url: `../api/penjualan.php?${queryParams}`, method: 'GET' })
                        .then(result => {
                            const detailData = result.data.detail;
                            detailData.forEach(detail => {
                                const detailRow = document.createElement('tr');
                                detailRow.innerHTML = `
                                    <td>${detail.id_produk}</td>
                                    <td>${detail.nama_product}</td>
                                    <td>${detail.nama_supplier}</td>
                                    <td>${detail.jumlah_penjualan} ${detail.nama_satuan}</td>
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

    async function fetchUsersPegawai() {
        try {
            const result = await callAPI({ url: '../api/user.php', method: 'GET' });
            const userSelect = document.getElementById('user_id');
            userSelect.innerHTML = '<option value="">Pilih User</option>';
            result.data.data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.username;
                userSelect.appendChild(option);
            });
            // Aktifkan Select2 setelah isi data
            $("#user_id").select2({
                placeholder: "Pilih User",
                allowClear: true
            });

            const username = getUsername();
            if (role !== 'admin' && username) {
                $('#user_id').val(result.data.data.find(u => u.username === username).id).trigger('change');
                $('#user_id').prop('disabled', true);
            }
        } catch (error) {
            console.error('Gagal memuat user:', error);
        }
    }
    fetchUsersPegawai();

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
        // append to first child
        table.insertBefore(tr, table.firstChild);
        updateGrandTotal(); // Update grand total after adding a new product
        document.getElementById('product_id').value = ''; // Clear the select input after
        document.getElementById('product_id').dispatchEvent(new Event('change')); // Trigger change event for Select2
    });

    // editPriceBtn
    const editPriceBtn = document.getElementById('editPriceBtn');
    editPriceBtn.addEventListener('click', () => {
        edit_price = !edit_price;
        const hargaBeliInputs = table.querySelectorAll('.harga_beli');
        hargaBeliInputs.forEach(input => {
            input.disabled = !edit_price; // Toggle disabled state
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
        let total_trx = 0;

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
                total_trx += parseFloat(hargaBeliInput.value) * quantity;
            }
        });

        if (penjualanData.length === 0) {
            alert('Tidak ada barang yang dipenjualan.');
            return;
        }

        const total_bayar = parseFloat(document.getElementById('total_bayar').value) || 0;

        // if (total_bayar < total_trx) {
        //     alert('Total bayar tidak boleh kurang dari total transaksi.');
        //     return;
        // }

        const body = {
            penjualan: penjualanData,
            total_bayar: total_bayar,
            kasir_id: $('#user_id').val(),
            nama_pembeli: document.getElementById('customer_name').value
        }
        const edit_penjualan_id = document.getElementById('edit_penjualan_id').value;
        if (edit_penjualan_id != '') body.edit_penjualan_id = edit_penjualan_id;

        let method = 'POST';
        if (edit_penjualan_id != '') method = 'PUT';

        try {
            const result = await callAPI({
                url: '../api/penjualan.php',
                method,
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
        document.getElementById('edit_penjualan_id').value = '';
        document.getElementById('user_id').value = '';
        document.getElementById('user_id').dispatchEvent(new Event('change'));

        document.getElementById('customer_name').value = '';
        document.getElementById('total_bayar').value = '';
        document.getElementById('grand_total').value = '';
        document.getElementById('total_kembalian').value = '';
        table.innerHTML = '';
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
        const kembalian = totalBayar - grandTotal;
        
        total_kembalian.value = formatCurrencyIDR(kembalian);
    };

    // totalBayar change
    total_bayar.addEventListener('input', updateKembalian);

    table.addEventListener('input', function (e) {
        if (e.target && e.target.classList.contains('quantity')) {
            updateTotal(e.target);
        }
        if (e.target && e.target.classList.contains('harga_beli')) {
            updateTotal(e.target);
        }
    });

    // removeBtn
    deleteRow = (btn) => {
        const row = btn.closest('tr');
        if (row) {
            row.remove();
            updateGrandTotal();
        }
    };

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