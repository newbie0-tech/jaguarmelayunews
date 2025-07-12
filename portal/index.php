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
<style>/* grid responsif kategori */
.news-sections{
  display:grid;
  gap:32px;
  padding:0 32px 60px;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
}
.youtube-mini {
  width: 100%;
  aspect-ratio: 16/9;
  border-radius: 6px;
  border: none;
}
.slider {
  position: relative;
  overflow: hidden;
  height: 250px;
}
.slider-track {
  display: flex;
  transition: transform 0.5s ease;
}
.slider img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 8px;
}
.slider-controls {
  position: absolute;
  top: 40%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 10px;
}
.slider-controls button {
  background: rgba(0,0,0,0.5);
  color: white;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
.iklan-box {
  height: 250px;
  background: #f3f3f3;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px dashed #aaa;
  border-radius: 8px;
  font-weight: bold;
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
<?php
$sliderPosts = $conn->query("SELECT slug, gambar, judul FROM posts WHERE status=1 ORDER BY tanggal DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
  <div class="main-grid">
  <!-- Kolom 1: YouTube -->
  <aside class="sidebar youtube-box">
    <h3>Jaguar Channel</h3>
    <div class="youtube-wrapper">
      <iframe src="https://www.youtube.com/embed/pG0RgDw55kI"
        title="Jaguar Channel"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen>
      </iframe>
    </div>
  </aside>

  <!-- Kolom 2: Berita Terbaru -->
  <div class="news-content">
    <h1 class="page-title">Berita Terbaru</h1>
    <!-- berita looping seperti biasa -->
    ...
  </div>

  <!-- Kolom 3: Iklan dan Populer -->
  <aside class="sidebar">
    <h3>Iklan</h3>
    <img src="/portal/assets/banner-iklan.png" alt="Iklan" style="width:100%; border-radius:8px; margin-bottom:20px;">
    
    <h3>Berita Populer</h3>
    <ul class="popular-list">
      <?php foreach($populer as $pop): ?>
        <li><a href="artikel.php?slug=<?= urlencode($pop['slug']) ?>"><?= htmlspecialchars($pop['judul']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>
</div>
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
<script>
let currentIndex = 0;
const slider = document.querySelector(".slider-track");
const slides = document.querySelectorAll(".slider-track a");
function updateSlider() {
  const width = slides[0].offsetWidth;
  slider.style.transform = `translateX(-${currentIndex * width}px)`;
}
function nextSlide() {
  currentIndex = (currentIndex + 1) % slides.length;
  updateSlider();
}
function prevSlide() {
  currentIndex = (currentIndex - 1 + slides.length) % slides.length;
  updateSlider();
}
setInterval(nextSlide, 5000); // auto-slide 5 detik
</script>

<?php require_once __DIR__.'/inc/footer.php'; ?>
