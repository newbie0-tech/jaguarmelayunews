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
<?php require_once __DIR__.'/../inc/header.php'; ?>
<div class="container-form">
    <h2>Tambah Berita</h2>

    <?php if (!empty($msg)) echo "<div class='form-msg'>$msg</div>"; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="news-form">
        <label for="judul">Judul Berita</label>
        <input type="text" id="judul" name="judul" required>

        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug">

        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori_id">
            <!-- Loop kategori -->
            <?php foreach ($kategoriList as $id => $nama): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($nama) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="isi">Isi Berita</label>
        <textarea id="isi" name="isi" rows="6" required></textarea>

        <label for="gambar">Upload Gambar</label>
        <input type="file" id="gambar" name="gambar">

        <label for="tags">Tags</label>
        <input type="text" id="tags" name="tags" placeholder="mis: olahraga, nasional, ekonomi">

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="publish">Publish</option>
            <option value="draft">Draft</option>
        </select>

        <button type="submit" class="btn-submit">Simpan Berita</button>
    </form>
</div>
<?php require_once __DIR__.'/../inc/footer.php'; ?>

