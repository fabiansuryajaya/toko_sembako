<div class="page user-page">
    <h1>Master User</h1>
    <div class="create-container">
        <button class="createBtn" id="createUserBtn">Tambah User</button>
    </div>

    <table border="1" cellspacing="0" cellpadding="8" style="width:100%;margin-top:16px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Modal User -->
    <div id="UserModal" class="modal" style="display:none;">
        <div class="modal-content" style="min-width:320px;max-width:400px;">
            <h2 id="modalTitle">Tambah User</h2>
            <input type="hidden" id="user_id">
            <div style="margin-bottom:8px;">
                <label for="username">Username:</label>
                <input type="text" id="username" style="width:100%;" required>
            </div>
            <div style="margin-bottom:8px;">
                <label for="password">Password:</label>
                <input type="password" id="password" style="width:100%;" required>
            </div>
            <div style="text-align:right;">
                <button type="button" id="closeUserModalBtn">Batal</button>
                <button type="button" id="saveUserBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/admin/user.js?v=20251023"></script>