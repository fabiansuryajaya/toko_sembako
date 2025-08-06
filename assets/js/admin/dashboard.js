document.addEventListener('DOMContentLoaded', () => {
    // check local_storage for user data
    const user_role = localStorage.getItem('user_role');
    if (!user_role || user_role !== 'admin') {
        alert('Anda tidak memiliki akses ke halaman ini.');
        window.location.href = '../index.php';
        return;
    }

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
        // Tambahkan ikon Font Awesome di depan nama grup
        groupItem.innerHTML = `<a href="#" class="menu-toggle">
            <span class="menu-group-label">${group}</span>
            <i class="fa fa-chevron-right"></i>
        </a>`;
        if (typeof items === 'object') {
            const submenu = document.createElement('ul');
            submenu.classList.add('submenu');
            submenu.style.display = 'none';
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
            const icon = this.querySelector('i.fa');
            if (submenu) {
                // Tutup semua submenu & reset ikon
                document.querySelectorAll('.menu-group .submenu').forEach(function(otherSubmenu) {
                    if (otherSubmenu !== submenu) {
                        otherSubmenu.style.display = 'none';
                        const otherIcon = otherSubmenu.parentElement.querySelector('.menu-toggle i.fa');
                        if (otherIcon) {
                            otherIcon.classList.add('fa-chevron-right');
                            otherIcon.classList.remove('fa-chevron-down');
                        }
                    }
                });
                // Toggle submenu yang diklik
                const isOpen = submenu.style.display !== 'none';
                submenu.style.display = isOpen ? 'none' : 'block';
                if (icon) {
                    icon.classList.toggle('fa-chevron-right', isOpen);
                    icon.classList.toggle('fa-chevron-down', !isOpen);
                }
            }
        });
    });

    const links = document.querySelectorAll('.sidebar a');
    const screenContent = document.getElementById('screen-content');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            links.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            const page = link.getAttribute('data-page');
            if (page) {
                loadPage(page);
            }
        });
    });

    // Agar variabel JS bisa diakses antar file, gunakan window sebagai global scope
    function loadPage(page) {
        if (page == "logout"){
            callAPI({
                url: '../api/auth.php',
                body: { logout: '1' },
                method: 'POST'
            }).then(response => {
                if (response.status === '200') {
                    window.location.href = '../index.php';
                } else {
                    alert('Gagal logout: ' + response.message);
                }
            });
            return;
        }
        fetch(page + '.php')
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat halaman: ' + page);
                return response.text();
            })
            .then(html => {
                screenContent.innerHTML = html;

                // Jalankan ulang script JS yang ada di halaman yang dimuat
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                tempDiv.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        // Eksekusi script di global scope agar variabelnya global
                        newScript.textContent = `(function(){ ${oldScript.textContent} }).call(window);`;
                    }
                    document.body.appendChild(newScript);
                });
            })
            .catch(err => {
                screenContent.innerHTML = '<p>Gagal memuat halaman.</p>';
                console.error(err);
            });
    }

    // Load default page
    loadPage('product');
});

