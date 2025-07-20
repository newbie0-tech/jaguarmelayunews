<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$categories = $conn->query("SELECT id, name FROM categories 
  ORDER BY FIELD(name, 'Budaya Lokal','Daerah','Dunia','Hukum','Nasional','Pendidikan','Politik'), name")->fetch_all(MYSQLI_ASSOC);

$populer = $conn->query("SELECT judul, slug FROM posts WHERE status=1 ORDER BY views DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Map Media</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/portal/css/index.css">
  <style>
    body {
      background: radial-gradient(ellipse at center, #081a29 0%, #000 100%);
      color: #f8f9fa;
    }

    .page-wrap {
      max-width: 960px;
      margin: 40px auto;
      padding: 16px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 16px;
      box-shadow: 0 0 24px rgba(0, 255, 255, 0.3);
    }

    h1 {
      color: #00ffff;
      text-align: center;
      text-shadow: 0 0 10px #0ff;
    }

    .map-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 0 12px #00ffff;
    }

    iframe {
      width: 100%;
      height: 440px;
      border: none;
    }

    .radar-icon {
      display: block;
      margin: 0 auto 20px;
      width: 80px;
      animation: pulse 2s infinite ease-in-out;
      opacity: 0.8;
    }

    @keyframes pulse {
      0% { transform: scale(1); opacity: 0.6; }
      50% { transform: scale(1.1); opacity: 1; }
      100% { transform: scale(1); opacity: 0.6; }
    }
  </style>
</head>
<body>

  <div class="page-wrap text-center">
    <img src="/portal/assets/radar-icon.svg" alt="Radar" class="radar-icon">
    <h1>Lokasi & Map Media</h1>
    <p>Kantor pusat Jaguar Melayu News dapat ditemukan di lokasi berikut:</p>
    <div class="map-container my-4">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18..."
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
    <p class="small text-light">Alamat lengkap: Jl. Lintas Timur KM 18-Kelurahan Tenayan Raya, Pekanbaru, Riau</p>
  </div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
</body>
</html>
