<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css" />

    <!-- Select2 CSS -->
    <link href="../assets/css/library/select2.min.css" rel="stylesheet" />
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="../assets/js/library/select2.min.js"></script>
</head>

<body>
    
    <div class="dashboard">
        <!-- Sidebar -->
       <aside class="sidebar"> 
            <h2>Menu</h2>
            <nav>
                <ul id="menu-bar">
                </ul>
            </nav>
        </aside>


        <!-- Screen page -->
        <main class="screen" id="screen-content">
            <!-- Konten dinamis akan muncul di sini -->
            <h1>Selamat datang di Dashboard Admin</h1>
        </main>
    </div>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/global.js"></script>
    <script src="../assets/js/admin/dashboard.js"></script>
</body>

</html>

<script>
    // document ready
    $(document).ready(function() {
        const menu = {
            "Master" : {
                "Product" : "product",
                "Supplier" : "supplier",
                "Unit" : "unit",
                "User" : "user"
            },
            "Transaksi" : {
                "Restock" : "restock",
                "Penjualan" : "penjualan",
                "Hutang" : "hutang"
            },
            "Setting" : "setting",
            "Logout" : "logout"
        }

        const menu_bar = document.getElementById('menu-bar');
        for (const [group, items] of Object.entries(menu)) {
            const groupItem = document.createElement('li');
            groupItem.classList.add('menu-group');
            groupItem.innerHTML = `<a href="#" class="menu-toggle">${group}</a>`;
            // Buat submenu hanya jika items adalah object (bukan string)
            if (typeof items === 'object') {
                const submenu = document.createElement('ul');
                submenu.classList.add('submenu');
                submenu.style.display = 'none'; // Sembunyikan default
                for (const [label, page] of Object.entries(items)) {
                    const item = document.createElement('li');
                    item.innerHTML = `<a href="#" data-page="${page}">${label}</a>`;
                    submenu.appendChild(item);
                }
                groupItem.appendChild(submenu);
            }
            menu_bar.appendChild(groupItem);
        }

        // Toggle submenu saat menu group diklik
        document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.parentElement.querySelector('.submenu');
                if (submenu) {
                    submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
                }
            });
        });
    });
</script>

