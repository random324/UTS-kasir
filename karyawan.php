<?php
require_once 'auth.php';
wajibAdmin();

include 'database/koneksi.php';
$pageTitle = 'Karyawan - Aplikasi Kasir';

$pesan = "";
$tipe_pesan = "";

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = trim($_GET['edit']);
    $stmt_e = mysqli_prepare($conn, "SELECT id, nama, username, role, status FROM user WHERE id = ?");
    mysqli_stmt_bind_param($stmt_e, "s", $id_edit);
    mysqli_stmt_execute($stmt_e);
    $res_e = mysqli_stmt_get_result($stmt_e);
    $edit_data = mysqli_fetch_assoc($res_e);
    mysqli_stmt_close($stmt_e);
}

if (isset($_POST['simpan_karyawan'])) {
    $mode     = $_POST['mode'] ?? 'tambah';
    $id       = trim($_POST['id']);
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);
    $status   = trim($_POST['status']);

    if ($id === "" || $nama === "" || $username === "" || $role === "" || $status === "") {
        $pesan = "ID, Nama, Username, Role, dan Status wajib diisi!";
        $tipe_pesan = "error";
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $pesan = "Status tidak valid!";
        $tipe_pesan = "error";
    } else {
        if ($mode === 'edit') {
            if ($password !== "") {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE user SET nama = ?, username = ?, password = ?, role = ?, status = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssssss", $nama, $username, $password_hash, $role, $status, $id);
            } else {
                $sql = "UPDATE user SET nama = ?, username = ?, role = ?, status = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssss", $nama, $username, $role, $status, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $pesan = "Data karyawan ($id) berhasil diperbarui!";
                $tipe_pesan = "success";
                $edit_data = null;
            } else {
                $pesan = "Gagal memperbarui data: " . mysqli_error($conn);
                $tipe_pesan = "error";
            }
            mysqli_stmt_close($stmt);

        } else {
            if ($password === "") {
                $pesan = "Password wajib diisi untuk karyawan baru!";
                $tipe_pesan = "error";
            } else {
                $cek = mysqli_prepare($conn, "SELECT id FROM user WHERE id = ? OR username = ?");
                mysqli_stmt_bind_param($cek, "ss", $id, $username);
                mysqli_stmt_execute($cek);
                mysqli_stmt_store_result($cek);

                if (mysqli_stmt_num_rows($cek) > 0) {
                    $pesan = "ID atau Username sudah digunakan!";
                    $tipe_pesan = "error";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO user (id, nama, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssss", $id, $nama, $username, $password_hash, $role, $status);

                    if (mysqli_stmt_execute($stmt)) {
                        $pesan = "Data karyawan baru berhasil ditambahkan!";
                        $tipe_pesan = "success";
                    } else {
                        $pesan = "Gagal menyimpan data: " . mysqli_error($conn);
                        $tipe_pesan = "error";
                    }
                    mysqli_stmt_close($stmt);
                }
                mysqli_stmt_close($cek);
            }
        }
    }
}

$karyawan_list = [];
$q_user = mysqli_query($conn, "SELECT id, nama, username, role, status FROM user ORDER BY id ASC");
if ($q_user) {
    while ($row = mysqli_fetch_assoc($q_user)) {
        $karyawan_list[] = $row;
    }
}

include 'layout/sidebar.php';
include 'layout/navbar.php';
?>
<style>
.alert {
    padding: 12px 16px; 
    border-radius: 6px; 
    margin-bottom: 18px;
    font-size: 14px; 
    font-weight: 500;
}
.alert-success { 
    background-color: #d1fae5; 
    color: #065f46; 
    border: 1px solid #a7f3d0; 
}
.alert-error { 
    background-color: #fee2e2; 
    color: #991b1b; 
    border: 1px solid #fecaca; 
}

.form-section h2, .table-section h2 {
    font-size: 18px; 
    margin-bottom: 16px; 
    color: #111827;
}
.data-form { 
    display: flex; 
    flex-direction: column; 
    gap: 14px; 
}
.form-row { 
    display: flex; 
    gap: 16px; 
    flex-wrap: wrap; 
}
.form-group { 
    flex: 1; 
    min-width: 180px; 
    display: flex; 
    flex-direction: column; 
    gap: 6px; 
}
.form-group label { 
    font-size: 13px; 
    font-weight: 600; 
    color: #374151; 
}
.form-group input, .form-group select {
    padding: 9px 12px; 
    border: 1px solid #d1d5db; 
    border-radius: 6px;
    font-size: 14px; 
    outline: none; 
    background-color: #ffffff;
}
.form-group input[readonly] { 
    background-color: #f3f4f6; 
    color: #6b7280; 
}
.form-actions { 
    display: flex; 
    gap: 10px; 
    margin-top: 4px; 
}
.btn {
    padding: 10px 18px; 
    border: none; 
    border-radius: 6px; 
    font-size: 14px;
    font-weight: 500; 
    cursor: pointer; 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center;
}
.btn-primary { 
    background-color: #111827; 
    color: #ffffff; 
}
.btn-secondary { 
    background-color: #e5e7eb;
    color: #374151; 
}

