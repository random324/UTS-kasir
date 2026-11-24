<?php 
session_start();
include 'database/koneksi.php';
$pageTitle = 'Dashboard - Aplikasi Kasir';

$terlaris_list = [];
$q_terlaris = mysqli_query($conn, "
    SELECT p.id, p.kode, p.nama, p.satuan, COALESCE(SUM(dt.jumlah), 0) AS total_terjual
    FROM detail_transaksi dt
    JOIN produk p ON dt.produk_id = p.id
    GROUP BY p.id, p.kode, p.nama, p.satuan
    ORDER BY total_terjual DESC
    LIMIT 3
");
if ($q_terlaris) {
    while ($row = mysqli_fetch_assoc($q_terlaris)) {
        $terlaris_list[] = $row;
    }
}

$stok_menipis_list = [];
$q_menipis = mysqli_query($conn, "
    SELECT id, kode, nama, stok, satuan
    FROM produk
    WHERE stok <= 15
    ORDER BY stok ASC, nama ASC
");
if ($q_menipis) {
    while ($row = mysqli_fetch_assoc($q_menipis)) {
        $stok = (int) $row['stok'];
        if ($stok === 0) {
            $row['status_stok'] = 'Stok Habis';
            $row['status_class'] = 'status-habis';
        } else {
            $row['status_stok'] = 'Stok Menipis';
            $row['status_class'] = 'status-menipis';
        }
        $stok_menipis_list[] = $row;
    }
}

$keuntungan_hari_ini = 0;
$q_untung = mysqli_query($conn, "
    SELECT COALESCE(SUM((dt.harga_satuan - dt.harga_beli) * dt.jumlah), 0) AS keuntungan
    FROM detail_transaksi dt
    JOIN transaksi t ON dt.transaksi_id = t.id
    WHERE DATE(t.tanggal) = CURDATE()
");
if ($q_untung) {
    $d_untung = mysqli_fetch_assoc($q_untung);
    $keuntungan_hari_ini = (int) ($d_untung['keuntungan'] ?? 0);
}

$omzet_hari_ini = 0;
$jumlah_trx_hari_ini = 0;
$q_ringkasan = mysqli_query($conn, "
    SELECT COALESCE(SUM(total), 0) AS omzet, COUNT(*) AS jml_trx
    FROM transaksi
    WHERE DATE(tanggal) = CURDATE()
");
if ($q_ringkasan) {
    $d_ringkasan = mysqli_fetch_assoc($q_ringkasan);
    $omzet_hari_ini = (int) ($d_ringkasan['omzet'] ?? 0);
    $jumlah_trx_hari_ini = (int) ($d_ringkasan['jml_trx'] ?? 0);
}

$transaksi_hari_ini = [];
$q_trx = mysqli_query($conn, "
    SELECT t.id, t.tanggal, t.user_id, u.nama AS nama_user, t.total, t.bayar, t.kembali, t.`metode bayar` AS metode_bayar
    FROM transaksi t
    LEFT JOIN user u ON t.user_id = u.id
    WHERE DATE(t.tanggal) = CURDATE()
    ORDER BY t.tanggal DESC
");
if ($q_trx) {
    while ($row = mysqli_fetch_assoc($q_trx)) {
        $transaksi_hari_ini[] = $row;
    }
}

include 'layout/sidebar.php';
include 'layout/navbar.php';
?>
<style>
.dashboard-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: stretch;
}

.info-card {
    flex: 1;
    min-width: 280px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 18px 20px;
    background-color: #ffffff;
    display: flex;
    flex-direction: column;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.card-header h3 {
    font-size: 16px;
    color: #111827;
}

.badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 999px;
}

.badge-info {
    background-color: #dbeafe;
    color: #1d4ed8;
}

.badge-warning {
    background-color: #fef3c7;
    color: #b45309;
}

.badge-success {
    background-color: #d1fae5;
    color: #047857;
}

.card-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 240px;
    overflow-y: auto;
}

.card-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border-radius: 6px;
    background-color: #f9fafb;
    gap: 10px;
}

.card-list-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border-radius: 6px;
    background-color: #fef2f2;
    gap: 10px;
}

.item-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.item-sub {
    font-size: 12px;
    color: #6b7280;
    margin-top: 3px;
}

.status-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}

