<?php
session_start();
if (!isset($_SESSION['admin']) || !is_numeric($_SESSION['admin'])) {
  header('Location: login.php'); exit;
}
require_once __DIR__.'/../inc/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$MAX_UPLOAD = 5 * 1024 * 1024;
$uploadDir = __DIR__ . '/../data/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$msg = '';
$judul = $isi = $tags = '';
$katID = 0; $slug = ''; $status = 1;
$gambar = 'assets/placeholder.jpg';

function make_slug($str) {
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)), '-');
}

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul  = trim($_POST['judul'] ?? '');
  $isi    = $_POST['isi'] ?? '';
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = ($_POST['status'] ?? '1') === '1' ? 1 : 0;
  $slug   = make_slug($judul);

  // Cek slug unik
  $cek = $conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $cek->bind_param("s", $slug);
  $cek->execute(); $cek->bind_result($count); $cek->fetch(); $cek->close();
  if ($count > 0) $slug .= '-' . time();

  // Gambar
  if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $size = $_FILES['gambar']['size'];
    $allow = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allow)) $msg = 'Format gambar tidak valid';
    elseif ($size > $MAX_UPLOAD) $msg = 'Ukuran gambar maksimal 5MB';
    elseif ($_FILES['gambar']['error']) $msg = 'Terjadi kesalahan saat upload gambar';
    else {
      $fname = time().'_'.rand(100,999).".$ext";
      $dest  = "$uploadDir/$fname";
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        $gambar = 'uploads/'.$fname;
      } else {
        $msg = 'Gagal menyimpan gambar';
      }
    }
  }

  if (!$msg && $judul && $isi && $katID) {
    $stmt = $conn->prepare("INSERT INTO posts (judul,slug,isi,tags,status,gambar,kategori_id,penulis_id) VALUES (?,?,?,?,?,?,?,?)");
    $penulis = (int)$_SESSION['admin'];
    $stmt->bind_param("ssssisii", $judul, $slug, $isi, $tags, $status, $gambar, $katID, $penulis);

    try {
      if ($stmt->execute()) {
        $msg = "Berita berhasil disimpan!";
        $judul = $isi = $tags = ''; $katID = 0; $status = 1;
      } else {
        $msg = "Gagal menyimpan berita.";
      }
    } catch (Exception $e) {
      $msg = "Kesalahan: " . $e->getMessage();
    }
    $stmt->close();
  } elseif (!$msg) {
    $msg = "Semua field wajib diisi.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Berita</title>
  <link rel="stylesheet" href="/portal/css/tambah_berita.css">
  <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // CKEditor init
      ClassicEditor.create(document.querySelector('#isi'))
        .catch(err => console.error(err));

      // Slug otomatis
      const judul = document.getElementById('judul');
      const slug  = document.getElementById('slug');
      judul.addEventListener('input', () => {
        slug.value = judul.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      });

      // Preview gambar
      const file = document.getElementById('gambar');
      const prev = document.getElementById('prev');
      const note = document.getElementById('note');
      file.addEventListener('change', () => {
        const f = file.files[0];
        if (!f) return;
        if (f.size > <?= $MAX_UPLOAD ?>) {
          alert("Ukuran gambar > 5MB");
          file.value = '';
          prev.style.display = 'none';
          note.textContent = '';
        } else {
          prev.src = URL.createObjectURL(f);
          prev.style.display = 'block';
          note.textContent = Math.round(f.size / 1024) + ' KB';
        }
      });
    });
  </script>
</head>
<body>
<div class="form-wrapper">
  <h1>Tambah Berita</h1>
  <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Judul</label>
      <input id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" required>
    </div>
    <div class="form-group">
      <label>Slug</label>
      <input id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" readonly>
    </div>
    <div class="form-group">
      <label>Kategori</label>
      <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $katID == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Isi Berita</label>
      <textarea id="isi" name="isi"><?= htmlspecialchars($isi) ?></textarea>
    </div>
    <div class="form-group">
      <label>Gambar Utama</label>
      <input type="file" id="gambar" name="gambar" accept="image/*">
      <img id="prev" class="preview-img" style="display:none">
      <div id="note" class="note"></div>
    </div>
    <div class="form-group">
      <label>Tags (pisah koma)</label>
      <input name="tags" value="<?= htmlspecialchars($tags) ?>">
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Publish</option>
        <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Draft</option>
      </select>
    </div>
    <button class="btn-submit">Simpan</button>
  </form>

  <div style="text-align:center;margin-top:20px">
    <a href="/portal/admin/dashboard.php" class="back-link">← Kembali ke Dashboard</a>
  </div>
</div>
<?php require_once __DIR__.'/../inc/footer.php'; ?>
</body>
</html>
