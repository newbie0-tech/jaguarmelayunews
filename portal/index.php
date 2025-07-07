<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

/* ----------------------------------------------
   Ambil daftar kategori & berita terbaru per kategori
   ---------------------------------------------- */
$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY FIELD(name,'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name");
while ($row = $res->fetch_assoc()) $categories[] = $row;

$categoryColors = [
    'Budaya Lokal' => '#20c997',
    'Daerah'       => '#0d6efd',
    'Dunia'        => '#6610f2',
    'Hukum'        => '#6f42c1',
    'Nasional'     => '#198754',
    'Pendidikan'   => '#fd7e14',
    'Politik'      => '#dc3545',
];
function catColor($name,$cmap){return $cmap[$name] ?? '#0d6efd';}
?>

<style>
.page-title{font-size:clamp(24px,3vw,32px);color:#6c4c35;margin:24px 0 8px;text-align:center;font-weight:700;}

/* ==== Grid 4 kolom kategori ==== */
.news-sections{display:grid;gap:32px;padding:0 32px 60px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));}
@media(min-width:1100px){.news-sections{grid-template-columns:repeat(4,1fr);} }
.section{background:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.05);padding:20px 18px 28px;}
.section h2{margin:0 0 16px;font-size:20px;font-weight:600;border-bottom:3px solid currentColor;padding-bottom:4px;}

.article{margin:0 0 20px;border-bottom:1px solid #e5e5e5;padding-bottom:12px;}
.article:last-child{border-bottom:none;}
.article h3{margin:0 0 6px;font-size:15px;font-weight:600;}
.article p{margin:0 0 8px;font-size:14px;color:#333;line-height:1.4;}
.article footer{font-size:12px;color:#555;display:flex;justify-content:space-between;flex-wrap:wrap;gap:6px;}
.badge{padding:2px 8px;border-radius:4px;font-size:11px;color:#fff;}
.read-btn{padding:4px 10px;border-radius:4px;font-size:12px;color:#fff;text-decoration:none;white-space:nowrap;}
.read-btn:hover{filter:brightness(.9);} 
</style>

<h1 class="page-title">Berita Terbaru</h1>

<div class="news-sections">
<?php foreach ($categories as $cat):
      // ambil 5 berita terbaru untuk kategori ini
      $postsStmt = $conn->prepare("SELECT id, judul, slug, LEFT(isi,150) AS snippet, tanggal FROM posts WHERE kategori_id=? ORDER BY tanggal DESC LIMIT 5");
      $postsStmt->bind_param('i',$cat['id']);
      $postsStmt->execute();
      $posts = $postsStmt->get_result();
      if ($posts->num_rows === 0) continue; // skip kosong
      $color = catColor($cat['name'],$categoryColors);
?>
    <section class="section" style="border-top:4px solid <?= $color ?>;">
        <h2 style="color:<?= $color ?>;"><?= htmlspecialchars($cat['name']) ?></h2>
        <?php while ($row = $posts->fetch_assoc()): ?>
            <article class="article">
                <h3><a href="artikel.php?slug=<?= urlencode($row['slug']) ?>"><?= htmlspecialchars($row['judul']) ?></a></h3>
                <p><?= htmlspecialchars($row['snippet']) ?>…</p>
                <footer>
                    <time datetime="<?= $row['tanggal'] ?>"><?= date('d M Y H:i',strtotime($row['tanggal'])) ?></time>
                    <a class="read-btn" style="background:<?= $color ?>;" href="artikel.php?slug=<?= urlencode($row['slug']) ?>">Baca →</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </section>
<?php endforeach; ?>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
