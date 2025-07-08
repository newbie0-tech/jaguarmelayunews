<?php
$title = $title ?? 'Jaguar Melayu News';
$activeCat = isset($_GET['k']) ? (int)$_GET['k'] : 0;
$categoriesNav = $breakingNews = [];
if (isset($conn) && $conn instanceof mysqli) {
  $sqlCat = "SELECT id,name FROM categories ORDER BY FIELD(name,'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'),name";
  if ($r = $conn->query($sqlCat)) $categoriesNav = $r->fetch_all(MYSQLI_ASSOC);
  $sqlBr  = "SELECT slug,judul FROM posts ORDER BY tanggal DESC LIMIT 5";
  if ($r = $conn->query($sqlBr))  $breakingNews  = $r->fetch_all(MYSQLI_ASSOC);
}
$categoryColorsNav = [
 'Budaya Lokal'=>'#20c997','Daerah'=>'#0d6efd','Dunia'=>'#6610f2','Hukum'=>'#6f42c1','Nasional'=>'#198754','Pendidikan'=>'#fd7e14','Politik'=>'#dc3545',
];
function navColor($n,$m){return $m[$n] ?? '#0d6efd';}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    body {font-family: Verdana, sans-serif; margin: 0; background: #f8f9fa;}
    header.site-header {
      position: relative;
      background: linear-gradient(90deg,#5b3e2a 0%,#b88e50 50%,#d4af37 100%);
      color: #fff;
      padding: 10px 20px;
    }
    header.site-header::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -18px;
      width: 100%;
      height: 18px;
      background: url('/portal/assets/') repeat-x center/auto 100%;
    }
    .header-inner {
      max-width: 1280px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .banner-logo {flex: 0 0 120px;}
    .banner-logo img {width: 120px; height: auto;}
    .site-title {
      margin: 0;
      font-size: 26px;
      font-weight: 700;
      letter-spacing: .4px;
      flex: 0 1 auto;
    }
    .separator {
      flex: 0 0 4px;
      background: #d4af37;
      height: 54px;
      border-radius: 2px;
      box-shadow: 0 0 4px rgba(0,0,0,.25);
    }
    .ad-slot {
      flex: 0 0 180px;
      text-align: center;
    }
    .ad-slot img {
      width: 180px;
      height: 100px;
      object-fit: cover;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,.15);
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0% {transform: translateY(0)}
      50% {transform: translateY(-4px)}
      100% {transform: translateY(0)}
    }
    .breaking-bar {
      background: #d4af37;
      color: #000;
      padding: 6px 0;
      overflow: hidden;
      white-space: nowrap;
      font-size: 14px;
    }
    .breaking-bar .label {
      background: #5b3e2a;
      color: #fff;
      padding: 4px 10px;
      margin-right: 10px;
      font-weight: 700;
      border-radius: 3px;
    }
    .breaking-track {
      display: inline-block;
      animation: scroll 22s linear infinite;
    }
    @keyframes scroll {
      0% {transform: translateX(100%)}
      100% {transform: translateX(-100%)}
    }
    .breaking-track a {
      color: #000;
      text-decoration: none;
      margin-right: 22px;
    }
    .category-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      padding: 10px 12px;
      justify-content: center;
      background: #fff;
      border-bottom: 1px solid #ccc;
    }
    .category-bar a {
      color: #fff;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: .88rem;
      text-decoration: none;
      white-space: nowrap;
    }
    @media(max-width:700px){
      .separator {display: none}
      .ad-slot {margin-top: 6px;}
    }
    .stroke-red {
  color: #fff;
  text-shadow: -1px -1px 0 red, 1px -1px 0 red, -1px 1px 0 red, 1px 1px 0 red;
}
.stroke-green {
  color: #fff;
  text-shadow: -1px -1px 0 green, 1px -1px 0 green, -1px 1px 0 green, 1px 1px 0 green;
}

  </style>
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <div class="banner-logo"><img src="/portal/assets/logo.png" alt="Logo"></div>
    <h2 class="site-title"><span class="stroke-red ; 1 px">Jaguar </span><span class="stroke-green ; 1 px">Melayu News</h2>
    <div class="separator"></div>

    <!-- Iklan terpisah -->
      <div class="ad-slot"><img src="/portal/assets/ads/iklan1.png"></div>
    <div class="ad-slot"><img src="/portal/assets/ads/iklan2.png" ></div>
    <div class="ad-slot"><img src="/portal/assets/ads/iklan3.png"></div>
         </div>
  </div>
