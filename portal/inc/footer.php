<?php
// --- inc/footer.php ---
?>
<?php
<?php
/* ===== Hitung visitor sederhana ===== */
$statsDir = '/data';                  // volume Railway
if (!is_dir($statsDir)) mkdir($statsDir, 0755, true);

$file = $statsDir.'/visitors.txt';

$visits = 0;
if (is_readable($file)) {
    $visits = (int) file_get_contents($file);
}
$visits++;
file_put_contents($file, $visits, LOCK_EX);
?>
<style>
    footer.site-footer{
        background:linear-gradient(90deg,#000 0%, #0d6efd 100%);
        color:#fff;
        padding:32px 16px 24px;
        font-size:14px;
        margin-top:48px;
    }
    .site-footer .container{max-width:1280px;margin:0 auto;display:flex;flex-wrap:wrap;gap:32px;justify-content:space-between;}
    .footer-col{min-width:200px;flex:1 1 200px;}
    .footer-col h3{margin:0 0 12px;font-size:16px;}
    .footer-col ul{list-style:none;padding:0;margin:0;}
    .footer-col li{margin-bottom:6px;}
    .footer-col a{color:#fff;text-decoration:none;opacity:.9;}
    .footer-col a:hover{opacity:1;text-decoration:underline;}
    .social-icons a{display:inline-block;margin-right:10px;opacity:.8;transition:opacity .2s;}
    .social-icons a:hover{opacity:1;}
    .social-icons img{width:36px;height:36px;filter:invert(1);} /* ganti 50→36 px biar rapi */
    .footer-logo img{width:110px;margin-bottom:12px;}
    .copyright{text-align:center;margin-top:32px;font-size:12px;color:rgba(255,255,255,.85);}    
    /* mobile */
    @media(max-width:600px){.site-footer .container{flex-direction:column;gap:24px;text-align:center;}.social-icons a{margin-right:6px;}}
</style>
<footer class="site-footer">
  <div class="container">
    <div class="footer-col footer-logo">
      <img src="/portal/assets/logo.png" alt="Logo Jaguar Melayu News">
      <p>Portal berita independen yang menyajikan informasi terkini—lokal, nasional, hingga mancanegara.</p>
    </div>
    <div class="footer-col">
      <h3>Navigasi</h3>
      <ul>
        <li><a href="/portal/">Beranda</a></li>
        <li><a href="/portal/tentang.php">Tentang Kami</a></li>
        <li><a href="/portal/visi-misi.php">Visi &middot; Misi</a></li>
        <li><a href="/portal/map-media.php">Map Media</a></li>
        <li><a href="/portal/kontak.php">Alamat / Kontak</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h3>Sosial Media</h3>
      <div class="social-icons">
        <a href="https://facebook.com" target="_blank"><img src="/portal/assets/fb.png" alt="Facebook"></a>
        <a href="https://tiktok.com" target="_blank"><img src="/portal/assets/tk.png" alt="TikTok"></a>
        <a href="https://instagram.com" target="_blank"><img src="/portal/assets/ig.png" alt="Instagram"></a>
      </div>
    </div>
  </div>
  <div class="copyright">
  © <?= date('Y') ?> Jaguar Melayu News – All rights reserved.<br>
  <span style="font-size:13px;color:#ddd;">
    <footer> Pengunjung: <?= $visits ?> </footer>
  </span>
</div>

</footer>

</body>
</html>
