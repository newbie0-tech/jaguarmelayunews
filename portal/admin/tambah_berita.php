<?php
/* -----------------------------------------------------------
   admin/tambah_berita.php – Form tambah berita
   ----------------------------------------------------------- */
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__.'/../inc/db.php';

$msg=''; $judul=$isi=$tags=''; $katID=0; $slug=''; $status=1;
$MAX_UPLOAD = 5 * 1024 * 1024;
$uploadDir  = __DIR__.'/../uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name")?->fetch_all(MYSQLI_ASSOC) ?? [];

function make_slug($str){
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)),'-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul  = trim($_POST['judul'] ?? '');
  $isi    = $_POST['isi'] ?? '';
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = (isset($_POST['status']) && $_POST['status']=='1') ? 1 : 0;
  $slug   = make_slug($judul);

  $check=$conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $check->bind_param('s',$slug);
  $check->execute(); $check->bind_result($cnt); $check->fetch(); $check->close();
  if($cnt>0) $slug .= '-'.time();

  /* ---- upload gambar / fallback ---- */
$gambar = '';               // default

if (!empty($_FILES['gambar']['name'])) {
    /* (blok validasi seperti sebelumnya) */
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        $gambar = 'uploads/'.$fname;
    } else {
        $msg = 'Gagal menyimpan gambar ke server.';
    }
}

/* Jika admin tidak meng‑upload & tetap perlu nilai (NOT NULL) */
if ($gambar === '') {
    $gambar = 'assets/placeholder.jpg';   // HARUS ada file ini
}
}

  if(!$msg && $judul && $isi && $katID){
     $stmt=$conn->prepare(
       "INSERT INTO posts
        (judul, slug, isi, tags, status, gambar, kategori_id, penulis_id)
        VALUES (?,?,?,?,?,?,?,?)");
     $penulis=$_SESSION['admin'];
     $stmt->bind_param('ssssisii',$judul, $slug, $isi, $tags, $status, $gambar, $katID, $penulis);

     if($stmt->execute()){
        $msg='Berita berhasil disimpan!';
        $judul=$isi=$tags=''; $katID=0; $slug=''; $status=1;
     } else {
        $msg='Gagal menambahkan berita: '.$stmt->error;
     }
     $stmt->close();
  } elseif(!$msg) $msg='Semua field wajib diisi.';
}
?>

<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Berita</title>
<link rel="stylesheet" href="/portal/css/tambah_berita.css">
<style>
body {
  background: #f9f9f9;
  font-family: 'Segoe UI', sans-serif;
}

.form-wrapper {
background: #fff;
max-width: 700px;
margin: 40px auto;
padding: 30px;
box-shadow: 0 2px 8px rgba(0,0,0,0.1);
border-radius: 12px;
}

h1 {
font-size: 28px;
text-align: center;
color: #0057b8;
margin-bottom: 25px;
}

.form-group {
margin-bottom: 20px;
}

.form-group label {
display: block;
font-weight: 600;
margin-bottom: 6px;
color: #333;
}

.form-group input\[type="text"],
.form-group input\[type="file"],
.form-group select,
.form-group textarea {
width: 100%;
padding: 10px 12px;
border: 1px solid #ccc;
border-radius: 6px;
font-size: 14px;
}

.form-group textarea {
resize: vertical;
min-height: 180px;
}

.preview-img {
margin-top: 10px;
max-width: 100%;
height: auto;
border: 1px solid #ccc;
border-radius: 6px;
}

.note {
font-size: 12px;
color: #666;
margin-top: 4px;
}

.btn-submit {
background: #0057b8;
color: white;
padding: 12px 24px;
border: none;
border-radius: 6px;
font-weight: bold;
cursor: pointer;
font-size: 15px;
width: 100%;
transition: background 0.3s ease;
}

.btn-submit\:hover {
background: #0048a0;
}

.alert {
background: #e6f2ff;
padding: 10px 15px;
border-left: 4px solid #007bff;
margin-bottom: 20px;
border-radius: 6px;
color: #003e7f;
}

a.back-link {
display: inline-block;
margin-top: 25px;
text-align: center;
text-decoration: none;
color: #333;
background: #ddd;
padding: 10px 18px;
border-radius: 6px;
font-weight: bold;
transition: background 0.3s ease;
}
a.back-link\:hover {
background: #ccc;
} </style>

<script src="/portal/vendor/tinymce/tinymce.min.js"></script>

<script>
tinymce.init({
  selector: '#isi',
  height: 520,
  menubar: 'file edit view insert format tools table help',
  plugins: 'preview code lists autolink link image media table autoresize',
  toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code preview',
  branding: false
});

document.addEventListener('DOMContentLoaded', () => {
  const judulInput = document.getElementById('judul');
  const slugInput  = document.getElementById('slug');
  judulInput.addEventListener('input', () => {
    slugInput.value = judulInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  });

  const fileInp = document.getElementById('gambar');
  const prevImg = document.getElementById('prev');
  const note    = document.getElementById('note');
  const MAX_SIZE = 5 * 1024 * 1024;

  fileInp.addEventListener('change', () => {
    const file = fileInp.files[0];
    if (!file) return;

    if (file.size > MAX_SIZE) {
      alert('Ukuran gambar > 5 MB');
      fileInp.value = '';
      prevImg.style.display = 'none';
      note.textContent = '';
      return;
    }
    prevImg.src = URL.createObjectURL(file);
    prevImg.style.display = 'block';
    note.textContent = Math.round(file.size / 1024) + ' KB';
  });
});
</script>

</head>
<body>
<div class="form-wrapper">
  <h1>Tambah Berita</h1>
  <?php if($msg):?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif;?>
  <form method="post" enctype="multipart/form-data" novalidate>
    <div class="form-group"><label for="judul">Judul Berita</label>
      <input id="judul" name="judul" value="<?=htmlspecialchars($judul)?>" required></div>

```
<div class="form-group"><label for="slug">Slug</label>
  <input id="slug" name="slug" value="<?=htmlspecialchars($slug)?>" readonly></div>

<div class="form-group"><label for="kategori">Kategori</label>
  <select id="kategori" name="kategori" required>
    <option value="">--Pilih Kategori--</option>
    <?php foreach($cats as $c):?>
      <option value="<?=$c['id']?>" <?=$katID==$c['id']?'selected':''?>><?=$c['name']?></option>
    <?php endforeach;?>
  </select>
</div>

<div class="form-group"><label for="isi">Isi Berita</label>
  <textarea id="isi" name="isi" required><?=htmlspecialchars($isi)?></textarea></div>

<div class="form-group"><label for="gambar">Gambar Utama (800×450, <5 MB)</label>
  <input type="file" id="gambar" name="gambar" accept="image/*">
  <img id="prev" class="preview-img" style="display:none">
  <div id="note" class="note"></div>
</div>

<div class="form-group"><label for="tags">Tags (pisah koma)</label>
  <input type="text" id="tags" name="tags" value="<?=htmlspecialchars($tags)?>">
</div>

<div class="form-group"><label for="status">Status</label>
  <select id="status" name="status">
    <option value="1" <?=$status==1?'selected':''?>>Publish</option>
    <option value="0" <?=$status==0?'selected':''?>>Draft</option>
  </select>
</div>

<button class="btn-submit" type="submit">Simpan</button>
```

  </form>

  <div style="text-align:center">
    <a href="/portal/admin/dashboard.php" class="back-link">← Kembali ke Dashboard</a>
  </div>
</div>
<?php require_once __DIR__.'/../inc/footer.php'; ?>







