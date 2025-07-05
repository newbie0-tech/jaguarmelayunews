<?php
// tentang.php – Halaman “Tentang Kami”
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

// Ambil daftar reporter/editor
$team = $conn->query("SELECT fullname, username, created_at FROM users WHERE role = 'editor' ORDER BY fullname")?->fetch_all(MYSQLI_ASSOC) ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tentang Kami – Jaguar Melayu News</title>
  <link rel="stylesheet" href="/portal/style.css">
  <style>
    .about-wrap{max-width:900px;margin:40px auto;padding:0 12px;line-height:1.7;font-size:16px;color:#333;background:#fff;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.05);} 
    .about-wrap h1{margin-top:0;color:#0057b8;padding-top:28px;text-align:center;}
    .about-wrap p{margin:18px 0;}
    .team{margin:32px 0 48px;}
    .team h2{font-size:20px;color:#0057b8;margin-bottom:12px;}
    .team ul{list-style:none;padding:0;display:grid;gap:10px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));}
    .team li{background:#f6f9ff;border:1px solid #dde5ff;border-radius:6px;padding:12px;text-align:center;}
  </style>
</head>
<body>
<div class="about-wrap">
  <h1>Tentang Kami</h1>
  <p><strong>Jaguar Melayu News</strong> adalah portal berita independen yang berkomitmen menyajikan informasi akurat, seimbang, dan mendalam seputar <em>Budaya Lokal</em>, peristiwa <em>Nasional</em>, hingga perkembangan <em>Dunia</em>. Didirikan pada 2025, kami hadir untuk memberikan perspektif Melayu yang inklusif dan progresif bagi para pembaca.</p>

  <p>Misi kami sederhana: <em>memberikan fakta, memperkaya wawasan, dan menginspirasi perubahan positif</em>. Tim redaksi kami bekerja 24/7 memverifikasi setiap data, menulis dengan integritas, serta menjunjung tinggi Kode Etik Jurnalistik.</p>

  <?php if($team): ?>
  <section class="team">
    <h2>Tim Reporter &amp; Editor</h2>
    <ul>
      <?php foreach($team as $t): ?>
        <li>
          <strong><?= htmlspecialchars($t['fullname'] ?: $t['username']) ?></strong><br>
          <span style="font-size:13px;color:#555;">bergabung sejak <?= date('M Y', strtotime($t['created_at'])) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <p style="padding-bottom:32px;text-align:center;">Kontak redaksi: <a href="mailto:redaksi@jaguarmelayunews.id">redaksi@jaguarmelayunews.id</a></p>
</div>
</body>
</html>
<?php require_once __DIR__.'/inc/footer.php'; ?>
