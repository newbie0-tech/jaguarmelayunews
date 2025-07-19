<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$categories = $conn->query("SELECT id, name FROM categories 
  ORDER BY FIELD(name, 'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name")->fetch_all(MYSQLI_ASSOC);

$populer = $conn->query("SELECT judul, slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/portal/css/index.css">
<!DOCTYPE html><html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Map Media</title>
  <link rel="stylesheet" href="/portal/style.css">
  <style>
    .page-wrap{max-width:840px;margin:40px auto;padding:0 16px;color:#333;}
    h1{color:#0057b8;text-align:center;}
    iframe{width:100%;height:440px;border:0;border-radius:8px;margin-top:24px;}
  </style>
</head>
<body>
  <div class="page-wrap">
    <h1>Lokasi & Map Media</h1>
    <p>Kantor pusat kami dapat ditemukan di lokasi berikut:</p>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18..." allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
  </body>
  <?php require_once __DIR__.'/inc/footer.php'; ?>
</html>
