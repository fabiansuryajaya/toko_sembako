document.addEventListener('DOMContentLoaded', () => {
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

    function loadPage(page) {
        fetch(page + '.php')
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat halaman: ' + page);
                return response.text();
            })
            .then(html => {
                screenContent.innerHTML = html;
            })
            .catch(err => {
                screenContent.innerHTML = '<p>Gagal memuat halaman.</p>';
                console.error(err);
            });
    }

    // Load default page
    loadPage('home');
});
