<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css" />



</head>

<body>
    
    <div class="dashboard">
        <!-- Sidebar -->
       <aside class="sidebar"> 
            <h2>Menu</h2>
            <nav>
                <ul>
                    <!-- Master -->
                    <li class="menu-group">
                        <a href="#" class="menu-toggle">Master</a>
                        <ul class="submenu">
                            <li><a href="#" data-page="product">Product</a></li>
                            <li><a href="#" data-page="supplier">Supplier</a></li>
                            <li><a href="#" data-page="unit">Satuan</a></li>
                            <li><a href="#" data-page="user">User</a></li>
                        </ul>
                    </li>

                    <!-- Transaksi -->
                    <li class="menu-group">
                        <a href="#" class="menu-toggle">Transaksi</a>
                        <ul class="submenu">
                            <li><a href="#" data-page="restock">Restock</a></li>
                            <li><a href="#" data-page="penjualan">Penjualan</a></li>
                            <li><a href="#" data-page="hutang">Hutang</a></li>
                        </ul>
                    </li>

                    <!-- Lainnya -->
                    <li class="menu-group">
                        <a href="#" class="menu-toggle">Lainnya</a>
                        <ul class="submenu">
                            <li><a href="#" data-page="setting">Setting</a></li>
                            <li><a href="#" data-page="logout">Logout</a></li>
                        </ul>
                    </li>
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

<style>
    .sidebar {
        width: 200px;
        background-color: #2c2c2c;
        color: white;
        padding: 20px;
        font-family: sans-serif;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 8px;
        border-radius: 4px;
    }

    .sidebar .menu-toggle {
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
    }

    .sidebar .submenu {
        display: none;
        margin-left: 10px;
        margin-top: 5px;
    }

    .sidebar .menu-group.open .submenu {
        display: block;
    }

</style>
<script>
    document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });
</script>

