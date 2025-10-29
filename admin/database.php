<!-- Buatkan halaman untuk export dan import DB -->
<div class="page product-page">
    <h1>Database Management</h1>
    <div class="import-export-container" style="max-width:400px;margin:32px auto;padding:24px;border:1px solid #ddd;border-radius:8px;background:#fafafa;">
        <form id="importForm" enctype="multipart/form-data" style="margin-bottom:24px;">
            <label for="importFile" style="font-weight:bold;">Import Database (.sql/.zip):</label>
            <input type="file" name="importFile" id="importFile" accept=".sql,.zip" required style="margin:8px 0 16px 0;">
            <button type="submit" style="background:#2196f3;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;">Import</button>
        </form>
        <button id="exportBtn" style="background:#4caf50;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;width:100%;">Export Database</button>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#exportBtn').on('click', function() {
        window.open('../api/export_db.php', '_blank');
    });

    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        var fileInput = document.getElementById('importFile');
        var file = fileInput.files[0];
        if (!file) {
            alert('Pilih file terlebih dahulu!');
            return;
        }
        var formData = new FormData();
        formData.append('importFile', file);

        $.ajax({
            url: '../api/import_db.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Database imported successfully!');
            },
            error: function(xhr, status, error) {
                alert('Error importing database: ' + error);
            }
        });
    });
});
</script>

<script>
    // You can add JavaScript here if needed for additional functionality
    $(document).ready(function() {
        function export_db() {
            // open new tab
            window.open('../api/export_db.php', '_blank');
        }

        function import_db() {
            var fileInput = document.getElementById('importFile');
            var file = fileInput.files[0];
            var formData = new FormData();
            formData.append('importFile', file);

            $.ajax({
                url: '../api/import_db.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Database imported successfully!');
                },
                error: function(xhr, status, error) {
                    alert('Error importing database: ' + error);
                }
            });
        }
    });
</script>