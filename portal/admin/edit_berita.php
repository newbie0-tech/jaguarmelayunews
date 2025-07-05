<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once __DIR__.'/../inc/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: daftar_berita.php'); exit; }

$msg = ''; $cats = [];
$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Data lama
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { header('Location: daftar_berita.php'); exit; }

$judul  = $post['judul'];
$slug   = $post['slug'];
$isi    = $post['isi'];
$katID  = $post['kategori_id'];
$gambar = $post['gambar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $slug  = strtolower(preg_replace('/[^a-z0-9]+/i','-', $judul));
    $isi   = $_POST['isi'] ?? '';
    $katID = (int)($_POST['kategori'] ?? 0);

    // Gambar baru
    $gambarBaru = $gambar;
    if (!empty($_FILES['gambar']['name'])) {
        $ext   = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];
        $size  = $_FILES['gambar']['size'];
        if (in_array($ext, $allow) && $size <= 5*1024*1024) { // 5 MB
            $fname = time().'_'.rand(100,999).".$ext";
            $dest  = __DIR__.'/../uploads/'.$fname;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
                if ($gambar && file_exists(__DIR__.'/../'.$gambar)) unlink(__DIR__.'/../'.$gambar);
                $gambarBaru = 'uploads/'.$fname;
            }
        } else {
            $msg = 'Format gambar harus jpg/jpeg/png/webp dan < 5 MB';
        }
    }

    if ($judul && $isi && $katID && !$msg) {
        $upd = $conn->prepare("UPDATE posts SET judul=?, slug=?, isi=?, gambar=?, kategori_id=? WHERE id=?");
        $upd->bind_param('ssssii', $judul, $slug, $isi, $gambarBaru, $katID, $id);
        if ($upd->execute()) {
        header('Location: daftar_berita.php?msg=updated');
        exit;
    }
    $msg = 'Gagal memperbarui berita.';
        if ($upd->errno == 1062) $msg = 'Slug sudah digunakan, ubah judul.';
    } elseif (!$msg) {
        $msg = 'Semua field wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Berita</title>
  <link rel="stylesheet" href="/portal/style.css">
  <script src="/portal/vendor/tinymce/tinymce.min.js"></script>
  <script>
    tinymce.init({
      selector:'#isi',
      height: 700,                // diperbesar
      menubar: 'file edit view insert format tools table help',
      plugins: 'preview code lists autolink link image media table autoresize',
      toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code preview',
      branding: false
    });
    function slugify(str){return str.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');}
    document.addEventListener('DOMContentLoaded',()=>{
      const jud=document.getElementById('judul');
      const s=document.getElementById('slug');
      jud.addEventListener('input',()=>{s.value=slugify(jud.value);});
      const fileInp=document.getElementById('gambar');
      const prev=document.getElementById('prev');
      const note=document.getElementById('note');
      fileInp.addEventListener('change',()=>{
        if(fileInp.files[0]){
          const f=fileInp.files[0];
          if(f.size>5*1024*1024){alert('Ukuran gambar > 5MB');fileInp.value='';prev.style.display='none';note.textContent='';return;}
          prev.src=URL.createObjectURL(f);prev.style.display='block';note.textContent=Math.round(f.size/1024)+' KB';
        }
      });
    });
  </script>
  <style>
    .form-wrapper{max-width:880px;margin:30px auto;background:#fff;padding:40px;border-radius:8px;box-shadow:0 4px 14px rgba(0,0,0,.08);} 
    .form-group{margin-bottom:22px;}
    label{font-weight:600;margin-bottom:8px;display:block;font-size:15px;}
    input,select,textarea{width:100%;padding:14px;font-size:15px;border:1px solid #ccc;border-radius:4px;}
    textarea{min-height:380px;}
    .btn-submit{padding:12px 28px;background:#0057b8;color:#fff;border:none;border-radius:4px;font-size:16px;font-weight:600;cursor:pointer;}
    .btn-submit:hover{background:#00408a;}
    .alert{background:#e9ffe9;color:#155724;padding:12px 16px;border-radius:4px;margin-bottom:22px;text-align:center;}
    .preview-img{max-width:260px;margin-top:8px;border:1px solid #ddd;border-radius:4px}
    .note{font-size:12px;color:#666;margin-top:4px;}
  </style>
</head>
<body>
<div class="form-wrapper">
  <h1 style="text-align:center;color:#0057b8;margin-top:0">Edit Berita</h1>
  <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" novalidate>
    <div class="form-group"><label for="judul">Judul</label><input id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" required></div>
    <div class="form-group"><label for="slug">Slug</label><input id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" readonly></div>
    <div class="form-group"><label for="kategori">Kategori</label>
      <select id="kategori" name="kategori" required>
        <option value="">--Pilih Kategori--</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($katID == $c['id']) ? 'selected' : '' ?>><?= $c['name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label for="isi">Isi Berita</label>
      <textarea id="isi" name="isi" required><?= htmlspecialchars($isi) ?></textarea>
    </div>
    <div class="form-group"><label for="gambar">Gambar Utama (opsional, max 5MB)</label>
      <input type="file" id="gambar" name="gambar" accept="image/*">
      <?php if($gambar): ?><img src="/portal/<?= $gambar ?>" class="preview-img"><?php endif; ?>
      <img id="prev" class="preview-img" style="display:none">
      <div id="note" class="note"></div>
    </div>
    <button class="btn-submit">Simpan Perubahan</button>
  </form>
</div>
</body>
</html>
