<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

/* ── ambil kategori ───────────────────────────────────── */
$categories = [];
$q = "SELECT id,name
      FROM categories
      ORDER BY FIELD(name,'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name";
$r = $conn->query($q);
while($row=$r->fetch_assoc()) $categories[]=$row;

$colorMap = [
  'Budaya Lokal'=>'#20c997','Daerah'=>'#0d6efd','Dunia'=>'#6610f2',
  'Hukum'=>'#6f42c1','Nasional'=>'#198754','Pendidikan'=>'#fd7e14','Politik'=>'#dc3545'
];
function catColor($n,$m){return $m[$n]??'#0d6efd';}
?>
<link rel="stylesheet" href="/portal/css/index.css">

<h1 class="page-title">Berita Terbaru</h1>

<div class="news-sections">
<?php foreach($categories as $cat):
      /* untuk tiap kategori ambil 5 posting terakhir */
      $stmt=$conn->prepare("
        SELECT judul,slug,gambar,tanggal
        FROM posts
        WHERE kategori_id=? AND status=1
        ORDER BY tanggal DESC
        LIMIT 5");
      $stmt->bind_param('i',$cat['id']);
      $stmt->execute();
      $posts=$stmt->get_result();
      if(!$posts->num_rows) continue;
      $cColor=catColor($cat['name'],$colorMap);
?>
  <section class="section" style="--cat-clr:<?= $cColor ?>">
    <h2><?= htmlspecialchars($cat['name']) ?></h2>

    <?php while($p=$posts->fetch_assoc()): ?>
      <article class="news-card">
        <a href="artikel.php?slug=<?= urlencode($p['slug']) ?>">
          $imgRel = $p['gambar'] ?: 'assets/placeholder.jpg';    
$imgSrc = '/portal/' . ltrim($imgRel, '/');             
?>
<img src="<?= htmlspecialchars($imgSrc) ?>"
     alt="<?= htmlspecialchars($p['judul']) ?>">
          <h3><?= htmlspecialchars($p['judul']) ?></h3>
        </a>
        <time datetime="<?= $p['tanggal'] ?>">
          <?= date('d M Y',strtotime($p['tanggal'])) ?>
        </time>
      </article>
    <?php endwhile; ?>

  </section>
<?php endforeach; ?>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