.divider { 
    border: none; 
    border-top: 1px solid #e5e7eb; 
    margin: 26px 0; 
}
.table-responsive { 
    overflow-x: auto; 
}
.data-table { 
    width: 100%; 
    border-collapse: collapse; 
    font-size: 14px; 
    white-space: nowrap; 
}
.data-table th, .data-table td { 
    padding: 12px; 
    text-align: left; 
    border-bottom: 1px solid #e5e7eb; 
}
.data-table th { 
    background-color: #f3f4f6; 
    color: #111827; 
    font-weight: 600; 
}
.data-table tbody tr:hover { 
    background-color: #f9fafb; 
}

.status-badge {
    display: inline-block; 
    padding: 3px 9px; 
    border-radius: 999px;
    font-size: 12px; 
    font-weight: 600;
}
.status-aktif { 
    background-color: #d1fae5; 
    color: #047857; 
}
.status-nonaktif { 
    background-color: #fee2e2; 
    color: #b91c1c; 
}

.btn-edit {
    display: inline-block; 
    padding: 5px 10px; 
    border-radius: 4px;
    background-color: #fef3c7; 
    color: #b45309; 
    text-decoration: none;
    font-size: 12px; 
    font-weight: 600;
}
.btn-edit:hover { 
    background-color: #fde68a; 
}
</style>

<div class="content">
    <?php if ($pesan !== ""): ?>
        <div class="alert alert-<?= $tipe_pesan === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="form-section">
        <h2><?= $edit_data ? 'Edit Data Karyawan' : 'Tambah Data Karyawan' ?></h2>
        <form action="karyawan.php" method="POST" class="data-form">
            <input type="hidden" name="mode" value="<?= $edit_data ? 'edit' : 'tambah' ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="id">ID User</label>
                    <input type="text" id="id" name="id" maxlength="10"
                           value="<?= htmlspecialchars($edit_data['id'] ?? '') ?>"
                           <?= $edit_data ? 'readonly' : 'required' ?>
                           placeholder="Contoh: USR-01">
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" maxlength="35"
                           value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>"
                           placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="35"
                           value="<?= htmlspecialchars($edit_data['username'] ?? '') ?>"
                           placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">
                        Password <?= $edit_data ? '(Kosongkan jika tidak ingin diubah)' : '' ?>
                    </label>
                    <input type="password" id="password" name="password" maxlength="35"
                           <?= $edit_data ? '' : 'required' ?>
                           placeholder="<?= $edit_data ? '••••••••' : 'Masukkan password' ?>">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <?php $role_val = strtolower($edit_data['role'] ?? 'pegawai'); ?>
                    <select id="role" name="role" required>
                        <option value="admin" <?= $role_val === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="pegawai" <?= $role_val === 'pegawai' ? 'selected' : '' ?>>Pegawai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <?php $status_val = strtolower($edit_data['status'] ?? 'aktif'); ?>
                    <select id="status" name="status" required>
                        <option value="aktif" <?= $status_val === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $status_val === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="simpan_karyawan" class="btn btn-primary">
                    <?= $edit_data ? 'Simpan Perubahan' : 'Simpan Karyawan' ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="karyawan.php" class="btn btn-secondary">Batal Edit</a>
                <?php else: ?>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <hr class="divider">

    <section class="table-section">
        <h2>Daftar Karyawan</h2>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($karyawan_list) > 0): ?>
                        <?php foreach ($karyawan_list as $k): ?>
                            <?php $is_aktif = strtolower($k['status']) === 'aktif'; ?>
                            <tr>
                                <td><?= htmlspecialchars($k['id']) ?></td>
                                <td><?= htmlspecialchars($k['nama']) ?></td>
                                <td><?= htmlspecialchars($k['username']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($k['role'])) ?></td>
                                <td>
                                    <span class="status-badge <?= $is_aktif ? 'status-aktif' : 'status-nonaktif' ?>">
                                        <?= htmlspecialchars(ucfirst($k['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="karyawan.php?edit=<?= urlencode($k['id']) ?>" class="btn-edit">Edit / Ubah Status</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280;">Belum ada data karyawan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php
include 'layout/footer.php';
?>