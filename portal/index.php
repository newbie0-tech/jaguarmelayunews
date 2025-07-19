<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$categories = $conn->query("SELECT id, name FROM categories 
  ORDER BY FIELD(name, 'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name")->fetch_all(MYSQLI_ASSOC);

$populer = $conn->query("SELECT judul, slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/portal/css/index.css">

<div class="container py-4">
  <div class="text-center mb-4">
    <h3 class="text-warning">Jaguar Channel</h3>
    <div class="ratio ratio-16x9">
      <iframe src="https://www.youtube.com/embed/pG0RgDw55kI" allowfullscreen></iframe>
    </div>
  </div>
<!-- Kategori Navigasi -->
<nav class="category-nav text-center mb-4">
  <?php foreach ($categories as $cat): ?>
    <a href="/portal/kategori.php?id=<?= $cat['id'] ?>" class="btn btn-outline-warning mx-1 mb-2">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endforeach; ?>
</nav>

  <h1 class="text-center text-warning mb-4">Berita Terbaru</h1>

  <div class="row">
    <div class="col-lg-9">
      <?php foreach ($categories as $cat): 
        $catId = $cat['id'];
        $catName = $cat['name'];
        $stmt = $conn->prepare("SELECT judul, slug, gambar, tanggal FROM posts WHERE kategori_id=? AND status=1 ORDER BY tanggal DESC LIMIT 4");
        $stmt->bind_param('i', $catId);
        $stmt->execute();
        $posts = $stmt->get_result();
        if ($posts->num_rows < 1) continue;
      ?>
        <section class="mb-4">
          <h2 class="text-warning mb-3"><?= htmlspecialchars($catName) ?></h2>
          <div class="row g-3">
            <?php while ($p = $posts->fetch_assoc()):
              $imgSrc = $p['gambar'] ?: 'assets/placeholder.jpg';
              $imgFull =  (strpos($imgSrc, 'http') === 0) ? $imgSrc : '/portal/' . ltrim($imgSrc, '/');
            ?>
              <div class="col-md-6">
                <div class="card bg-dark text-light h-100">
                  <a href="artikel.php?slug=<?= urlencode($p['slug']) ?>">
                    <img src="<?= htmlspecialchars($imgFull) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['judul']) ?>">
                  </a>
                  <div class="card-body">
                    <h5 class="card-title">
                      <a href="artikel.php?slug=<?= urlencode($p['slug']) ?>" class="text-warning text-decoration-none">
                        <?= htmlspecialchars($p['judul']) ?>
                      </a>
                    </h5>
                    <p class="card-text">
                      <small class="text-muted"><?= date('d M Y', strtotime($p['tanggal'])) ?></small>
                    </p>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>

    <div class="col-lg-3">
      <div class="bg-dark p-3 rounded mb-4">
        <h4 class="text-warning">Berita Populer</h4>
        <ul class="list-unstyled overflow-auto" style="max-height: 150px;">
          <?php foreach ($populer as $pop): ?>
            <li class="mb-2">
              <a href="artikel.php?slug=<?= urlencode($pop['slug']) ?>" class="text-light text-decoration-none">
                <?= htmlspecialchars($pop['judul']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
<div class="d-flex flex-column gap-3 sticky-top" style="top: 100px;">
  <?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="text-center bg-light rounded shadow-sm p-2">
      <img src="/portal/uploads/iklan<?= $i ?>.jpg" alt="Iklan <?= $i ?>" class="img-fluid rounded" onerror="this.style.display='none'">
    </div>
  <?php endfor; ?>
</div>
</div>
  </div>
       <?php require_once __DIR__.'/inc/footer.php'; ?>
