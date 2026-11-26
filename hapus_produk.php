PHP
<?php
require_once 'auth.php';
wajibAdmin();

include 'database/koneksi.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    header("Location: produk.php");
    exit;
}

$id = trim($_GET['id']);

$stmt_cek = mysqli_prepare($conn, "SELECT COUNT(*) AS jumlah_pakai FROM detail_transaksi WHERE produk_id = ?");
mysqli_stmt_bind_param($stmt_cek, "s", $id);
mysqli_stmt_execute($stmt_cek);
$res_cek = mysqli_stmt_get_result($stmt_cek);
$data_cek = mysqli_fetch_assoc($res_cek);
mysqli_stmt_close($stmt_cek);

if (($data_cek['jumlah_pakai'] ?? 0) > 0) {
    echo "<script>
        alert('Produk gagal dihapus! Produk ini masih memiliki riwayat transaksi (digunakan pada laporan Barang Terlaris & Keuntungan).');
        window.location='produk.php';
    </script>";
    exit;
}

try {
    $stmt_hapus = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($stmt_hapus, "s", $id);

    if (mysqli_stmt_execute($stmt_hapus)) {
        echo "<script>
            alert('Produk berhasil dihapus!');
            window.location='produk.php';
        </script>";
    } else {
        echo "<script>
            alert('Produk tidak dapat dihapus karena masih terhubung dengan data lain.');
            window.location='produk.php';
        </script>";
    }
    mysqli_stmt_close($stmt_hapus);
} catch (mysqli_sql_exception $e) {
    echo "<script>
        alert('Produk tidak dapat dihapus karena masih digunakan pada riwayat transaksi!');
        window.location='produk.php';
    </script>";
}
exit;