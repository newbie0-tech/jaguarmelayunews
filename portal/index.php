<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$categories = $conn->query("SELECT id, name FROM categories 
  ORDER BY FIELD(name, 'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name")->fetch_all(MYSQLI_ASSOC);
$populer = $conn->query("SELECT judul,slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<link rel="stylesheet" href="/portal/css/index.css">

<div class="page-wrapper">
  <!-- YouTube kecil di bawah header -->
  <div class="youtube-box">
    <h3>Jaguar Channel</h3>
    <div class="youtube-wrapper">
      <iframe src="https://www.youtube.com/embed/pG0RgDw55kI" allowfullscreen></iframe>
    </div>
  </div>

  <h1 class="page-title">Berita Terbaru</h1>

  <div class="main-grid">
    <!-- Konten utama (berita kategori) -->
    <div class="news-content">
      <?php foreach($categories as $cat): /* loop berita per kategori */ endforeach; ?>
    </div>

    <!-- Sidebar kanan: Populer -->
    <aside class="sidebar">
      <h3>Berita Populer</h3>
      <ul class="popular-list">
        <?php foreach($populer as $p): ?>
          <li><a href="artikel.php?slug=<?= urlencode($p['slug']) ?>"><?= htmlspecialchars($p['judul']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
  </div>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
