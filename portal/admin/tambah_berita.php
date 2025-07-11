<?php
session_start();
if (!isset($_SESSION['admin']) || !is_numeric($_SESSION['admin'])) {
  header('Location: login.php'); exit;
}

require_once __DIR__ . '/../inc/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg = '';
$judul = $isi = $tags = '';
$katID = 0;
$status = 1;
$slug = '';
$MAX_UPLOAD = 5 * 1024 * 1024;
$uploadDir = realpath(__DIR__ . '/portal/uploads');

if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true); // buat folder jika belum ada
}

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

function make_slug($str) {
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)), '-');
}
echo "<pre>";
var_dump($_POST);
var_dump($_FILES);
echo "</pre>";
exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul  = trim($_POST['judul'] ?? '');
  $isi    = $_POST['isi'] ?? '';
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = ($_POST['status'] ?? '1') === '1' ? 1 : 0;
  $slug   = make_slug($judul);

  // cek slug unik
  $check = $conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $check->bind_param('s', $slug);
  $check->execute();
  $check->bind_result($cnt);
  $check->fetch();
  $check->close();
  if ($cnt > 0) $slug .= '-' . time();

  // proses upload gambar
  $gambar = '';
  if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp'];
    $size = $_FILES['gambar']['size'];

    if (!in_array($ext, $allow))        $msg = 'Ekstensi gambar harus jpg/jpeg/png/webp';
    elseif ($size > $MAX_UPLOAD)        $msg = 'Ukuran gambar maksimal 5 MB';
    elseif ($_FILES['gambar']['error']) $msg = 'Terjadi error saat upload';
    else {
      $fname = time() . '_' . rand(100,999) . ".$ext";
      $dest = $uploadDir . '/' . $fname;
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        $gambar = 'uploads/' . $fname;
      } else {
        $msg = 'Gagal menyimpan gambar.';
      }
    }
  }

  if (!$gambar) $gambar = 'assets/placeholder.gif';

  if (!$msg && $judul && $isi && $katID) {
    $stmt = $conn->prepare("INSERT INTO posts (judul, slug, isi, tags, status, gambar, kategori_id, penulis_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $penulis = (int)$_SESSION['admin'];
    $stmt->bind_param('ssssisii', $judul, $slug, $isi, $tags, $status, $gambar, $katID, $penulis);
    try {
      if ($stmt->execute()) {
        $msg = '✅ Berita berhasil disimpan.';
        $judul = $isi = $tags = ''; $katID = 0; $status = 1; $slug = '';
      } else {
        $msg = '❌ Gagal menambahkan berita.';
      }
    } catch (mysqli_sql_exception $e) {
      $msg = '❌ Error: ' . $e->getMessage();
    }
    $stmt->close();
  } elseif (!$msg) {
    $msg = '❌ Semua field wajib diisi.';
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah Berita</title>
  <link rel="stylesheet" href="/portal/css/tambah_berita.css">
  <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
  <style>
    .form-wrapper { max-width: 880px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 14px rgba(0,0,0,.08); }
    .form-group { margin-bottom: 22px; }
    label { font-weight: 600; margin-bottom: 8px; display: block; font-size: 15px; }
    input, select, textarea { width: 100%; padding: 14px; font-size: 15px; border: 1px solid #ccc; border-radius: 4px; }
    textarea { min-height: 380px; }
    .btn-submit { padding: 12px 28px; background: #0057b8; color: #fff; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { background: #00408a; }
    .alert { background: #e9ffe9; color: #155724; padding: 12px 16px; border-radius: 4px; margin-bottom: 22px; text-align: center; }
    .preview-img { max-width: 260px; margin-top: 8px; border: 1px solid #ddd; border-radius: 4px }
    .note { font-size: 12px; color: #666; margin-top: 4px; }
  </style>
</head>
<body>
<div class="form-wrapper">
  <h1 style="text-align:center;color:#0057b8;margin-top:0">Tambah Berita</h1>
  <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="form-group"><label>Judul</label><input id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" required></div>
    <div class="form-group"><label>Slug</label><input id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" readonly></div>
    <div class="form-group"><label>Kategori</label>
      <select name="kategori" required>
        <option value="">--Pilih Kategori--</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $katID == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Isi Berita</label>
      <textarea id="isi" name="isi" required><?= htmlspecialchars($isi) ?></textarea>
    </div>
    <div class="form-group"><label>Gambar Utama (opsional, max 5MB)</label>
      <input type="file" id="gambar" name="gambar" accept="image/*">
      <img id="prev" class="preview-img" style="display:none">
      <div id="note" class="note"></div>
    </div>
    <div class="form-group"><label>Tags</label><input name="tags" value="<?= htmlspecialchars($tags) ?>"></div>
    <div class="form-group"><label>Status</label>
      <select name="status">
        <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Publish</option>
        <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Draft</option>
      </select>
    </div>
    <button class="btn-submit">Simpan</button>
  </form>
</div>

<script>
function slugify(str) {
  return str.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
}

document.addEventListener('DOMContentLoaded', () => {
  const judul = document.getElementById('judul');
  const slug  = document.getElementById('slug');
  const file  = document.getElementById('gambar');
  const prev  = document.getElementById('prev');
  const note  = document.getElementById('note');

  judul.addEventListener('input', () => slug.value = slugify(judul.value));

  file.addEventListener('change', () => {
    const f = file.files[0];
    if (!f) return;
    if (f.size > <?= $MAX_UPLOAD ?>) {
      alert('Ukuran gambar > 5 MB');
      file.value = '';
      prev.style.display = 'none';
      note.textContent = '';
      return;
    }
    prev.src = URL.createObjectURL(f);
    prev.style.display = 'block';
    note.textContent = Math.round(f.size / 1024) + ' KB';
  });

  ClassicEditor.create(document.querySelector('#isi'), {
    toolbar: ['heading','|','bold','italic','underline','|','link','bulletedList','numberedList','|','insertTable','mediaEmbed','undo','redo']
  }).catch(error => console.error(error));
});
</script>
</body>
</html>
