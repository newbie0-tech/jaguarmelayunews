<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

/* ── ambil slug & sanitasi ───────────────────────────── */
$slug = preg_replace('/[^a-z0-9-]/i', '', ($_GET['slug'] ?? ''));

/* ── ambil artikel + kategori + penulis ──────────────── */
$stmt = $conn->prepare("
  SELECT p.id, p.judul, p.isi, p.gambar, p.tanggal,
         c.name  AS kategori,
         u.fullname AS penulis
  FROM   posts p
  JOIN   categories c ON c.id = p.kategori_id
  LEFT   JOIN users u ON u.id = p.penulis_id
  WHERE  p.slug = ?
  LIMIT  1");
$stmt->bind_param('s', $slug);
$stmt->execute(); $artikel = $stmt->get_result()->fetch_assoc();

if (!$artikel) {
  echo "<main style='max-width:800px;margin:60px auto;text-align:center'>
          <h1>404</h1><p>Artikel tidak ditemukan.</p></main>";
  require_once __DIR__.'/inc/footer.php'; exit;
}

/* ── siapkan data OG ─────────────────────────────────── */
$baseUrl = 'https://'.$_SERVER['HTTP_HOST'];
$absUrl  = $baseUrl.'/artikel.php?slug='.urlencode($slug);

$imgRel  = $artikel['gambar'] ?: 'assets/placeholder.jpg';
$imgAbs  = $baseUrl.'/portal/'.ltrim($imgRel, '/');

$description = mb_substr(strip_tags($artikel['isi']), 0, 150).'…';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($artikel['judul']) ?> – Jaguar Melayu News</title>

<!-- ‑‑‑ Open Graph ‑‑‑ -->
<meta property="og:type"        content="article">
<meta property="og:url"         content="<?= $absUrl ?>">
<meta property="og:title"       content="<?= htmlspecialchars($artikel['judul']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:image"       content="<?= $imgAbs ?>">
<meta property="og:image:type"  content="image/jpeg">
<meta property="og:image:width" content="800">
<meta property="og:image:height"content="450">
<meta property="og:site_name"   content="Jaguar Melayu News">

<!-- ‑‑‑ Twitter Card ‑‑‑ -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?= htmlspecialchars($artikel['judul']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
<meta name="twitter:image"       content="<?= $imgAbs ?>">

<link rel="stylesheet" href="/portal/css/style.css">

<style>
#readingProgressBar{position:fixed;top:0;left:0;height:4px;background:#0057b8;width:0%;z-index:9999;transition:width .2s}
.share-buttons{margin:18px 0;display:flex;gap:8px;flex-wrap:wrap}
.share-buttons a{padding:8px 14px;border-radius:4px;font-size:14px;color:#fff;text-decoration:none}
.share-wa{background:#25d366}.share-fb{background:#3b5998}.share-tg{background:#0088cc}
</style>

<script>
document.addEventListener('DOMContentLoaded',()=>{
  const bar=document.getElementById('readingProgressBar');
  document.addEventListener('scroll',()=>{
    const st=document.documentElement.scrollTop||document.body.scrollTop;
    const dh=document.documentElement.scrollHeight-document.documentElement.clientHeight;
    bar.style.width=((st/dh)*100)+'%';
  });
});
</script>
</head>

<body>
<div id="readingProgressBar"></div>

<main style="max-width:900px;margin:30px auto;padding:0 12px">
  <article>
    <h1 style="margin-top:0;color:#0057b8"><?= htmlspecialchars($artikel['judul']) ?></h1>
    <p style="color:#666;font-size:14px;margin-top:4px">
      <span style="background:#eee;padding:2px 6px;border-radius:3px;font-weight:600">
        <?= htmlspecialchars($artikel['kategori']) ?>
      </span>
      • <?= date('d M Y H:i',strtotime($artikel['tanggal'])) ?>
      <?php if($artikel['penulis']): ?> • <?= htmlspecialchars($artikel['penulis']) ?><?php endif; ?>
    </p>

    <!-- tombol share -->
    <div class="share-buttons">
      <a class="share-wa"
         href="https://wa.me/?text=<?= urlencode($artikel['judul'].' – '.$absUrl) ?>"
         target="_blank" rel="noopener">WhatsApp</a>

      <a class="share-fb"
         href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($absUrl) ?>"
         target="_blank" rel="noopener">Facebook</a>

      <a class="share-tg"
         href="https://t.me/share/url?url=<?= urlencode($absUrl) ?>&text=<?= urlencode($artikel['judul']) ?>"
         target="_blank" rel="noopener">Telegram</a>
    </div>

    <?php if($imgRel): ?>
      <img src="/portal/<?= htmlspecialchars($imgRel) ?>"
           alt="Cover"
           style="max-width:100%;margin:18px 0;border-radius:6px">
    <?php endif; ?>

    <div style="font-size:16px;line-height:1.6">
      <?= nl2br($artikel['isi']) ?>
    </div>
  </article>
</main>

<?php require_once __DIR__.'/inc/footer.php'; ?>
