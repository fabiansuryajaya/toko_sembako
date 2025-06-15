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
                    <li><a href="#" data-page="product">Product</a></li> 
                    <li><a href="#" data-page="supplier">Supplier</a></li> 
                    <li><a href="#" data-page="unit">Unit</a></li> 
                    <li><a href="#" data-page="user">User</a></li>
                    <li><a href="#" data-page="setting  ">Setting </a></li>
                </ul>
            </nav>
        </aside>

        <!-- Screen page -->
        <main class="screen" id="screen-content">
            <!-- Konten dinamis akan muncul di sini -->
            <h1>Selamat datang di Dashboard Admin</h1>
        </main>
    </div>

    <script src="../assets/js/global.js"></script>
    <script src="../assets/js/admin/dashboard.js"></script>
</body>

</html>