<?php 
session_start();
include 'database/koneksi.php';
$pageTitle = 'Restok Produk - Aplikasi Kasir';

$pesan = "";

if (!isset($_GET['id']) || trim($_GET['id']) === "") {
    header("Location: produk.php");
    exit;
}

$id = trim($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT id, kode, nama, stok, satuan FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='produk.php';</script>";
    exit;
}

if (isset($_POST['proses_restok'])) {
    $tambah_stok = (int) $_POST['tambah_stok'];

    if ($tambah_stok <= 0) {
        $pesan = "Jumlah tambah stok harus lebih dari 0!";
    } else {
        $update_stmt = mysqli_prepare($conn, "UPDATE produk SET stok = stok + ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "is", $tambah_stok, $id);

        if (mysqli_stmt_execute($update_stmt)) {
            mysqli_stmt_close($update_stmt);
            echo "<script>alert('Stok berhasil ditambahkan sebanyak +$tambah_stok!'); window.location='produk.php';</script>";
            exit;
        } else {
            $pesan = "Gagal menambah stok: " . mysqli_error($conn);
            mysqli_stmt_close($update_stmt);
        }
    }
}

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
.btn-restok-submit { 
    background-color: #047857; 
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
        <h2>Tambah Stok Produk (Restok)</h2>
        <form action="" method="POST" class="data-form">
            <div class="form-row">
                <div class="form-group">
                    <label>ID & Kode Produk</label>
                    <input type="text" value="<?= htmlspecialchars($data['id'] . ' - ' . $data['kode']) ?>" readonly>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label>Nama Produk</label>
                    <input type="text" value="<?= htmlspecialchars($data['nama']) ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Stok Saat Ini</label>
                    <input type="text" value="<?= htmlspecialchars($data['stok'] . ' ' . $data['satuan']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="tambah_stok">Jumlah Stok Masuk (+)</label>
                    <input type="number" id="tambah_stok" name="tambah_stok" min="1" placeholder="Masukkan jumlah tambahan stok" required autofocus>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="proses_restok" class="btn btn-restok-submit">+ Tambah Stok</button>
                <a href="produk.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
</div>

<?php 
include 'layout/footer.php';
?>