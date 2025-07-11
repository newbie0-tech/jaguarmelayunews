<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

/* ── ambil kategori ─ */
$categories = [];
$q = "SELECT id,name
      FROM categories
      ORDER BY FIELD(name,'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name";
$r = $conn->query($q);
while($row = $r->fetch_assoc()) $categories[] = $row;

$colorMap = [
  'Budaya Lokal'=>'#20c997','Daerah'=>'#0d6efd','Dunia'=>'#6610f2',
  'Hukum'=>'#6f42c1','Nasional'=>'#198754','Pendidikan'=>'#fd7e14','Politik'=>'#dc3545'
];
function catColor($n,$m){ return $m[$n] ?? '#0d6efd'; }

$populer = $conn->query("SELECT judul,slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")?->fetch_all(MYSQLI_ASSOC) ?? [];
?>

<link rel="stylesheet" href="/portal/css/index.css">
<style>
body {
  background: #f8f9fa;
  font-family: Verdana, sans-serif;
}
.page-wrapper {
  max-width: 1280px;
  margin: auto;
  padding: 20px;
}
.youtube-embed {
  margin-bottom: 30px;
  text-align: center;
}
.youtube-embed iframe {
  width: 100%;
  max-width: 800px;
  height: 450px;
  border-radius: 8px;
  border: none;
}
h1.page-title {
  text-align: center;
  font-size: 32px;
  margin-top: 0;
  color: #5a3e1b;
}
.main-grid {
  display: grid;
  grid-template-columns: 1.2fr 3fr 1.2fr;
  gap: 24px;
  margin-top: 20px;
}
.sidebar, .news-content {
  background: #fff;
  padding: 16px 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.sidebar h3 {
  font-size: 18px;
  border-bottom: 2px solid #ccc;
  margin-bottom: 12px;
  padding-bottom: 6px;
}
.popular-list li {
  margin: 8px 0;
  list-style-type: square;
}
.popular-list a {
  color: #0056b3;
  text-decoration: none;
}
.news-section {
  margin-bottom: 40px;
}
.cat-title {
  font-size: 20px;
  color: var(--cat-clr);
  margin-bottom: 16px;
  border-bottom: 1px solid #ddd;
  padding-bottom: 4px;
}
.news-card {
  display: flex;
  gap: 14px;
  margin-bottom: 18px;
  background: #fdfdfd;
  border-radius: 6px;
  overflow: hidden;
}
.news-card img {
  width: 120px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}
.news-info h3 {
  margin: 0 0 4px;
  font-size: 16px;
  color: #333;
}
.news-info time {
  font-size: 13px;
  color: #666;
}
@media (max-width: 992px) {
  .main-grid {
    grid-template-columns: 1fr;
  }
}
</style>
<!-- index.php -->
<link rel="stylesheet" href="css/style.css">
<div class="page-wrapper">
  <!-- YouTube Embed -->
  <div class="youtube-embed">
    <h3 style="font-size:22px; margin-bottom:12px; color:#d00000;">Jaguar Channel</h3>
    <iframe width="100%" height="400"
  src="https://www.youtube.com/embed/pG0RgDw55kI"
  title="YouTube video player" frameborder="0"
  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
  allowfullscreen>
</iframe>
  </div>
  <h1 class="page-title">Berita Terbaru</h1>

  <div class="main-grid">
    <!-- Sidebar Kiri: Kategori -->
    <aside class="sidebar">
      <h3>Kategori Populer</h3>
      <ul style="padding-left:18px;">
        <?php foreach($categories as $c): ?>
          <li><a href="kategori.php?id=<?= $c['id'] ?>" style="color:<?= catColor($c['name'],$colorMap) ?>"><?= htmlspecialchars($c['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <!-- Konten Utama -->
    <div class="news-content">
      <?php foreach($categories as $cat):
        $stmt = $conn->prepare("
          SELECT judul,slug,gambar,tanggal
          FROM posts
          WHERE kategori_id=? AND status=1
          ORDER BY tanggal DESC
          LIMIT 5");
        $stmt->bind_param('i', $cat['id']);
        $stmt->execute();
        $posts = $stmt->get_result();
        if(!$posts->num_rows) continue;
        $cColor = catColor($cat['name'],$colorMap);
      ?>
      <section class="news-section" style="--cat-clr:<?= $cColor ?>;">
        <h2 class="cat-title"><?= htmlspecialchars($cat['name']) ?></h2>
        <?php while($p = $posts->fetch_assoc()):
          $imgRel = $p['gambar'] ?: 'assets/placeholder.jpg';
          $imgSrc = '/portal/' . ltrim($imgRel, '/');
        ?>
        <article class="news-card">
          <a href="artikel.php?slug=<?= urlencode($p['slug']) ?>">
            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['judul']) ?>">
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

    <!-- Sidebar Kanan: Populer -->
    <aside class="sidebar">
      <h3>Berita Populer</h3>
      <ul class="popular-list">
        <?php foreach($populer as $pop): ?>
          <li><a href="artikel.php?slug=<?= urlencode($pop['slug']) ?>"><?= htmlspecialchars($pop['judul']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
  </div>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
