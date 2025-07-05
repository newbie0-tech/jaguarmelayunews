<?php
// admin/hapus_berita.php (dengan konfirmasi UI)
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__.'/../inc/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: daftar_berita.php');
    exit;
}

// Ambil detail berita
$cek = $conn->prepare("SELECT judul, gambar FROM posts WHERE id=? LIMIT 1");
$cek->bind_param('i', $id);
$cek->execute();
$cek->bind_result($judul, $gambar);
if (!$cek->fetch()) {
    $cek->close();
    header('Location: daftar_berita.php?msg=notfound');
    exit;
}
$cek->close();

// Jika form konfirmasi dikirim POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // hapus gambar lama
    if ($gambar && file_exists(__DIR__.'/../'.$gambar)) @unlink(__DIR__.'/../'.$gambar);

    $del = $conn->prepare("DELETE FROM posts WHERE id=? LIMIT 1");
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();

    header('Location: daftar_berita.php?msg=deleted');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Hapus Berita</title>
  <link rel="stylesheet" href="/portal/style.css">
  <style>
    .confirm-wrap{max-width:600px;margin:60px auto;background:#fff;padding:32px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.08);text-align:center;}
    .confirm-wrap h1{color:#dc3545;font-size:24px;margin-top:0;}
    .confirm-wrap p{font-size:15px;color:#333;}
    .btn{display:inline-block;padding:8px 18px;border-radius:4px;font-size:14px;color:#fff;text-decoration:none;margin:0 6px;}
    .btn-red{background:#dc3545;}
    .btn-red:hover{background:#b22424;}
    .btn-grey{background:#6c757d;}
    .btn-grey:hover{background:#555e65;}
    img.preview{max-width:100%;height:auto;margin:18px 0;border-radius:6px;}
  </style>
</head>
<body>
  <div class="confirm-wrap">
    <h1>Hapus Berita</h1>
    <p>Anda yakin ingin menghapus berita:</p>
    <p><strong><?= htmlspecialchars($judul) ?></strong></p>
    <?php if($gambar): ?><img src="/portal/<?= htmlspecialchars($gambar) ?>" class="preview" alt="cover"><?php endif; ?>

    <form method="post" style="margin-top:24px;">
      <button type="submit" class="btn btn-red">Ya, Hapus</button>
      <a href="daftar_berita.php" class="btn btn-grey">Batal</a>
    </form>
  </div>
</body>
</html>
