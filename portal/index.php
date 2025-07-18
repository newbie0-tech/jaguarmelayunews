<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

// Ambil daftar kategori
$categories = $conn->query("SELECT id, name FROM categories 
  ORDER BY FIELD(name, 'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name")->fetch_all(MYSQLI_ASSOC);

// Ambil 5 berita populer
$populer = $conn->query("SELECT judul, slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<link rel="stylesheet" href="/portal/css/index.css">

<div class="page-wrapper">
  <!-- Embed YouTube di bawah header -->
  <div class="youtube-box">
    <h3>Jaguar Channel</h3>
    <div class="youtube-wrapper">
      <iframe src="https://www.youtube.com/embed/pG0RgDw55kI" allowfullscreen></iframe>
    </div>
  </div>
  <h1 class="page-title">Berita Terbaru</h1>
<div class="main-grid">

  <!-- Kolom iklan kiri -->
  <div class="left-ads">
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <div class="ad-box">
        <img src="/portal/assets/ads/iklan<?= $i ?>.png" alt="Iklan <?= $i ?>">
      </div>
    <?php endfor; ?>
  </div>

  <!-- Konten berita -->
  <div class="news-content">
    <?php foreach ($categories as $cat): 
      $catId = $cat['id'];
      $catName = $cat['name'];
      $stmt = $conn->prepare("SELECT judul, slug, gambar, tanggal FROM posts WHERE kategori_id=? AND status=1 ORDER BY tanggal DESC LIMIT 4");
      $stmt->bind_param('i', $catId);
      $stmt->execute();
      $posts = $stmt->get_result();
      if ($posts->num_rows < 1) continue;
    ?>
      <section class="news-section">
        <h2 class="cat-title"><?= htmlspecialchars($catName) ?></h2>
        <?php while ($p = $posts->fetch_assoc()):
          $imgSrc = $p['gambar'] ?: 'assets/placeholder.jpg';
          $imgFull =  (strpos($imgSrc, 'http') === 0) ? $imgSrc : '/portal/' . ltrim($imgSrc, '/');
        ?>
          <article class="news-card">
            <a href="artikel.php?slug=<?= urlencode($p['slug']) ?>">
              <img src="<?= htmlspecialchars($imgFull) ?>" alt="<?= htmlspecialchars($p['judul']) ?>">
            </a>
            <div class="news-info">
              <h3><a href="artikel.php?slug=<?= urlencode($p['slug']) ?>"><?= htmlspecialchars($p['judul']) ?></a></h3>
              <time datetime="<?= $p['tanggal'] ?>"><?= date('d M Y', strtotime($p['tanggal'])) ?></time>
            </div>
          </article>
        <?php endwhile; ?>
      </section>
    <?php endforeach; ?>
  </div>

  <!-- Sidebar berita populer + iklan kanan -->
  <aside class="sidebar">
    <h3>Berita Populer</h3>
    <ul class="popular-list" style="max-height: 220px; overflow-y: auto; padding-right: 5px;">
      <?php foreach ($populer as $pop): ?>
        <li><a href="artikel.php?slug=<?= urlencode($pop['slug']) ?>"><?= htmlspecialchars($pop['judul']) ?></a></li>
      <?php endforeach; ?>
    </ul>

    <div class="ads-sidebar">
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div class="ads-box">
          <img src="/portal/assets/ads/iklan2<?= $i ?>.png" alt="Iklan <?= $i ?>">
        </div>
      <?php endfor; ?>
    </div>
  </aside>

</div>

</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
