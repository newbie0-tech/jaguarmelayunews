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

$kategoriList = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name");
while ($row = $res->fetch_assoc()) {
    $kategoriList[$row['id']] = $row['name'];
}

function make_slug($str) {
    return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)), '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul  = trim($_POST['judul'] ?? '');
    $isi    = trim($_POST['isi'] ?? '');
    $katID  = (int)($_POST['kategori_id'] ?? 0);
    $tags   = trim($_POST['tags'] ?? '');
    $status = $_POST['status'] === 'draft' ? 0 : 1;
    $slug   = make_slug($judul);
    $penulis = (int)$_SESSION['admin'];

    // Cek slug unik
    $stmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch(); $stmt->close();
    if ($cnt > 0) $slug .= '-' . time();

    $gambar = 'assets/placeholder.gif';
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];
        $size = $_FILES['gambar']['size'];

        if (!in_array($ext, $allow))        $msg = '❌ Ekstensi gambar tidak valid.';
        elseif ($size > $MAX_UPLOAD)        $msg = '❌ Ukuran gambar maksimal 5 MB.';
        elseif ($_FILES['gambar']['error']) $msg = '❌ Terjadi error saat upload.';
        else {
            $fname = time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $uploadDir . $fname;
            if ($_FILES['gambar']['tmp_name']) {
  $tmpPath = $_FILES['gambar']['tmp_name'];
  $fileName = basename($_FILES['gambar']['name']);
  $uploadedUrl = uploadToImageKit($tmpPath, $fileName);

  if ($uploadedUrl) {
    $gambar = $uploadedUrl;
  } else {
    $msg = 'Upload ke ImageKit gagal.';
  }
            } else {
                $msg = '❌ Gagal menyimpan gambar.';
            }
        }
    }

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
?>

<!-- HTML -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<div class="container-form">
    <h2>Tambah Berita</h2>
    <?php if ($msg): ?>
        <div class='form-msg'><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="news-form">
        <label for="judul">Judul Berita</label>
        <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" required>

        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" readonly>

        <label for="kategori">Kategori</label>
        <select name="kategori_id" id="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $id => $nama): ?>
                <option value="<?= $id ?>" <?= $katID==$id?'selected':'' ?>><?= htmlspecialchars($nama) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="isi">Isi Berita</label>
        <textarea id="isi" name="isi" rows="6"><?= htmlspecialchars($isi) ?></textarea>
        <script>CKEDITOR.replace('isi');</script>

        <label for="gambar">Upload Gambar</label>
        <input type="file" id="gambar" name="gambar">

        <label for="tags">Tags</label>
        <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags) ?>" placeholder="mis: olahraga, nasional">

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="publish" <?= $status==1?'selected':'' ?>>Publish</option>
            <option value="draft" <?= $status==0?'selected':'' ?>>Draft</option>
        </select>

        <button type="submit" class="btn-submit">Simpan Berita</button>
    </form>
</div>

<?php require_once __DIR__.'/../inc/footer.php'; ?>
