<div class="page member-page">
    <h1>Daftar Member</h1>
    <div class="create-container">
        <button class="createBtn" id="createMemberBtn">Buat Member</button>
    </div>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Member</th>
                <th>Nomor HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="MemberModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Buat Member</h2>
            <form id="createMemberForm">
                <label for="name">Nama:</label>
                <input type="text" id="member_name" name="name" required>
                <label for="name">No Hp:</label>
                <input type="text" id="nomor_hp" name="nomor_hp" required>
               
                <button type="button" id="closeModalBtn">Batal</button>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
let action = "create";
let editMemberId = null;

function openModal(act = "create", id = null) {
    action = act;
    editMemberId = id;
    document.getElementById('MemberModal').style.display = 'flex';
}

document.getElementById('createMemberBtn').addEventListener('click', () => openModal("create"));

document.getElementById('closeModalBtn').addEventListener('click', () => {
    document.getElementById('MemberModal').style.display = 'none';
    document.getElementById('createMemberForm').reset();
    editMemberId = null;
});

const memberTable = document.querySelector('.member-page tbody');

async function fetchMember() {
    try {
        const result = await callAPI({ url: '../api/member.php', method: 'GET' });
        memberTable.innerHTML = '';
        result.data.forEach(member => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${member.id}</td>
                <td>${member.nama}</td>
                <td>${member.nomor_hp}</td>
                <td>
                    <button data-id="${member.id}" class="edit-btn">Edit</button>
                </td>
            `;
            memberTable.appendChild(tr);
        });
        addTableEventListeners();
    } catch (error) {
        console.error('Gagal memuat member:', error);
    }
}

function addTableEventListeners() {
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.getAttribute('data-id');
            await getEditData(id);
        });
    });
    // Tambahkan event listener untuk tombol hapus jika diperlukan
}

fetchMember();

async function submitMember() {
    try {
        const method = action === "create" ? 'POST' : 'PUT';
        const body = {
            nama: document.getElementById('member_name').value,
            nomor_hp: document.getElementById('nomor_hp').value
        };
        if (action === "edit" && editMemberId) {
            body.id = editMemberId;
        }
        await callAPI({ url: '../api/member.php', method, body });
        fetchMember();
    } catch (error) {
        console.error('Gagal menyimpan member:', error);
    }
}

document.getElementById('createMemberForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitMember();
    document.getElementById('MemberModal').style.display = 'none';
    document.getElementById('createMemberForm').reset();
    editMemberId = null;
});

async function getEditData(id) {
    try {
        const result = await callAPI({ url: `../api/member.php?id=${id}`, method: 'GET' });
        if (result.data.length > 0) {
            openModal("edit", id);
            document.getElementById('member_name').value = result.data[0].nama;
        } else {
            console.error('Member tidak ditemukan');
        }
    } catch (error) {
        console.error('Gagal mendapatkan data member:', error);
    }
}
</script>