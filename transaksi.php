<?php 
require_once 'auth.php';
wajibLogin();

include 'database/koneksi.php';
$pageTitle = 'Transaksi - Aplikasi Kasir';

$pesan = "";
$tipe_pesan = "";

$user_login_id   = $_SESSION['user_id'];
$user_login_nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? $user_login_id);

function generateIdTransaksi(mysqli $conn) {
    $query = mysqli_query($conn, "SELECT MAX(id) AS max_id FROM transaksi WHERE id LIKE 'TRX-%'");
    $data  = mysqli_fetch_assoc($query);
    $maxId = $data['max_id'] ?? '';

    if ($maxId !== '') {
        $urutan = (int) substr($maxId, 4) + 1;
    } else {
        $urutan = 1;
    }

    return 'TRX-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
}

if (isset($_POST['simpan_transaksi'])) {
    $id           = generateIdTransaksi($conn);
    $user_id      = $user_login_id;
    $bayar        = (int) $_POST['bayar'];
    $metode_bayar = trim($_POST['metode_bayar']);

    $produk_ids   = $_POST['produk_id'] ?? [];
    $jumlahs      = $_POST['jumlah'] ?? [];

    if ($user_id === "" || $metode_bayar === "" || count($produk_ids) === 0) {
        $pesan = "Lengkapi metode bayar dan minimal pilih 1 produk!";
        $tipe_pesan = "error";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $keranjang = [];
            for ($i = 0; $i < count($produk_ids); $i++) {
                $pid = trim($produk_ids[$i]);
                $qty = (int) $jumlahs[$i];
                if ($pid !== "" && $qty > 0) {
                    if (!isset($keranjang[$pid])) {
                        $keranjang[$pid] = 0;
                    }
                    $keranjang[$pid] += $qty;
                }
            }

            if (count($keranjang) === 0) {
                throw new Exception("Pilih minimal 1 produk dengan jumlah minimal 1!");
            }

            $total_belanja = 0;
            $item_valid = [];

            $stmt_produk = mysqli_prepare($conn, "SELECT nama, harga_beli, haga_jual, stok FROM produk WHERE id = ? FOR UPDATE");
            foreach ($keranjang as $pid => $qty) {
                mysqli_stmt_bind_param($stmt_produk, "s", $pid);
                mysqli_stmt_execute($stmt_produk);
                $res_p = mysqli_stmt_get_result($stmt_produk);
                $prod = mysqli_fetch_assoc($res_p);

                if (!$prod) {
                    throw new Exception("Produk dengan ID '$pid' tidak ditemukan!");
                }
                if ($prod['stok'] < $qty) {
                    throw new Exception("Stok '{$prod['nama']}' tidak mencukupi! (Sisa stok: {$prod['stok']})");
                }

                $harga_beli   = (int) $prod['harga_beli'];
                $harga_satuan = (int) $prod['haga_jual'];
                $subtotal     = $harga_satuan * $qty;
                $total_belanja += $subtotal;

                $item_valid[] = [
                    'produk_id'    => $pid,
                    'jumlah'       => $qty,
                    'harga_beli'   => $harga_beli,
                    'harga_satuan' => $harga_satuan,
                    'subtotal'     => $subtotal
                ];
            }
            mysqli_stmt_close($stmt_produk);

            if ($bayar < $total_belanja) {
                throw new Exception("Uang bayar (Rp " . number_format($bayar, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($total_belanja, 0, ',', '.') . ")!");
            }
            $kembali = $bayar - $total_belanja;

            $sql_trx = "INSERT INTO transaksi (id, user_id, total, bayar, kembali, `metode bayar`) 
                        VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_trx = mysqli_prepare($conn, $sql_trx);
            mysqli_stmt_bind_param($stmt_trx, "ssiiis", $id, $user_id, $total_belanja, $bayar, $kembali, $metode_bayar);
            if (!mysqli_stmt_execute($stmt_trx)) {
                throw new Exception("Gagal menyimpan transaksi: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt_trx);

            $stmt_detail = mysqli_prepare($conn, "INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, harga_satuan, harga_beli, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_stok   = mysqli_prepare($conn, "UPDATE produk SET stok = stok - ? WHERE id = ?");

            foreach ($item_valid as $item) {
                mysqli_stmt_bind_param($stmt_detail, "ssiiii", $id, $item['produk_id'], $item['jumlah'], $item['harga_satuan'], $item['harga_beli'], $item['subtotal']);
                if (!mysqli_stmt_execute($stmt_detail)) {
                    throw new Exception("Gagal menyimpan detail transaksi: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt_stok, "is", $item['jumlah'], $item['produk_id']);
                if (!mysqli_stmt_execute($stmt_stok)) {
                    throw new Exception("Gagal mengurangi stok produk: " . mysqli_error($conn));
                }
            }
            mysqli_stmt_close($stmt_detail);
            mysqli_stmt_close($stmt_stok);

            mysqli_commit($conn);
            $pesan = "Transaksi ($id) berhasil! Total: Rp " . number_format($total_belanja, 0, ',', '.') . " | Kembalian: Rp " . number_format($kembali, 0, ',', '.');
            $tipe_pesan = "success";

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $pesan = $e->getMessage();
            $tipe_pesan = "error";
        }
    }
}

$id_otomatis = generateIdTransaksi($conn);

$produk_list = [];
$q_produk = mysqli_query($conn, "SELECT id, kode, nama, haga_jual, stok, satuan FROM produk WHERE stok > 0 ORDER BY nama ASC");
if ($q_produk) {
    while ($p = mysqli_fetch_assoc($q_produk)) {
        $produk_list[] = $p;
    }
}

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
.items-section h3 {
    font-size: 18px;
    margin-bottom: 16px;
    color: #111827;
}
.items-section h3 {
    font-size: 15px;
    margin-top: 8px;
    margin-bottom: 10px;
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
.form-group input[readonly] {
    background-color: #f3f4f6;
    color: #111827;
    font-weight: 600;
}

/* --- TABEL KERANJANG BARANG --- */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.items-table th,
.items-table td {
    padding: 10px;
    border: 1px solid #e5e7eb;
    text-align: left;
}
.items-table th {
    background-color: #f3f4f6;
    font-size: 13px;
    color: #111827;
}
.items-table select,
.items-table input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 14px;
}
.items-table input[readonly] {
    background-color: #f9fafb;
    color: #374151;
}
.btn-add-row {
    align-self: flex-start;
    background-color: #dbeafe;
    color: #1d4ed8;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.btn-add-row:hover {
    background-color: #bfdbfe;
}
.btn-remove-row {
    background-color: #fee2e2;
    color: #b91c1c;
    border: none;
    padding: 7px 11px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}
.btn-remove-row:hover {
    background-color: #fecaca;
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
</style>

<div class="content">
    <?php if ($pesan !== ""): ?>
        <div class="alert alert-<?= $tipe_pesan === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="form-section">
        <h2>Input Transaksi Kasir</h2>
        <form action="" method="POST" class="data-form">

            <div class="form-row">
                <div class="form-group">
                    <label for="id">ID Transaksi (Otomatis)</label>
                    <input type="text" id="id" name="id" value="<?= htmlspecialchars($id_otomatis) ?>" readonly required>
                </div>
                <div class="form-group">
                    <label for="kasir_nama">Kasir / User (Otomatis)</label>
                    <input type="text" id="kasir_nama" value="<?= htmlspecialchars($user_login_nama) ?>" readonly>
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_login_id) ?>">
                </div>
                <div class="form-group">
                    <label>Waktu Transaksi</label>
                    <input type="text" value="Otomatis (Saat Disimpan)" readonly>
                </div>
            </div>

            <div class="items-section">
                <h3>Daftar Barang yang Dibeli</h3>
                <table class="items-table" id="tabel-barang">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Pilih Produk</th>
                            <th style="width: 18%;">Harga (Rp)</th>
                            <th style="width: 14%;">Jumlah</th>
                            <th style="width: 20%;">Subtotal (Rp)</th>
                            <th style="width: 8%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="keranjang-body">
                        <tr class="item-row">
                            <td>
                                <select name="produk_id[]" class="select-produk" onchange="updateBaris(this)" required>
                                    <option value="" data-harga="0" data-stok="0">-- Pilih Produk --</option>
                                    <?php foreach ($produk_list as $p): ?>
                                        <option value="<?= htmlspecialchars($p['id']) ?>" 
                                                data-harga="<?= (int)$p['haga_jual'] ?>" 
                                                data-stok="<?= (int)$p['stok'] ?>">
                                            <?= htmlspecialchars($p['nama']) ?> (Stok: <?= (int)$p['stok'] ?> <?= htmlspecialchars($p['satuan']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="input-harga" value="0" readonly>
                            </td>
                            <td>
                                <input type="number" name="jumlah[]" class="input-jumlah" value="1" min="1" oninput="updateBaris(this)" required>
                            </td>
                            <td>
                                <input type="number" class="input-subtotal" value="0" readonly>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-remove-row" onclick="hapusBaris(this)">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn-add-row" onclick="tambahBaris()">+ Tambah Barang</button>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total">Total Belanja (Rp)</label>
                    <input type="number" id="total" name="total" value="0" readonly required>
                </div>
                <div class="form-group">
                    <label for="bayar">Bayar (Rp)</label>
                    <input type="number" id="bayar" name="bayar" placeholder="0" min="0" oninput="hitungKembali()" required>
                </div>
                <div class="form-group">
                    <label for="kembali">Kembali (Rp)</label>
                    <input type="number" id="kembali" name="kembali" value="0" readonly required>
                </div>
                <div class="form-group">
                    <label for="metode_bayar">Metode Bayar</label>
                    <select id="metode_bayar" name="metode_bayar" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="simpan_transaksi" class="btn btn-primary">Proses & Simpan Transaksi</button>
                <button type="reset" class="btn btn-secondary" onclick="resetFormTransaksi()">Reset</button>
            </div>

        </form>
    </section>
</div>

<script>
    function tambahBaris() {
        const tbody = document.getElementById('keranjang-body');
        const barisPertama = tbody.querySelector('.item-row');
        const barisBaru = barisPertama.cloneNode(true);

        barisBaru.querySelector('.select-produk').selectedIndex = 0;
        barisBaru.querySelector('.input-harga').value = 0;
        const inputJumlah = barisBaru.querySelector('.input-jumlah');
        inputJumlah.value = 1;
        inputJumlah.removeAttribute('max');
        barisBaru.querySelector('.input-subtotal').value = 0;

        tbody.appendChild(barisBaru);
    }

    function hapusBaris(tombol) {
        const tbody = document.getElementById('keranjang-body');
        const semuaBaris = tbody.querySelectorAll('.item-row');
        if (semuaBaris.length > 1) {
            tombol.closest('.item-row').remove();
            hitungTotalBelanja();
        } else {
            alert('Minimal harus ada 1 baris barang dalam transaksi!');
        }
    }

    function updateBaris(elemen) {
        const row = elemen.closest('.item-row');
        const select = row.querySelector('.select-produk');
        const optionTerpilih = select.options[select.selectedIndex];

        const harga = parseInt(optionTerpilih.getAttribute('data-harga')) || 0;
        const stokMax = parseInt(optionTerpilih.getAttribute('data-stok')) || 0;

        const inputHarga = row.querySelector('.input-harga');
        const inputJumlah = row.querySelector('.input-jumlah');
        const inputSubtotal = row.querySelector('.input-subtotal');

        inputHarga.value = harga;

        if (stokMax > 0) {
            inputJumlah.max = stokMax;
            if (parseInt(inputJumlah.value) > stokMax) {
                inputJumlah.value = stokMax;
            }
        }

        const jumlah = parseInt(inputJumlah.value) || 0;
        inputSubtotal.value = harga * jumlah;

        hitungTotalBelanja();
    }

    function hitungTotalBelanja() {
        const semuaSubtotal = document.querySelectorAll('.input-subtotal');
        let total = 0;
        semuaSubtotal.forEach(input => {
            total += parseInt(input.value) || 0;
        });

        document.getElementById('total').value = total;
        hitungKembali();
    }

    function hitungKembali() {
        const total = parseInt(document.getElementById('total').value) || 0;
        const bayar = parseInt(document.getElementById('bayar').value) || 0;
        const kembali = bayar - total;

        document.getElementById('kembali').value = kembali >= 0 ? kembali : 0;
    }

    function resetFormTransaksi() {
        setTimeout(() => {
            const tbody = document.getElementById('keranjang-body');
            const semuaBaris = tbody.querySelectorAll('.item-row');
            semuaBaris.forEach((baris, idx) => {
                if (idx > 0) baris.remove();
            });
            tbody.querySelector('.input-harga').value = 0;
            tbody.querySelector('.input-jumlah').value = 1;
            tbody.querySelector('.input-subtotal').value = 0;
            document.getElementById('total').value = 0;
            document.getElementById('kembali').value = 0;
        }, 10);
    }
</script>

<?php 
include 'layout/footer.php';
?>