<?php
session_start();
if (!isset($_SESSION['admin']) || !is_numeric($_SESSION['admin'])) {
  header('Location: login.php'); exit;
}
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/header.php';

$judul = $isi = $tags = '';
$katID = 0;
$status = 1;
$msg = '';
$slug = '';
$MAX_UPLOAD = 5 * 1024 * 1024;
$uploadDir = __DIR__ . '/../uploads';

if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

function make_slug($str) {
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)), '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul  = trim($_POST['judul'] ?? '');
  $isi    = $_POST['isi'] ?? '';
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = ($_POST['status'] ?? '1') === '1' ? 1 : 0;
  $slug   = make_slug($judul);

  // Slug unik
  $cek = $conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $cek->bind_param('s', $slug);
  $cek->execute();
  $cek->bind_result($ada);
  $cek->fetch();
  $cek->close();
  if ($ada > 0) $slug .= '-' . time();

  $gambar = '';
  if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp'];
    $size = $_FILES['gambar']['size'];

    if (!in_array($ext, $allow))        $msg = '❌ Format gambar tidak valid.';
    elseif ($size > $MAX_UPLOAD)        $msg = '❌ Ukuran gambar max 5MB.';
    elseif ($_FILES['gambar']['error']) $msg = '❌ Error saat upload.';
    else {
      $fname = time() . '_' . rand(1000,9999) . ".$ext";
      $dest = $uploadDir . '/' . $fname;
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        $gambar = 'uploads/' . $fname;
      } else {
        $msg = '❌ Gagal simpan gambar.';
      }
    }
  }

  if (!$gambar) $gambar = 'assets/placeholder.gif';

  if (!$msg && $judul && $isi && $katID) {
    $penulis = (int)$_SESSION['admin'];
    $stmt = $conn->prepare("INSERT INTO posts (judul, slug, isi, tags, status, gambar, kategori_id, penulis_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssisii', $judul, $slug, $isi, $tags, $status, $gambar, $katID, $penulis);
    if ($stmt->execute()) {
      $msg = '✅ Berita berhasil disimpan.';
      $judul = $isi = $tags = ''; $katID = 0; $status = 1; $slug = '';
    } else {
      $msg = '❌ Gagal menyimpan berita.';
    }
    $stmt->close();
  } elseif (!$msg) {
    $msg = '❌ Semua field wajib diisi.';
  }
}
?>

<div class="container mt-4 mb-5">
  <h2>📝 Tambah Berita</h2>
  <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($judul) ?>">
    </div>
    <div class="mb-3">
      <label>Kategori</label>
      <select name="kategori" class="form-select" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $katID == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Isi Berita</label>
      <textarea name="isi" class="form-control" rows="10" required><?= htmlspecialchars($isi) ?></textarea>
    </div>
    <div class="mb-3">
      <label>Gambar Utama</label>
      <input type="file" name="gambar" accept="image/*" class="form-control">
    </div>
    <div class="mb-3">
      <label>Tags</label>
      <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($tags) ?>">
    </div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="1" <?= $status==1?'selected':'' ?>>Publish</option>
        <option value="0" <?= $status==0?'selected':'' ?>>Draft</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">💾 Simpan</button>
  </form>
</div>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
