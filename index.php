
<?php
// Redirect permanen (HTTP 302 → 307 tergantung browser)
// Ganti '/berita/login.php' jika jalurnya berbeda.
header('Location: /portal/index.php', true, 302);
exit;   // pastikan tak ada output lain setelah ini