.status-habis {
    background-color: #fee2e2;
    color: #b91c1c;
}

.status-menipis {
    background-color: #fef3c7;
    color: #b45309;
}

.item-stat {
    font-size: 12px;
    font-weight: 600;
    color: #111827;
    background-color: #e5e7eb;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
}

.btn-restok-mini {
    font-size: 12px;
    font-weight: 600;
    color: #047857;
    background-color: #d1fae5;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    white-space: nowrap;
    transition: background-color 0.2s;
}

.btn-restok-mini:hover {
    background-color: #a7f3d0;
}

.profit-box {
    padding: 12px 0;
    margin: auto 0;
}

.profit-amount {
    font-size: 26px;
    font-weight: 700;
    color: #047857;
    margin-bottom: 8px;
}

.profit-meta {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.6;
}

.empty-text {
    font-size: 13px;
    color: #6b7280;
    padding: 14px 0;
    text-align: center;
}

.divider {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 26px 0;
}

.table-section h2 {
    font-size: 18px;
    margin-bottom: 16px;
    color: #111827;
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
</style>

<div class="content">
    <section class="dashboard-section">

        <div class="dashboard-cards">

            <div class="info-card">
                <div class="card-header">
                    <h3>Barang Terlaris</h3>
                    <span class="badge badge-info">Top Produk</span>
                </div>
                <?php if (count($terlaris_list) > 0): ?>
                    <ul class="card-list">
                        <?php foreach ($terlaris_list as $item): ?>
                            <li class="card-list-item">
                                <div>
                                    <div class="item-title"><?= htmlspecialchars($item['nama']) ?></div>
                                    <div class="item-sub">Kode: <?= htmlspecialchars($item['kode']) ?> • <?= htmlspecialchars($item['satuan']) ?></div>
                                </div>
                                <span class="item-stat"><?= (int)$item['total_terjual'] ?> terjual</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-text">Belum ada data penjualan.</div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3>Stok Menipis</h3>
                    <span class="badge badge-warning"><?= count($stok_menipis_list) ?> Barang</span>
                </div>
                <?php if (count($stok_menipis_list) > 0): ?>
                    <ul class="card-list">
                        <?php foreach ($stok_menipis_list as $m): ?>
                            <li class="card-list-row">
                                <div>
                                    <div class="item-title">
                                        <?= htmlspecialchars($m['nama']) ?>
                                        <span class="status-pill <?= $m['status_class'] ?>"><?= $m['status_stok'] ?></span>
                                    </div>
                                    <div class="item-sub">
                                        Kode: <?= htmlspecialchars($m['kode']) ?> • Sisa stok: <strong><?= (int)$m['stok'] ?></strong> <?= htmlspecialchars($m['satuan']) ?>
                                    </div>
                                </div>
                                <a href="restok_produk.php?id=<?= urlencode($m['id']) ?>" class="btn-restok-mini">+ Restok</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-text">Semua stok produk aman.</div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3>Keuntungan Hari Ini</h3>
                    <span class="badge badge-success"><?= date('d M Y') ?></span>
                </div>
                <div class="profit-box">
                    <div class="profit-amount">Rp <?= number_format($keuntungan_hari_ini, 0, ',', '.') ?></div>
                    <div class="profit-meta">
                        Total Transaksi: <strong><?= $jumlah_trx_hari_ini ?></strong> transaksi<br>
                        Total Penjualan: <strong>Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>

        </div>

        <hr class="divider">

        <div class="table-section">
            <h2>Daftar Transaksi Hari Ini</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Nama Kasir</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembali</th>
                            <th>Metode Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transaksi_hari_ini) > 0): ?>
                            <?php foreach ($transaksi_hari_ini as $trx): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trx['id']) ?></td>
                                    <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($trx['nama_user'] ?? $trx['user_id']) ?></td>
                                    <td>Rp <?= number_format((int)$trx['total'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format((int)$trx['bayar'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format((int)$trx['kembali'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($trx['metode_bayar']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #6b7280;">Belum ada transaksi pada hari ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
<?php 
include 'layout/footer.php';
?>