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

// Ambil 5 berita populer
$populer = $conn->query("SELECT judul,slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")?->fetch_all(MYSQLI_ASSOC) ?? [];
?>
<link rel="stylesheet" href="/portal/css/index.css">
<style>
.main-grid {
  display: grid;
  grid-template-columns: 1fr 3fr 1.2fr;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
.sidebar, .news-content {
  background: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.sidebar h3 {
  margin-top: 0;
  font-size: 18px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 8px;
}
.popular-list li {
  margin: 10px 0;
}
.popular-list a {
  color: #0057b8;
  text-decoration: none;
}
.share-btns a {
  margin: 6px 6px 0 0;
  padding: 8px 12px;
  font-size: 13px;
  color: #fff;
  text-decoration: none;
  border-radius: 4px;
  font-weight: bold;
}
.share-btns .wa { background: #25D366; }
.share-btns .fb { background: #3b5998; }
.share-btns .tt { background: #000; }
</style>

<h1 class="page-title">Berita Terbaru</h1>

<div class="main-grid">
  <!-- Sidebar KIRI: YouTube -->
  <aside class="sidebar">
    <h3>Jaguar Channel</h3>
    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
      <iframe src="https://www.youtube.com/embed/@teamgampemburusejarahmelay3314" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
    </div>
    <div class="share-btns" style="margin-top:10px;">
      <a class="wa" href="https://wa.me/?text=Simak%20video%20ini%20https://youtu.be/" target="_blank">WhatsApp</a>
      <a class="fb" href="https://www.facebook.com/sharer/sharer.php?u=https://youtu.be/VIDEO_ID_KAMU" target="_blank">Facebook</a>
      <a class="tt" href="https://www.tiktok.com/@jaguarmelayunews" target="_blank">TikTok</a>
    </div>
  </aside>

  <!-- Konten utama berita -->
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
          <div class="news-info">
            <h3><?= htmlspecialchars($p['judul']) ?></h3>
            <time datetime="<?= $p['tanggal'] ?>"><?= date('d M Y', strtotime($p['tanggal'])) ?></time>
          </div>
        </a>
      </article>
      <?php endwhile; ?>
    </section>
    <?php endforeach; ?>
  </div>

  <!-- Sidebar KANAN: Populer -->
  <aside class="sidebar">
    <h3>Berita Populer</h3>
    <ul class="popular-list">
      <?php foreach($populer as $pop): ?>
        <li><a href="artikel.php?slug=<?= urlencode($pop['slug']) ?>"><?= htmlspecialchars($pop['judul']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
