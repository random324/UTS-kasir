<?php 
session_start();
include 'database/koneksi.php';
$pageTitle = 'Edit Produk - Aplikasi Kasir';

$pesan = "";

if (!isset($_GET['id']) || trim($_GET['id']) === "") {
    header("Location: produk.php");
    exit;
}

$id_lama = trim($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "s", $id_lama);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    echo "<script>alert('Data produk tidak ditemukan!'); window.location='produk.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $kode        = trim($_POST['kode']);
    $nama        = trim($_POST['nama']);
    $kategori_id = trim($_POST['kategori_id']);
    $harga_beli  = (int) $_POST['harga_beli'];
    $haga_jual   = (int) $_POST['haga_jual'];
    $stok        = (int) $_POST['stok'];
    $satuan      = trim($_POST['satuan']);

    $sql = "UPDATE produk 
            SET kode = ?, nama = ?, kategori_id = ?, harga_beli = ?, haga_jual = ?, stok = ?, satuan = ? 
            WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($update_stmt, "sssiiiss", $kode, $nama, $kategori_id, $harga_beli, $haga_jual, $stok, $satuan, $id_lama);

    if (mysqli_stmt_execute($update_stmt)) {
        mysqli_stmt_close($update_stmt);
        echo "<script>alert('Data produk berhasil diperbarui!'); window.location='produk.php';</script>";
        exit;
    } else {
        $pesan = "Gagal memperbarui produk: " . mysqli_error($conn);
        mysqli_stmt_close($update_stmt);
    }
}

$kategori_query = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

include 'layout/sidebar.php';
include 'layout/navbar.php';
?>
<style>
.alert-error {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 18px;
    font-size: 14px;
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.form-section h2 { 
    font-size: 18px; 
    margin-bottom: 18px; 
    color: #111827; 
}
.data-form { 
    display: flex; 
    flex-direction: 
    column; gap: 15px; 
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
    margin-top: 6px; 
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
</style>

<div class="content">
    <?php if ($pesan !== ""): ?>
        <div class="alert-error"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <section class="form-section">
        <h2>Edit Data Produk</h2>
        <form action="" method="POST" class="data-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="id">ID Produk</label>
                    <input type="text" id="id" name="id" value="<?= htmlspecialchars($data['id']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="kode">Kode Produk</label>
                    <input type="text" id="kode" name="kode" maxlength="10" value="<?= htmlspecialchars($data['kode']) ?>" required>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" maxlength="50" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                            <option value="<?= htmlspecialchars($kat['id']) ?>" <?= $kat['id'] === $data['kategori_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat['nama_kategori']) ?> (<?= htmlspecialchars($kat['id']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <select id="satuan" name="satuan" required>
                        <?php 
                        $daftar_satuan = ['Pcs', 'Cup', 'Porsi', 'Box', 'Kg'];
                        foreach ($daftar_satuan as $s): 
                        ?>
                            <option value="<?= $s ?>" <?= $data['satuan'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="harga_beli">Harga Beli (Rp)</label>
                    <input type="number" id="harga_beli" name="harga_beli" min="0" value="<?= htmlspecialchars($data['harga_beli']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="haga_jual">Harga Jual (Rp)</label>
                    <input type="number" id="haga_jual" name="haga_jual" min="0" value="<?= htmlspecialchars($data['haga_jual']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" min="0" value="<?= htmlspecialchars($data['stok']) ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="produk.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
</div>

<?php 
include 'layout/footer.php';
?>