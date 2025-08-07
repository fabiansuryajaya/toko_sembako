<div class="page unit-page">
    <h1>Daftar Satuan</h1>
    <div class="create-container">
        <button class="createBtn" id="createUnitBtn">Buat Satuan</button>
    </div>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Popup modal -->
    <div id="UnitModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Buat Satuan</h2>
            <form id="createUnitForm">
                <label for="name">Nama Satuan:</label>
                <input type="text" id="unit_name" name="name" required>
                <button type="submit">Simpan</button>
                <button type="button" id="closeModalBtn">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>
let action = "create";
let editUnitId = null;

function openModal(act = "create", id = null) {
    action = act;
    editUnitId = id;
    document.getElementById('UnitModal').style.display = 'flex';
}

document.getElementById('createUnitBtn').addEventListener('click', () => openModal("create"));

document.getElementById('closeModalBtn').addEventListener('click', () => {
    document.getElementById('UnitModal').style.display = 'none';
    document.getElementById('createUnitForm').reset();
    editUnitId = null;
});

const unitTable = document.querySelector('.unit-page tbody');

async function fetchUnit() {
    try {
        const result = await callAPI({ url: '../api/unit.php', method: 'GET' });
        unitTable.innerHTML = '';
        result.data.forEach(unit => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${unit.id}</td>
                <td>${unit.nama}</td>
                <td>
                    <button data-id="${unit.id}" class="edit-btn">Edit</button>
                    <button data-id="${unit.id}" class="delete-btn">Hapus</button>
                </td>
            `;
            unitTable.appendChild(tr);
        });
        addTableEventListeners();
    } catch (error) {
        console.error('Gagal memuat unit:', error);
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

fetchUnit();

async function submitUnit() {
    try {
        const method = action === "create" ? 'POST' : 'PUT';
        const body = {
            nama: document.getElementById('unit_name').value
        };
        if (action === "edit" && editUnitId) {
            body.id = editUnitId;
        }
        await callAPI({ url: '../api/unit.php', method, body });
        fetchUnit();
    } catch (error) {
        console.error('Gagal menyimpan unit:', error);
    }
}

document.getElementById('createUnitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitUnit();
    document.getElementById('UnitModal').style.display = 'none';
    document.getElementById('createUnitForm').reset();
    editUnitId = null;
});

async function getEditData(id) {
    try {
        const result = await callAPI({ url: `../api/unit.php?id=${id}`, method: 'GET' });
        if (result.data.length > 0) {
            openModal("edit", id);
            document.getElementById('unit_name').value = result.data[0].nama;
        } else {
            console.error('Unit tidak ditemukan');
        }
    } catch (error) {
        console.error('Gagal mendapatkan data unit:', error);
    }
}
</script>