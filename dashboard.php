<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Kasir</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --primary:#5a55e8; --ink:#1e1b4b; --muted:#8b8fa8; --bg:#f6f7fb; --card:#fff; --line:#eceef5;
  --pink:#ffe2e5; --orange:#fff4de; --green:#dcfce7; --purple:#f3e8ff;
  --blue:#0095ff; --mint:#00e096;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',system-ui,sans-serif;font-size:13px;color:var(--ink);
  background:linear-gradient(135deg,#f3f5c9,#c8f5e0);min-height:100vh;padding:28px}
a{text-decoration:none;color:inherit}

/* ===== Frame ===== */
.app{display:grid;grid-template-columns:220px 1fr;background:#fff;border-radius:20px;
  overflow:hidden;max-width:1280px;margin:auto;box-shadow:0 20px 50px rgba(30,27,75,.12)}

/* ===== Sidebar ===== */
.sidebar{padding:26px 22px;display:flex;flex-direction:column;gap:30px;background:#fff}
.brand{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:600}
.logo{width:36px;height:36px;border-radius:10px;background:var(--primary);color:#fff;
  display:grid;place-items:center;font-weight:700}
.menu{list-style:none;display:flex;flex-direction:column;gap:6px}
.menu a{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;color:#6b6f8a}
.menu .ico{width:18px;text-align:center;font-size:15px}
.menu .active a{background:var(--primary);color:#fff;font-weight:500;box-shadow:0 8px 18px rgba(90,85,232,.35)}
.menu a:hover{background:var(--bg)}
.menu .active a:hover{background:var(--primary)}
.menu .sep{margin:6px 0;border-top:1px solid var(--line)}
.sidebar .menu:last-child{margin-top:auto}

/* ===== Main ===== */
.main{background:var(--bg);min-width:0}
.navbar{display:flex;align-items:center;gap:20px;padding:20px 28px;background:#fff}
.navbar h1{font-size:24px;font-weight:600;flex:0 0 auto}
.search{flex:1;max-width:340px;margin-left:auto;display:flex;align-items:center;gap:10px;
  background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:11px 16px}
.search input{border:0;background:none;outline:0;font:inherit;width:100%;color:var(--ink)}
.user{display:flex;align-items:center;gap:10px;margin-left:12px}
.avatar{width:38px;height:38px;border-radius:50%;background:var(--primary);color:#fff;
  display:grid;place-items:center;font-weight:600}
.user b{display:block;font-size:12px;font-weight:600}
.user small{color:var(--muted);font-size:11px}

.content{padding:24px 28px;display:grid;gap:20px}
.card{background:var(--card);border-radius:14px;padding:20px}
.card h2{font-size:15px;font-weight:600}
.card .sub{color:var(--muted);font-size:12px;margin-top:2px}

/* ===== Stat cards ===== */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:18px}
.stat{border-radius:12px;padding:16px}
.stat .dot{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;color:#fff;font-size:13px;margin-bottom:12px}
.stat strong{display:block;font-size:20px;font-weight:600}
.stat span{display:block;font-weight:500;margin:2px 0 4px}
.stat small{font-size:10.5px;color:var(--primary)}
.s1{background:var(--pink)}.s1 .dot{background:#ff6b81}
.s2{background:var(--orange)}.s2 .dot{background:#ff9f43}
.s3{background:var(--green)}.s3 .dot{background:#22c55e}
.s4{background:var(--purple)}.s4 .dot{background:#a855f7}

/* ===== Grid ===== */
.row{display:grid;grid-template-columns:2fr 1fr;gap:20px}
.head{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
.legend{display:flex;gap:16px;color:var(--muted);font-size:11px;margin-top:14px;justify-content:center}
.legend i{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px}

/* ===== Bar chart (CSS saja) ===== */
.chart{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;height:190px;align-items:end;
  margin-top:18px;border-bottom:1px solid var(--line);
  background:repeating-linear-gradient(to top,transparent 0,transparent 37px,var(--line) 37px,var(--line) 38px)}
.chart div{display:flex;align-items:flex-end;justify-content:center;gap:4px;height:100%}
.chart b{width:12px;border-radius:4px 4px 0 0;background:var(--blue)}
.chart b+b{background:var(--mint)}
.days{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;text-align:center;color:var(--muted);font-size:11px;margin-top:8px}

/* ===== Produk terlaris ===== */
.list{list-style:none;margin-top:14px;display:grid;gap:16px}
.list li{display:grid;grid-template-columns:1fr auto;gap:6px 10px;align-items:center}
.list .bar{grid-column:1/-1;height:6px;background:var(--line);border-radius:6px;overflow:hidden}
.list .bar i{display:block;height:100%;border-radius:6px}
.list em{font-style:normal;color:var(--muted);font-size:12px}

/* ===== Tabel ===== */
table{width:100%;border-collapse:collapse;margin-top:10px}
th{text-align:left;color:var(--muted);font-weight:500;font-size:12px;padding:10px 8px;border-bottom:1px solid var(--line)}
td{padding:13px 8px;border-bottom:1px solid var(--line)}
tr:last-child td{border-bottom:0}
.tag{display:inline-block;padding:2px 10px;border-radius:6px;font-size:11px;border:1px solid}
.tag.tunai{color:#22a06b;background:#e8faf1;border-color:#22a06b}
.tag.qris{color:var(--primary);background:#eeedff;border-color:var(--primary)}
.tag.debit{color:#e08a00;background:#fff4de;border-color:#e08a00}
.r{text-align:right}
.low{color:#e11d48;font-weight:500}

/* ===== Responsive ===== */
@media(max-width:1000px){
  .stats{grid-template-columns:repeat(2,1fr)}
  .row{grid-template-columns:1fr}
}
@media(max-width:760px){
  body{padding:0}
  .app{grid-template-columns:1fr;border-radius:0}
  .sidebar{flex-direction:row;align-items:center;padding:14px 18px;gap:14px;overflow-x:auto}
  .brand span{display:none}
  .menu{flex-direction:row}
  .menu a{white-space:nowrap}
  .sidebar .menu:last-child{margin:0}
  .menu .sep{display:none}
  .navbar{flex-wrap:wrap}
  .search{order:3;max-width:none;flex-basis:100%;margin:0}
  .user{margin-left:auto}
  .content{padding:16px}
}
</style>
</head>
<body>
<div class="app">

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar">
    <div class="brand"><div class="logo">K</div><span>Kasirku</span></div>

    <ul class="menu">
      <li class="active"><a href="#"><span class="ico"></span>Dashboard</a></li>
      <li><a href="#"><span class="ico"></span>Transaksi</a></li>
      <li><a href="#"><span class="ico"></span>Produk</a></li>
      <li><a href="#"><span class="ico"></span>Kategori</a></li>
      <li><a href="#"><span class="ico"></span>Pengguna</a></li>
    </ul>

    <ul class="menu">
      <li class="sep"></li>
      <li><a href="#"><span class="ico"></span>Keluar</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN ===== -->
  <div class="main">

    <!-- NAVBAR -->
    <header class="navbar">
      <h1>Dashboard</h1>
      <label class="search"> <input type="text" placeholder="Cari faktur / produk..."></label>
      <div class="user">
        <div class="avatar">M</div>
        <div><b>Musfiq</b><small>Admin</small></div>
      </div>
    </header>

    <main class="content">

      <!-- Ringkasan hari ini (tabel: transaksi) -->
      <section class="card">
        <div class="head">
          <div><h2>Penjualan Hari Ini</h2><p class="sub">Ringkasan transaksi</p></div>
        </div>
        <div class="stats">
          <div class="stat s1"><div class="dot"></div><strong>Rp 4,2 jt</strong><span>Total Penjualan</span><small>+8% dari kemarin</small></div>
          <div class="stat s2"><div class="dot"></div><strong>128</strong><span>Jumlah Transaksi</span><small>+5% dari kemarin</small></div>
          <div class="stat s3"><div class="dot"></div><strong>356</strong><span>Produk Terjual</span><small>+1,2% dari kemarin</small></div>
          <div class="stat s4"><div class="dot"></div><strong>7</strong><span>Stok Menipis</span><small>Perlu restock</small></div>
        </div>
      </section>

      <!-- Transaksi terbaru (tabel: transaksi + users) -->
      <section class="card">
        <h2>Transaksi Terbaru</h2>
        <table>
          <thead>
            <tr><th>No. Faktur</th><th>Tanggal</th><th>Kasir</th><th>Metode Bayar</th><th class="r">Total</th></tr>
          </thead>
          <tbody>
            <tr><td>INV-0001284</td><td>21 Nov 2026, 14:20</td><td>Musfiq</td><td><span class="tag tunai">Tunai</span></td><td class="r">Rp 85.000</td></tr>
            <tr><td>INV-0001283</td><td>21 Nov 2026, 14:05</td><td>Rani</td><td><span class="tag qris">QRIS</span></td><td class="r">Rp 142.500</td></tr>
            <tr><td>INV-0001282</td><td>21 Nov 2026, 13:48</td><td>Musfiq</td><td><span class="tag debit">Debit</span></td><td class="r">Rp 320.000</td></tr>
            <tr><td>INV-0001281</td><td>21 Nov 2026, 13:30</td><td>Rani</td><td><span class="tag tunai">Tunai</span></td><td class="r">Rp 56.000</td></tr>
          </tbody>
        </table>
      </section>

    </main>
  </div>
</div>
</body>
</html>