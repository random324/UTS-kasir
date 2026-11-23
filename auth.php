<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function wajibLogin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }
}

function wajibAdmin() {
    wajibLogin();
    if (strtolower($_SESSION['role']) !== 'admin') {
        echo "<script>alert('Akses ditolak! Halaman ini khusus untuk Admin.'); window.location='dashboard.php';</script>";
        exit;
    }
}