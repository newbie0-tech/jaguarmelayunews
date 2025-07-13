<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

// ── ambil kategori ─
$categories = [];
$q = "SELECT id,name FROM categories
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

<link rel="stylesheet" href="css/index.css">

<div class="page-wrapper">
  <h1 class="page-title">Berita Terbaru</h1>

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

  <!-- YouTube Embed -->
  <div class="youtube-embed">
    <h3 style="font-size:22px; margin-bottom:12px; color:#d00000;">Jaguar Channel</h3>
    <iframe src="https://www.youtube.com/embed/pG0RgDw55kI"
      title="Jaguar Channel"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      allowfullscreen>
    </iframe>
  </div>

</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
