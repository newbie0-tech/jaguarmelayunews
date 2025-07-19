<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/header.php';

// Ambil ID kategori dari URL
$catId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Validasi kategori
$cat = $conn->prepare("SELECT name FROM categories WHERE id=?");
$cat->bind_param('i', $catId);
$cat->execute();
$cat->bind_result($catName);
if (!$cat->fetch()) {
  echo "<div class='container py-5'><h2 class='text-danger'>Kategori tidak ditemukan!</h2></div>";
  require_once __DIR__.'/inc/footer.php';
  exit;
}
$cat->close();

// Ambil daftar artikel dalam kategori
$stmt = $conn->prepare("SELECT judul, slug, gambar, tanggal FROM posts WHERE kategori_id=? AND status=1 ORDER BY tanggal DESC");
$stmt->bind_param('i', $catId);
$stmt->execute();
$posts = $stmt->get_result();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/portal/css/index.css">

<div class="container py-5">
  <h1 class="text-warning mb-4"><?= htmlspecialchars($catName) ?></h1>

  <?php if ($posts->num_rows < 1): ?>
    <p class="text-muted">Belum ada berita untuk kategori ini.</p>
  <?php else: ?>
    <div class="row g-4">
      <?php while ($p = $posts->fetch_assoc()): 
        $imgSrc = $p['gambar'] ?: 'assets/placeholder.jpg';
        $imgFull = (strpos($imgSrc, 'http') === 0) ? $imgSrc : '/portal/' . ltrim($imgSrc, '/');
      ?>
        <div class="col-md-6 col-lg-4">
          <div class="card bg-dark text-light h-100 shadow">
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
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
