<?php 
session_start();
include 'database/koneksi.php';
$pageTitle = 'Dashboard - Aplikasi Kasir';

$pesan = "";
$tipe_pesan = "";

// PROSES SIMPAN DATA KATEGORI
if (isset($_POST['simpan'])) {
    $id            = trim($_POST['id']);
    $nama_kategori = trim($_POST['nama_kategori']);

    if ($id === "" || $nama_kategori === "") {
        $pesan = "ID dan Nama Kategori wajib diisi!";
        $tipe_pesan = "error";
    } else {
        // Cek apakah ID kategori sudah ada di database
        $cek_stmt = mysqli_prepare($conn, "SELECT id FROM kategori WHERE id = ?");
        mysqli_stmt_bind_param($cek_stmt, "s", $id);
        mysqli_stmt_execute($cek_stmt);
        mysqli_stmt_store_result($cek_stmt);

        if (mysqli_stmt_num_rows($cek_stmt) > 0) {
            $pesan = "ID Kategori '$id' sudah digunakan!";
            $tipe_pesan = "error";
        } else {
            // Insert data baru ke tabel kategori
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO kategori (id, nama_kategori) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ss", $id, $nama_kategori);

            if (mysqli_stmt_execute($insert_stmt)) {
                $pesan = "Data kategori berhasil disimpan!";
                $tipe_pesan = "success";
            } else {
                $pesan = "Gagal menyimpan data: " . mysqli_error($conn);
                $tipe_pesan = "error";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($cek_stmt);
    }
}
$result = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY id ASC");
include 'layout/sidebar.php';
include 'layout/navbar.php';
?>
<style>
    .form-section h2,
.table-section h2 {
    font-size: 18px;
    margin-bottom: 18px;
    color: #111827;
}

/* --- FORM INPUT --- */
.data-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
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

.form-group input {
    padding: 9px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
    background-color: #ffffff;
}

.form-group input:focus {
    border-color: #111827;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 6px;
}

.btn {
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn:hover {
    opacity: 0.9;
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
    margin: 28px 0;
}

/* --- TABEL DATA --- */
.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    white-space: nowrap;
}

.data-table th,
.data-table td {
    padding: 12px 12px;
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

/* --- TOMBOL AKSI TABEL --- */
.action-cell {
    display: flex;
    gap: 6px;
}

.btn-action {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: background-color 0.2s;
}

.btn-edit {
    background-color: #fef3c7;
    color: #b45309;
}
.btn-edit:hover {
    background-color: #fde68a;
}

.btn-delete {
    background-color: #fee2e2;
    color: #b91c1c;
}
.btn-delete:hover {
    background-color: #fecaca;
}
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
</style>
<div class="content">
    <?php if ($pesan !== ""): ?>
    <div class="alert alert-<?= $tipe_pesan === 'success' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($pesan) ?>
    </div>
<?php endif; ?>
    <section class="form-section">
    <h2>Tambah Data Kategori</h2>
    <form action="" method="POST" class="data-form">
        <div class="form-row">
            <div class="form-group">
                <label for="id">ID Kategori</label>
                <input type="text" id="id" name="id" maxlength="10" placeholder="Contoh: KAT-01" required>
            </div>
            <div class="form-group" style="flex: 2;">
                <label for="nama_kategori">Nama Kategori</label>
                <input type="text" id="nama_kategori" name="nama_kategori" maxlength="50" placeholder="Contoh: Makanan" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Kategori</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
</section>

<hr class="divider">

<!-- BAGIAN 2: TABEL MENAMPILKAN DATA KATEGORI -->
<section class="table-section">
    <h2>Daftar Kategori</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td class="action-cell">
                                <a href="edit_kategori.php?id=<?= urlencode($row['id']) ?>" class="btn-action btn-edit">Edit</a>
                                <a href="hapus_kategori.php?id=<?= urlencode($row['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #6b7280;">Belum ada data kategori.</td>
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