</header>

<?php if ($breakingNews): ?>
<div class="breaking-bar"> <span class= "label ; stroke-red ; 3 px">
  Breaking News</span></div>
  <div class="breaking-bar"><div class="ticker-wrap">
    <div class="breaking-track">
      <?php foreach ($breakingNews as $n): ?>
        <a href="artikel.php?slug=<?= urlencode($n['slug']) ?>">
          <?= htmlspecialchars($n['judul']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<nav class="category-bar">
  <?php foreach ($categoriesNav as $c): ?>
    <a href="index.php?k=<?= $c['id'] ?>" style="background: <?= navColor($c['name'], $categoryColorsNav) ?>;">
      <?= htmlspecialchars($c['name']) ?>
    </a>
  <?php endforeach; ?>
</nav>
<!-- === SEARCH BAR (di bawah header) ====================== -->
<style>
  .search-bar{
    background:#fff;
    border-bottom:1px solid #ccc;
    padding:10px 16px;
  }
  .search-bar form{
    max-width:480px;
    margin:0 auto;
    display:flex;
  }
  .search-bar input[type="text"]{
    flex:1;
    padding:8px 12px;
    font-size:14px;
    border:1px solid #bbb;
    border-radius:4px 0 0 4px;
    outline:none;
  }
  .search-bar button{
    padding:8px 14px;
    border:none;
    background:#0057b8;
    color:#fff;
    font-size:14px;
    font-weight:600;
    border-radius:0 4px 4px 0;
    cursor:pointer;
  }
  .search-bar button:hover{background:#00408a;}
</style>

<div class="search-bar">
  <form action="/portal/search.php" method="get">
    <input type="text" name="q" placeholder="Cari artikel..." required>
    <button type="submit">Cari</button>
  </form>
</div>
<!-- ======================================================= -->
      <?php
// Ambil 5 berita terbaru untuk slider
$sliderPosts = [];
if (isset($conn) && $conn instanceof mysqli) {
  $q = "SELECT slug, judul, gambar FROM posts ORDER BY tanggal DESC LIMIT 5";
  if ($r = $conn->query($q)) $sliderPosts = $r->fetch_all(MYSQLI_ASSOC);
}
?>

<?php if (!empty($sliderPosts)): ?>
<!-- === SLIDER HEADLINE =================================== -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
.slider-wrap{margin:16px auto;max-width:980px}
.swiper{width:100%;border-radius:8px;overflow:hidden}
.swiper-slide{position:relative}
.swiper-slide img{width:100%;height:240px;object-fit:cover}
.slider-caption{position:absolute;bottom:0;left:0;right:0;padding:12px;background:rgba(0,0,0,0.6);color:#fff;font-size:16px;font-weight:bold;text-shadow:1px 1px 3px #000}
</style>

<div class="slider-wrap">
  <div class="swiper">
    <div class="swiper-wrapper">
      <?php foreach($sliderPosts as $s): ?>
        <div class="swiper-slide">
          <a href="artikel.php?slug=<?= urlencode($s['slug']) ?>">
            <img src="/portal/<?= $s['gambar'] ?: 'assets/default.jpg' ?>" alt="<?= htmlspecialchars($s['judul']) ?>">
            <div class="slider-caption"><?= htmlspecialchars($s['judul']) ?></div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  new Swiper('.swiper', {
    loop: true,
    autoplay: { delay: 5000 },
    speed: 500,
  });
</script>
<!-- ======================================================= -->
<?php endif; ?>

