<?php 
session_start();
$pageTitle = 'Produk - Aplikasi Kasir';
include 'layout/sidebar.php';
include 'layout/navbar.php';
?>
<style>
    .form-section h2,
.table-section h2 {
    font-size: 18px;
    margin-bottom: 18px;
    color: #111827;
}

/* --- FORM INPUT --- */
.data-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #111827;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 5px;
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

/* --- TABEL DATA --- */
.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.data-table th,
.data-table td {
    padding: 12px 14px;
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

/* --- TOMBOL AKSI TABEL --- */
.btn-action {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    margin-right: 4px;
}

.btn-edit {
    background-color: #fef3c7;
    color: #b45309;
}

.btn-edit:hover {
    background-color: #fde68a;
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
      <section class="form-section">
      <h2>Tambah Data Produk</h2>
      <form action="" method="POST" class="data-form">
            <div class="form-row">
                  <div class="form-group">
                  <label for="kode_produk">Kode Produk</label>
                  <input type="text" id="kode_produk" name="kode_produk" placeholder="Contoh: PRD-001" required>
                  </div>
                  <div class="form-group">
                  <label for="nama_produk">Nama Produk</label>
                  <input type="text" id="nama_produk" name="nama_produk" placeholder="Masukkan nama produk" required>
                  </div>
            </div>

            <div class="form-row">
                  <div class="form-group">
                  <label for="kategori">Kategori</label>
                  <select id="kategori" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Snack">Snack</option>
                  </select>
                  </div>
                  <div class="form-group">
                  <label for="harga">Harga (Rp)</label>
                  <input type="number" id="harga" name="harga" placeholder="Contoh: 15000" required>
                  </div>
                  <div class="form-group">
                  <label for="stok">Stok</label>
                  <input type="number" id="stok" name="stok" placeholder="0" required>
                  </div>
            </div>

            <div class="form-actions">
                  <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
      </form>
      </section>

      <hr class="divider">

      <!-- BAGIAN 2: TABEL MENAMPILKAN DATA -->
      <section class="table-section">
      <h2>Daftar Produk</h2>
      <div class="table-responsive">
            <table class="data-table">
                  <thead>
                  <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                        <td>1</td>
                        <td>PRD-001</td>
                        <td>Kopi Susu Gula Aren</td>
                        <td>Minuman</td>
                        <td>Rp 18.000</td>
                        <td>25</td>
                        <td>
                              <a href="#" class="btn-action btn-edit">Edit</a>
                              <a href="#" class="btn-action btn-delete">Hapus</a>
                        </td>
                  </tr>
                  <tr>
                        <td>2</td>
                        <td>PRD-002</td>
                        <td>Nasi Goreng Spesial</td>
                        <td>Makanan</td>
                        <td>Rp 25.000</td>
                        <td>12</td>
                        <td>
                              <a href="#" class="btn-action btn-edit">Edit</a>
                              <a href="#" class="btn-action btn-delete">Hapus</a>
                        </td>
                  </tr>
                  <tr>
                        <td>3</td>
                        <td>PRD-003</td>
                        <td>Kentang Goreng</td>
                        <td>Snack</td>
                        <td>Rp 15.000</td>
                        <td>30</td>
                        <td>
                              <a href="#" class="btn-action btn-edit">Edit</a>
                              <a href="#" class="btn-action btn-delete">Hapus</a>
                        </td>
                  </tr>
                  </tbody>
            </table>
      </div>
      </section>
</div>
<?php 
include 'layout/footer.php';
?>