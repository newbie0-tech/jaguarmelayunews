<?php

require_once dirname(__DIR__).'/inc/db.php';
require_once dirname(__DIR__).'/inc/header.php';

$slug = $_GET['slug'] ?? '';
$slug = preg_replace('/[^a-z0-9-]/i','', $slug); // sanitize

$stmt = $conn->prepare("SELECT p.id, p.judul, p.isi, p.gambar, p.tanggal,
                               c.name AS kategori,
                               u.fullname AS penulis
                        FROM posts p
                        JOIN categories c ON c.id = p.kategori_id
                        LEFT JOIN users u  ON u.id = p.penulis_id
                        WHERE p.slug = ? LIMIT 1");
$stmt->bind_param('s',$slug);
$stmt->execute();
$artikel = $stmt->get_result()->fetch_assoc();

if(!$artikel){
    echo "<main style='max-width:800px;margin:60px auto 120px;text-align:center'><h1>404</h1><p>Artikel tidak ditemukan.</p></main>";
    require_once __DIR__.'/inc/footer.php';
    exit;
}
?>
<!-- === PROGRESS BAR ATAS === -->
<div id="readingProgressBar"></div>
<style>
#readingProgressBar{position:fixed;top:0;left:0;height:4px;background:#0057b8;width:0%;z-index:9999;transition:width .2s ease;}
.share-buttons{margin:18px 0;display:flex;gap:8px;flex-wrap:wrap}
.share-buttons a{padding:8px 14px;border-radius:4px;font-size:14px;color:#fff;text-decoration:none;display:inline-block}
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
<main style="max-width:900px;margin:30px auto;padding:0 12px;">
  <article>
    <h1 style="margin-top:0;color:#0057b8;"><?=htmlspecialchars($artikel['judul'])?></h1>
    <p style="color:#666;font-size:14px;margin-top:4px;">
      <span style="background:#eee;padding:2px 6px;border-radius:3px;font-weight:600;"><?=htmlspecialchars($artikel['kategori'])?></span>
      • <?=date('d M Y H:i',strtotime($artikel['tanggal']))?>
      <?php if($artikel['penulis']):?> • <?=htmlspecialchars($artikel['penulis'])?><?php endif;?>
    </p>

    <!-- SHARE BUTTONS -->
    <div class="share-buttons">
      <a class="share-wa" href="https://wa.me/?text=<?=urlencode($artikel['judul'].' - https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>" target="_blank">WhatsApp</a>
      <a class="share-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>" target="_blank">Facebook</a>
      <a class="share-tg" href="https://t.me/share/url?url=<?=urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>&text=<?=urlencode($artikel['judul'])?>" target="_blank">Telegram</a>
    </div>

    <?php if($artikel['gambar']):?>
      <img src="/portal/<?=htmlspecialchars($artikel['gambar'])?>" alt="Cover" style="max-width:100%;margin:18px 0;border-radius:6px;">
    <?php endif;?>

    <div style="font-size:16px;line-height:1.6;"> <?= nl2br($artikel['isi']) ?> </div>
  </article>
</main>
<?php require_once __DIR__.'/inc/footer.php'; ?>
