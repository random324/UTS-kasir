<?php
session_start();
include 'database/koneksi.php';

if (!isset($_GET['id']) || trim($_GET['id']) === "") {
    header("Location: kategori.php");
    exit;
}

$id = trim($_GET['id']);

$delete_stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($delete_stmt, "s", $id);

if (mysqli_stmt_execute($delete_stmt)) {
    mysqli_stmt_close($delete_stmt);
    echo "<script>alert('Data kategori berhasil dihapus!'); window.location='kategori.php';</script>";
    exit;
} else {
    $error_msg = mysqli_error($conn);
    mysqli_stmt_close($delete_stmt);
    echo "<script>alert('Gagal menghapus data: " . addslashes($error_msg) . "'); window.location='kategori.php';</script>";
    exit;
}