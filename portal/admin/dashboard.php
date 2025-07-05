<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__.'/../inc/db.php';
require_once __DIR__.'/../inc/header.php';
?>
<style>
.dashboard-wrap{max-width:900px;margin:40px auto;padding:0 12px;display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));}
.action-card{background:#fff;border:1px solid #ddd;border-radius:8px;padding:28px 20px;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.05);transition:transform .15s,box-shadow .15s;}
.action-card:hover{transform:translateY(-4px);box-shadow:0 8px 14px rgba(0,0,0,.08);} 
.action-card h3{margin:0 0 12px;font-size:20px;color:#0057b8;}
.action-card p{font-size:14px;color:#444;margin:0 0 18px;}
.action-card .btn{padding:8px 16px;font-size:14px;border-radius:4px;background:#0057b8;color:#fff;text-decoration:none;display:inline-block;}
.action-card .btn:hover{background:#00408a;}
</style>

<div class="dashboard-wrap">
  <div class="action-card">
    <h3>Daftar Berita</h3>
    <p>Lihat, edit, atau hapus berita yang sudah ada.</p>
    <a class="btn" href="daftar_berita.php">Kelola Berita</a>
  </div>
  <div class="action-card">
    <h3>Tambah Berita</h3>
    <p>Buat berita baru untuk ditampilkan di portal.</p>
    <a class="btn" href="tambah_berita.php" target="_blank">Buat Berita</a>
  </div>
  <div class="action-card">
    <h3>Tambah Reporter</h3>
    <p>Tambahkan akun reporter/editor baru.</p>
    <a class="btn" href="tambah_reporter.php">Tambah Reporter</a>
  </div>
</div>

<?php require_once __DIR__.'/../inc/footer.php'; ?>
