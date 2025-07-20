<?php
header('Content-Type: text/html; charset=utf-8');
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
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12738.460381621152!2d101.451355!3d0.507068!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5abff5e5a6c6d%3A0x8d49fc1a19d2337c!2sPekanbaru!5e0!3m2!1sid!2sid!4v1629111234567"
  width="600"
  height="450"
  style="border:0;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
    </div>
    <p class="small text-light">Alamat lengkap: Jl. Lintas Timur KM 18-Kelurahan Tenayan Raya, Pekanbaru, Riau</p>
  </div>
<h1>Kontak Kami</h1>
    <div class="kontak-info">
      <p><strong>Alamat:</strong> 
        <a href="https://www.google.com/maps?q=Jalan+Lintas+Timur+KM+16+Kulim,+Tenayan+Raya,+Pekanbaru,+Riau" target="_blank">
          Jalan Lintas Timur KM 18, Kulim, Tenayan Raya, Kota Pekanbaru – Riau
        </a>
      </p>
      <p><strong>Email:</strong> 
        <a href="mailto:linkidindonesia@gmail.com">linkidindonesia@gmail.com</a>
      </p>
      <p><strong>Telepon / WhatsApp:</strong> 
        <a href="https://wa.me/62895" target="_blank">Whatsapp</a>
      </p>
      <p><strong>Jam Operasional:</strong> 
        Senin–Jumat, 09:00–17:00 WIB<br>
        Sabtu–Minggu, 10:00–14:00 WIB
      </p>
      <p><strong>Redaksi:</strong> JaguarMelayuNews.com – Media Independen Rakyat Melayu</p>
<?php require_once __DIR__.'/inc/footer.php'; ?>
</body>
</html>
