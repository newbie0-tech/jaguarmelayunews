<link rel="stylesheet" href="/portal/css/header.css">
<header class="site-header">
  <div class="top-bar">
    <div class="logo-title">
      <img src="/portal/assets/logo.png" alt="Logo" class="logo">
      <h1 class="site-title">
        <span class="stroke-red">Jaguar</span>
        <span class="stroke-green">Melayu News</span>
      </h1>
    </div>

    <div class="search-social">
      <form action="/portal/search.php" method="get" class="search-form">
        <input type="text" name="q" placeholder="Cari berita...">
        <button type="submit">🔍</button>
      </form>
      <div class="social-icons">
        <a href="https://www.facebook.com"><img src="/portal/assets/icons/fb.ico" alt="Facebook"></a>
        <a href="https://www.x.com"><img src="/portal/assets/icons/x.png" alt="Twitter/X"></a>
        <a href="hhttps://www.youtube.com"><img src="/portal/assets/icons/yt.png" alt="YouTube"></a>
      </div>
    </div>
  </div>

 <div class="breaking-marquee">
  <span class="breaking-flash">🚨 Breaking News</span>
  <marquee scrollamount="6">
    <?php
    $result = $conn->query("SELECT slug, judul FROM posts WHERE status=1 ORDER BY id DESC LIMIT 5");
    while ($row = $result->fetch_assoc()) {
      echo '<a href="/artikel.php?slug='.htmlspecialchars($row['slug']).'">'.htmlspecialchars($row['judul']).'</a> • ';
    }
    ?>
  </marquee>
</div>

  <nav class="main-nav">
    <a href="/portal/">Beranda</a>
    <a href="https://jaguarmelayunews-production.up.railway.app/jurnalis.php">Pendaftaran Jurnalis</a>
    <a href="https://jaguarmelayunews-production.up.railway.app/sk.jpg">Legalitas</a>
    <a href="https://jaguarmelayunews-production.up.railway.app/kontak.php">marketing</a>
    
  </nav>
</header>
