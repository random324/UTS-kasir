<?php 
session_start();
include 'database/koneksi.php';
$pageTitle = 'Produk - Aplikasi Kasir';

$pesan = "";
$tipe_pesan = "";

if (isset($_POST['simpan'])) {
    $id          = trim($_POST['id']);
    $kode        = trim($_POST['kode']);
    $nama        = trim($_POST['nama']);
    $kategori_id = trim($_POST['kategori_id']);
    $harga_beli  = (int) $_POST['harga_beli'];
    $haga_jual   = (int) $_POST['haga_jual'];
    $stok        = (int) $_POST['stok'];
    $satuan      = trim($_POST['satuan']);

    if ($id === "" || $kode === "" || $nama === "" || $kategori_id === "" || $satuan === "") {
        $pesan = "Semua kolom wajib diisi!";
        $tipe_pesan = "error";
    } else {
        $cek_stmt = mysqli_prepare($conn, "SELECT id FROM produk WHERE id = ?");
        mysqli_stmt_bind_param($cek_stmt, "s", $id);
        mysqli_stmt_execute($cek_stmt);
        mysqli_stmt_store_result($cek_stmt);

        if (mysqli_stmt_num_rows($cek_stmt) > 0) {
            $pesan = "ID Produk '$id' sudah digunakan!";
            $tipe_pesan = "error";
        } else {
            $sql = "INSERT INTO produk (id, kode, nama, kategori_id, harga_beli, haga_jual, stok, satuan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($insert_stmt, "ssssiiis", $id, $kode, $nama, $kategori_id, $harga_beli, $haga_jual, $stok, $satuan);

            if (mysqli_stmt_execute($insert_stmt)) {
                $pesan = "Data produk berhasil disimpan!";
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

$kategori_query = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

$produk_query = mysqli_query($conn, "
    SELECT p.*, k.nama_kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    ORDER BY p.id ASC
");

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
    .form-section h2,
    .table-section h2 {
        font-size: 18px;
        margin-bottom: 18px;
        color: #111827;
    }
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
    .form-group input,
.form-group select {
    padding: 9px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
    background-color: #ffffff;
}
.form-group input:focus,
.form-group select:focus {
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
.btn-restok {
    background-color: #d1fae5;
    color: #047857;
}
.btn-restok:hover {
    background-color: #a7f3d0;
}
.btn-delete {
    background-color: #fee2e2;
    color: #b91c1c;
}
.btn-delete:hover {
    background-color: #fecaca;
}
</style>

<div class="content">
    <?php if ($pesan !== ""): ?>
        <div class="alert alert-<?= $tipe_pesan === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="form-section">
        <h2>Tambah Data Produk</h2>
        <form action="" method="POST" class="data-form">

            <div class="form-row">
                <div class="form-group">
                    <label for="id">ID Produk</label>
                    <input type="text" id="id" name="id" maxlength="10" placeholder="Contoh: P001" required>
                </div>
                <div class="form-group">
                    <label for="kode">Kode Produk</label>
                    <input type="text" id="kode" name="kode" maxlength="10" placeholder="Contoh: BRG-001" required>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" maxlength="50" placeholder="Masukkan nama produk" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php if ($kategori_query && mysqli_num_rows($kategori_query) > 0): ?>
                            <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= htmlspecialchars($kat['id']) ?>">
                                    <?= htmlspecialchars($kat['nama_kategori']) ?> (<?= htmlspecialchars($kat['id']) ?>)
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <select id="satuan" name="satuan" required>
                        <option value="">-- Pilih Satuan --</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Cup">Cup</option>
                        <option value="Porsi">Porsi</option>
                        <option value="Box">Box</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="harga_beli">Harga Beli (Rp)</label>
                    <input type="number" id="harga_beli" name="harga_beli" min="0" placeholder="Contoh: 10000" required>
                </div>
                <div class="form-group">
                    <label for="haga_jual">Harga Jual (Rp)</label>
                    <input type="number" id="haga_jual" name="haga_jual" min="0" placeholder="Contoh: 15000" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok Awal</label>
                    <input type="number" id="stok" name="stok" min="0" placeholder="0" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Produk</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </section>

    <hr class="divider">

    <section class="table-section">
        <h2>Daftar Produk</h2>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produk_query && mysqli_num_rows($produk_query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($produk_query)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['kode']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori'] ?? $row['kategori_id']) ?></td>
                                <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['haga_jual'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['stok']) ?></td>
                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                                <td class="action-cell">
                                    <a href="edit_produk.php?id=<?= urlencode($row['id']) ?>" class="btn-action btn-edit">Edit</a>
                                    <a href="restok_produk.php?id=<?= urlencode($row['id']) ?>" class="btn-action btn-restok">+ Restok</a>
                                    <a href="hapus_produk.php?id=<?= urlencode($row['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: #6b7280;">Belum ada data produk.</td>
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