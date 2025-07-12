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
$sliderPosts = $conn->query("SELECT slug, gambar, judul FROM posts WHERE status=1 ORDER BY tanggal DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="/portal/css/style.css">
<div class="page-wrapper">
  <h1 class="page-title">Berita Terbaru</h1>

  <div class="main-grid-3">
    <!-- Kolom 1: YouTube -->
    <aside class="sidebar youtube-box">
      <h3>Jaguar Channel</h3>
      <div class="youtube-wrapper">
        <iframe src="https://www.youtube.com/embed/pG0RgDw55kI"
          title="Jaguar Channel"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen></iframe>
      </div>
    </aside>

    <!-- Kolom 2: Slider Berita -->
    <div class="slider-box">
      <h3 class="section-title">Headline Terbaru</h3>
      <div class="slider">
        <div class="slider-track">
          <?php foreach($sliderPosts as $s): 
            $src = '/portal/' . ltrim($s['gambar'] ?: 'assets/placeholder.jpg', '/');
          ?>
            <a href="artikel.php?slug=<?= urlencode($s['slug']) ?>">
              <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($s['judul']) ?>">
            </a>
          <?php endforeach; ?>
        </div>
        <div class="slider-controls">
          <button onclick="prevSlide()">&#10094;</button>
          <button onclick="nextSlide()">&#10095;</button>
        </div>
      </div>
    </div>

    <!-- Kolom 3: Iklan + Populer -->
    <aside class="sidebar iklan-box">
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

  <!-- Konten Utama Kategori -->
  <div class="news-content">
    <?php foreach($categories as $cat):
      $stmt = $conn->prepare("SELECT judul,slug,gambar,tanggal FROM posts WHERE kategori_id=? AND status=1 ORDER BY tanggal DESC LIMIT 5");
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
setInterval(nextSlide, 5000);
</script>

<?php require_once __DIR__.'/inc/footer.php'; ?>
