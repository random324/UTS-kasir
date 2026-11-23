<?php
$pageTitle = $pageTitle ?? 'Aplikasi kasir';
$role_login = strtolower($_SESSION['role'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <aside class="sidebar">
    <div class="sidebar-item">
      <div class="sidebar-header">
        <span class="brand">Kasir</span>
      </div>
      <div class="item">
        <ul class="nav-list">
          <div class="list">
            <li><a href="dashboard.php" class="nav-item">Dashboard</a></li>
          </div>
          <div class="list">
            <li><a href="transaksi.php" class="nav-item">Transaksi</a></li>
          </div>

          <?php if ($role_login === 'admin'): ?>
            <div class="list">
              <li><a href="produk.php" class="nav-item">Produk</a></li>
            </div>
            <div class="list">
              <li><a href="kategori.php" class="nav-item">Kategori</a></li>
            </div>
            <div class="list">
              <li><a href="karyawan.php" class="nav-item">Karyawan</a></li>
            </div>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="logout">
      <a href="logout.php" class="logout-btn">Log-out</a>
    </div>
  </aside>