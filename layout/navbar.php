<?php
$nama_user = $_SESSION['nama'] ?? 'Guest';
$role_user = ucfirst($_SESSION['role'] ?? 'User');
$judul_halaman = $pageTitle ?? 'Aplikasi Kasir';
?>
<div class="main">
    <nav class="navbar">
        <div class="location">
            <span><?= htmlspecialchars($judul_halaman) ?></span>
        </div>
        <div class="user">
            <span class="name"><?= htmlspecialchars($nama_user) ?> |</span>
            <span class="role"><?= htmlspecialchars($role_user) ?></span>
        </div>
    </nav>