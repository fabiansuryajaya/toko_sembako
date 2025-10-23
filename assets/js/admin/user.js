const tbody = document.querySelector('table tbody');
const userModal = document.getElementById('UserModal');
const modalTitle = document.getElementById('modalTitle');
const userIdInput = document.getElementById('user_id');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const saveUserBtn = document.getElementById('saveUserBtn');
const closeUserModalBtn = document.getElementById('closeUserModalBtn');
const createUserBtn = document.getElementById('createUserBtn');

// Fetch user data
async function fetchUsers() {
    try {
        const result = await callAPI({ url: '../api/user.php', method: 'GET' });
        console.log("result",result);
        
        tbody.innerHTML = '';
        result.data.data.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.username}</td>
                <td>********</td>
                <td>
                    <button class="editBtn" data-id="${user.id}">Edit</button>
                    <button class="deleteBtn" data-id="${user.id}">Hapus</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="4">Gagal memuat data user</td></tr>';
    }
}

fetchUsers();

// Open modal for create
createUserBtn.addEventListener('click', function () {
    modalTitle.textContent = 'Tambah User';
    userIdInput.value = '';
    usernameInput.value = '';
    passwordInput.value = '';
    userModal.style.display = 'flex';
});

// Close modal
closeUserModalBtn.addEventListener('click', function () {
    userModal.style.display = 'none';
});

// Save user (create or update)
saveUserBtn.addEventListener('click', async function () {
    const id = userIdInput.value;
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (!username || !password) {
        alert('Username dan Password wajib diisi!');
        return;
    }

    const body = { username, password };
    if (id) body.id = id;

    try {
        const result = await callAPI({
            url: '../api/user.php',
            method: id ? 'PUT' : 'POST',
            body
        });
        if (result.status !== 0) {
            alert(result.message || 'Gagal menyimpan user');
            return;
        }
        userModal.style.display = 'none';
        fetchUsers();
    } catch (err) {
        alert('Gagal menyimpan user');
    }
});

// Edit & Delete actions
tbody.addEventListener('click', async function (e) {
    if (e.target.classList.contains('editBtn')) {
        const id = e.target.getAttribute('data-id');
        try {
            const result = await callAPI({ url: `../api/user.php?id=${id}`, method: 'GET' });
            const user = result.data;
            modalTitle.textContent = 'Edit User';
            userIdInput.value = user.id;
            usernameInput.value = user.username;
            passwordInput.value = '';
            userModal.style.display = 'flex';
        } catch (err) {
            alert('Gagal mengambil data user');
        }
    } else if (e.target.classList.contains('deleteBtn')) {
        const id = e.target.getAttribute('data-id');
        if (confirm('Yakin hapus user ini?')) {
            try {
                const result = await callAPI({ url: `../api/user.php?id=${id}`, method: 'DELETE' });
                if (result.status !== 0) {
                    alert(result.message || 'Gagal menghapus user');
                    return;
                }
                fetchUsers();
            } catch (err) {
                alert('Gagal menghapus user');
            }
        }
    }
});

// Close modal on outside click
window.addEventListener('click', function (event) {
    if (event.target === userModal) {
        userModal.style.display = 'none';
    }
});