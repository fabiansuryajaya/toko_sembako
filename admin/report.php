<div class="page product-page">
    <h1>Report Penjualan</h1>

    <div class="filter-container">
        <div class="filter">
            <label for="from_date">Dari :</label>
            <input type="date" id="from_date" name="from_date">

            <label for="to_date">Sampai :</label>
            <input type="date" id="to_date" name="to_date">

            <!-- product -->
            <label for="product_id">Nama Barang:</label>
            <select id="product_id" name="product_id" style="width: 200px;" multiple>
                <option value="">Semua</option>
            </select>
        </div>
        <!-- export excel -->
        <div style="margin-left: auto;">
            <button id="export_excel_btn">Export to Excel</button>
            <button id="filter_btn">Filter</button>
        </div>
    </div>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah Penjualan</th>
                <th>Satuan Produk</th>
                <th>Harga Produk</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
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
        let edit_price   = false; // Flag for edit price mode
        start_date.value = new Date().toISOString().split('T')[0]; // Set to today
        to_date.value    = new Date().toISOString().split('T')[0]; // Set to today

        const product_list = {};

        async function fetchProduct() {
            try {
                const result = await callAPI({ url: '../api/product.php', method: 'GET' });
                const productSelect = document.getElementById('product_id');
                productSelect.innerHTML = '';
                const optionDefault = document.createElement('option');
                optionDefault.value = '';
                optionDefault.textContent = 'Semua';
                productSelect.appendChild(optionDefault);
                result.data.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id_product;
                    option.textContent = product.nama_product;
                    productSelect.appendChild(option);

                    product_list[product.id_product] = product;
                });

                // Initialize Select2 for product select
                $("#product_id").select2({
                    placeholder: "Pilih Barang",
                    allowClear: true
                });
            } catch (error) {
                console.error('Gagal memuat barang:', error);
            }
        }
        fetchProduct();

        const reportTable = document.querySelector('.product-page tbody');
        async function fetchReport() {
            try {
                const params = new URLSearchParams();
                if (start_date.value) params.append('from_date', start_date.value);
                if (to_date.value)    params.append('to_date', to_date.value);
                if (document.getElementById('product_id').value) 
                    params.append('product_id', $("#product_id").val());
                
                let grand_total = 0;
                const result = await callAPI({ url: '../api/report.php?' + params.toString(), method: 'GET' });
                reportTable.innerHTML = '';
                result.data.forEach(report => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${report.nama_product}</td>
                        <td>${report.total_jumlah}</td>
                        <td>${report.nama_satuan}</td>
                        <td>${formatCurrencyIDR(report.harga_penjualan)}</td>
                        <td>${formatCurrencyIDR(report.total_pembayaran)}</td>
                    `;
                    grand_total += parseFloat(report.total_pembayaran);
                    reportTable.appendChild(tr);
                });

                // Tambahkan baris untuk grand total
                const trTotal = document.createElement('tr');
                trTotal.innerHTML = `
                    <td colspan="4" style="text-align: right; font-weight: bold;">Grand Total:</td>
                    <td style="font-weight: bold;">${formatCurrencyIDR(grand_total)}</td>
                `;
                reportTable.appendChild(trTotal);
            } catch (error) {
                console.error('Gagal memuat laporan:', error);
            }
        }

        document.getElementById('filter_btn').addEventListener('click', fetchReport);

        // Export to Excel
        document.getElementById('export_excel_btn').addEventListener('click', function() {
            // export reportTable
            let tableHTML = '<table border="1"><tr><th>Nama Produk</th><th>Jumlah Penjualan</th><th>Satuan Produk</th><th>Harga Produk</th><th>Subtotal</th></tr>';
            reportTable.querySelectorAll('tr').forEach(row => {
                tableHTML += '<tr>' + row.innerHTML + '</tr>';
            });
            tableHTML += '</table>';
            const blob = new Blob([tableHTML], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'report.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
        // Initial fetch
        fetchReport();
    });
</script>