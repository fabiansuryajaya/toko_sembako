document.addEventListener('DOMContentLoaded', () => {
    $(document).on('select2:open', () => {
        setTimeout(() => {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });

    const role = getRole();
    const role_permission = {
        "pegawai": ["Master", "Product", "Transaksi", "Penjualan", "Hutang", "Logout"]
    }
    
    const menu = {
        "Master" : {
            "Product" : "product",
            "Supplier" : "supplier",
            "Unit" : "unit",
            "User" : "user",
            "Member" : "member"
        },
        "Transaksi" : {
            "Restock" : "restock",
            "Penjualan" : "penjualan",
            "Hutang" : "hutang"
        },
        "Report" : "report",
        "Setting" : "setting",
        "Logout" : "logout"
    }

    const menu_bar = document.getElementById('menu-bar');
    for (const [group, items] of Object.entries(menu)) {
        if (role !== "admin" && !role_permission[role].includes(group)) continue;
        const groupItem = document.createElement('li');
        // Tambahkan ikon Font Awesome di depan nama grup
        if (typeof items === 'object') {
            groupItem.classList.add('menu-group');
                
            groupItem.innerHTML = `<a href="#" class="menu-toggle">
                <span class="menu-group-label">${group}</span>
                <i class="fa fa-chevron-right"></i>
            </a>`;

            const submenu = document.createElement('ul');
            submenu.classList.add('submenu');
            submenu.style.display = 'none';
            for (const [label, page] of Object.entries(items)) {
                if (role !== "admin" && !role_permission[role].includes(label)) continue;
                const item = document.createElement('li');
                item.innerHTML = `<a href="#" id="sidebar-${page}" data-page="${page}">${label}</a>`;
                submenu.appendChild(item);
            }
            groupItem.appendChild(submenu);
        }else {
            const item = document.createElement('a');
            item.href = "#";
            item.id = `sidebar-${items}`;
            item.setAttribute('data-page', items);
            item.textContent = group;
            item.addEventListener('click', (e) => {
                e.preventDefault();
                if (items == null) return;
                loadPage(items);
            });
            //tes
            groupItem.appendChild(item);
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
                // document.querySelectorAll('.menu-group .submenu').forEach(function(otherSubmenu) {
                //     if (otherSubmenu !== submenu) {
                //         otherSubmenu.style.display = 'none';
                //         const otherIcon = otherSubmenu.parentElement.querySelector('.menu-toggle i.fa');
                //         if (otherIcon) {
                //             otherIcon.classList.add('fa-chevron-right');
                //             otherIcon.classList.remove('fa-chevron-down');
                //         }
                //     }
                // });
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
            
            if (page == "logout" || page == null) return;
            screenContent.innerHTML = '';
            if (page) {
                // Simpan halaman yang dimuat ke dalam localStorage
                localStorage.setItem('lastPage', page);
                loadPage(page);
            }
        });
    });

    // last page
    const lastPage = localStorage.getItem('lastPage');
    if (lastPage !== null) {
        const lastPageLink = document.getElementById('sidebar-' + lastPage);
        if (lastPageLink) {
            lastPageLink.click();
        } else {
            loadPage('product');
        }
    }

    // Agar variabel JS bisa diakses antar file, gunakan window sebagai global scope
    function loadPage(page) {
        if (page == "logout"){
            callAPI({
                url: '../api/auth.php',
                body: { logout: '1' },
                method: 'POST'
            }).then(response => {
                window.location.href = '../index.php';
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
});

