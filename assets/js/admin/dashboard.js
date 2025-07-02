document.addEventListener('DOMContentLoaded', () => {
    // check local_storage for user data
    const user_role = localStorage.getItem('user_role');
    if (!user_role || user_role !== 'admin') {
        alert('Anda tidak memiliki akses ke halaman ini.');
        window.location.href = '../index.php';
        return;
    }

    const links = document.querySelectorAll('.sidebar a');
    const screenContent = document.getElementById('screen-content');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            links.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            const page = link.getAttribute('data-page');
            loadPage(page);
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