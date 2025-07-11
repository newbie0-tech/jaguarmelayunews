<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/header.php';
$msg = '';
$judul = $isi = $tags = '';
$katID = 0;
$status = 1;
$slug = '';
$uploadDir = __DIR__ . '/../uploads/';
$MAX_UPLOAD = 5 * 1024 * 1024;

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

function make_slug($str) {
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)), '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul  = trim($_POST['judul'] ?? '');
  $isi    = trim($_POST['isi'] ?? '');
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = ($_POST['status'] ?? '1') === '1' ? 1 : 0;
  $slug   = make_slug($judul);
  $penulis = (int)$_SESSION['admin'];

  $check = $conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $check->bind_param('s', $slug);
  $check->execute();
  $check->bind_result($cnt); $check->fetch(); $check->close();
  if ($cnt > 0) $slug .= '-' . time();

  $gambar = '';
  if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp'];
    $size = $_FILES['gambar']['size'];

    if (!in_array($ext, $allow))        $msg = 'Ekstensi gambar harus jpg/jpeg/png/webp';
    elseif ($size > $MAX_UPLOAD)        $msg = 'Ukuran gambar maksimal 5 MB';
    elseif ($_FILES['gambar']['error']) $msg = 'Terjadi error saat upload';
    else {
      $fname = time() . '_' . rand(1000,9999) . '.' . $ext;
      $dest = $uploadDir . $fname;
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        $gambar = 'uploads/' . $fname;
      } else {
        $msg = '❌ Gagal menyimpan gambar.';
      }
    }
  }

  if (!$gambar) $gambar = 'assets/placeholder.gif';

  if (!$msg && $judul && $isi && $katID) {
    $stmt = $conn->prepare("INSERT INTO posts (judul, slug, isi, tags, status, gambar, kategori_id, penulis_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssisii', $judul, $slug, $isi, $tags, $status, $gambar, $katID, $penulis);
    if ($stmt->execute()) {
      $msg = '✅ Berita berhasil disimpan.';
      $judul = $isi = $tags = ''; $katID = 0; $status = 1;
    } else {
      $msg = '❌ Gagal menambahkan berita.';
    }
    $stmt->close();
  } elseif (!$msg) {
    $msg = '❌ Semua field wajib diisi.';
  }
}
require_once __DIR__ . '/../inc/header.php';
?>

<div class="form-container">
  <h2>Tambah Berita</h2>

  <?php if (!empty($sukses)) echo "<div class='alert sukses'>$sukses</div>"; ?>
  <?php if (!empty($error)) echo "<div class='alert error'>$error</div>"; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Judul Berita</label>
      <input type="text" name="judul" required>
    </div>

    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" required>
    </div>

    <div class="form-group">
      <label>Kategori</label>
      <select name="kategori_id">
        <!-- Opsional: generate dari DB -->
        <option value="1">Nasional</option>
        <option value="2">Daerah</option>
        <option value="3">Politik</option>
        <option value="4">Pendidikan</option>
        <option value="5">Dunia</option>
      </select>
    </div>

    <div class="form-group">
      <label>Isi Berita</label>
      <textarea name="isi" rows="7" required></textarea>
    </div>

    <div class="form-group">
      <label>Upload Gambar</label>
      <input type="file" name="gambar">
    </div>

    <div class="form-group">
      <label>Tags</label>
      <input type="text" name="tags">
    </div>

    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <option value="Publish">Publish</option>
        <option value="Draft">Draft</option>
      </select>
    </div>

    <button type="submit" class="btn-submit">Simpan Berita</button>
  </form>
</div>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
