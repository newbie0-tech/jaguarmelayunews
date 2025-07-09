<?php
/* -----------------------------------------------------------
   admin/tambah_berita.php – Form tambah berita
   ----------------------------------------------------------- */
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__.'/../inc/db.php';

$msg=''; $judul=$isi=$tags=''; $katID=0; $slug=''; $status=1;  // 1 = publish
$MAX_UPLOAD = 5 * 1024 * 1024;                      // 5 MB
$uploadDir  = __DIR__.'/../uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name")
        ?->fetch_all(MYSQLI_ASSOC) ?? [];

function make_slug($str){
  return trim(strtolower(preg_replace('/[^a-z0-9]+/i','-', $str)),'-');
}

/* === PROSES SUBMIT === */
if ($_SERVER['REQUEST_METHOD']==='POST') {

  $judul  = trim($_POST['judul'] ?? '');
  $isi    = $_POST['isi'] ?? '';
  $katID  = (int)($_POST['kategori'] ?? 0);
  $tags   = trim($_POST['tags'] ?? '');
  $status = (isset($_POST['status']) && $_POST['status']=='1') ? 1 : 0;
  $slug   = make_slug($judul);

  /* slug unik */
  $check=$conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $check->bind_param('s',$slug); $check->execute();
  $check->bind_result($cnt);     $check->fetch(); $check->close();
  if($cnt>0) $slug .= '-'.time();

  /* upload gambar */
  $gambar='';
  if(!empty($_FILES['gambar']['name'])){
     $ext=strtolower(pathinfo($_FILES['gambar']['name'],PATHINFO_EXTENSION));
     $allow=['jpg','jpeg','png','webp'];
     $size=$_FILES['gambar']['size'];
     if(!in_array($ext,$allow))           $msg='Ekstensi gambar harus jpg/jpeg/png/webp';
     elseif($size>$MAX_UPLOAD)            $msg='Ukuran gambar maksimal 5 MB';
     elseif($_FILES['gambar']['error'])   $msg='Error upload gambar';
     else{
        $fname=time().'_'.rand(100,999).".$ext";
        $dest="$uploadDir/$fname";
        if(move_uploaded_file($_FILES['gambar']['tmp_name'],$dest))
           $gambar='uploads/'.$fname;
        else $msg='Gagal menyimpan gambar.';
     }
  }

  /* simpan DB */
  if(!$msg && $judul && $isi && $katID){
     $stmt=$conn->prepare(
       "INSERT INTO posts
        (judul, slug, isi, tags, status, gambar, kategori_id, penulis_id)
        VALUES (?,?,?,?,?,?,?,?)");
     $penulis = $_SESSION['admin'];
     $stmt->bind_param('ssssisii',
       $judul,$slug,$isi,$tags,$status,$gambar,$katID,$penulis
     );
     if($stmt->execute()){
        $msg='Berita berhasil disimpan!';
        $judul=$isi=$tags=''; $katID=0; $slug=''; $status=1;
     } else $msg='Gagal menambahkan berita: '.$stmt->error;
     $stmt->close();
  } elseif(!$msg) $msg='Semua field wajib diisi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Berita</title>

<link rel="stylesheet" href="/portal/css/style.css">
<script src="/portal/vendor/tinymce/tinymce.min.js"></script>

<script>
/* ─ TinyMCE ───────────────────────────────────────────── */
tinymce.init({
  selector:'#isi',
  height:520,
  menubar:'file edit view insert format tools table help',
  plugins:'preview code lists autolink link image media table autoresize',
  toolbar:'undo redo | styleselect | bold italic underline | ' +
          'alignleft aligncenter alignright | bullist numlist | ' +
          'link image media table | code preview',
  branding:false
});

/* ─ Util slug ─────────────────────────────────────────── */
function slugify(str){
  return str.toLowerCase()
            .replace(/[^a-z0-9]+/g,'-')
            .replace(/(^-|-$)/g,'');
}

document.addEventListener('DOMContentLoaded',()=>{

  /* Auto‑slug */
  const judul = document.getElementById('judul');
  const slug  = document.getElementById('slug');
  judul.addEventListener('input',()=>{ slug.value = slugify(judul.value); });

  /* Preview gambar */
  const fileInp = document.getElementById('gambar');
  const prev    = document.getElementById('prev');
  const note    = document.getElementById('note');
  const MAX_SIZE = <?= $MAX_UPLOAD ?>;

  fileInp.addEventListener('change',()=>{
     const f = fileInp.files[0];
     if(!f) return;
     if(f.size > MAX_SIZE){
        alert('Gambar > 5 MB');
        fileInp.value=''; prev.style.display='none'; note.textContent='';
        return;
     }
     prev.src = URL.createObjectURL(f);
     prev.style.display='block';
     note.textContent = Math.round(f.size/1024)+' KB';
  });
});
</script>
</head>

<body>
<div class="form-wrapper">
  <h1 style="text-align:center;color:#0057b8;margin-top:0">Tambah Berita</h1>

  <?php if($msg): ?>
    <div class="alert"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">

    <div class="form-group">
      <label for="judul">Judul Berita</label>
      <input id="judul" name="judul" required value="<?= htmlspecialchars($judul) ?>">
    </div>

    <div class="form-group">
      <label for="slug">Slug</label>
      <input id="slug" name="slug" readonly value="<?= htmlspecialchars($slug) ?>">
    </div>

    <div class="form-group">
      <label for="kategori">Kategori</label>
      <select id="kategori" name="kategori" required>
        <option value="">--Pilih Kategori--</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $katID==$c['id']?'selected':'' ?>>
            <?= htmlspecialchars($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="isi">Isi Berita</label>
      <textarea id="isi" name="isi" required><?= htmlspecialchars($isi) ?></textarea>
    </div>

    <div class="form-group">
      <label for="gambar">Gambar Utama (800×450, &lt;5 MB)</label>
      <input type="file" id="gambar" name="gambar" accept="image/*">
      <img id="prev" class="preview-img" style="display:none">
      <div id="note" class="note"></div>
    </div>

    <div class="form-group">
      <label for="tags">Tags (pisah koma)</label>
      <input id="tags" name="tags" value="<?= htmlspecialchars($tags) ?>">
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="1" <?= $status==1?'selected':'' ?>>Publish</option>
        <option value="0" <?= $status==0?'selected':'' ?>>Draft</option>
      </select>
    </div>

    <button class="btn-submit" type="submit">Simpan</button>
  </form>

  <div style="margin-top:20px;text-align:center">
    <a href="/portal/admin/dashboard.php"
       style="text-decoration:none;background:#ccc;color:#333;padding:10px 20px;border-radius:4px;font-weight:bold;display:inline-block">
       ← Kembali ke Dashboard
    </a>
  </div>
</div>

<?php require_once __DIR__.'/../inc/footer.php'; ?>
