<?php

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once __DIR__.'/../inc/db.php';
require_once __DIR__.'/../inc/header.php';
$msg=''; $judul=$isi=''; $katID=0; $slug='';
$MAX_UPLOAD=5*1024*1024;          // 5 MB limit
$cats=$conn->query("SELECT id,name FROM categories ORDER BY name")?->fetch_all(MYSQLI_ASSOC) ?? [];

function make_slug($str){
   $s=strtolower(preg_replace('/[^a-z0-9]+/i','-',trim($str)));
   return trim($s,'-');
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $judul = trim($_POST['judul'] ?? '');
  $isi   = $_POST['isi'] ?? '';
  $katID = (int)($_POST['kategori'] ?? 0);
  $slug  = make_slug($judul);

  /* pastikan slug unik */
  $slugCheck=$conn->prepare("SELECT COUNT(*) FROM posts WHERE slug=?");
  $slugCheck->bind_param('s',$slug);$slugCheck->execute();$slugCheck->bind_result($cnt);$slugCheck->fetch();$slugCheck->close();
  if($cnt>0){ $slug .= '-'.time(); }

  /* handle upload */
  $gambar='';
  if(!empty($_FILES['gambar']['name'])){
     $ext=strtolower(pathinfo($_FILES['gambar']['name'],PATHINFO_EXTENSION));
     $allow=['jpg','jpeg','png','webp'];
     $size=$_FILES['gambar']['size'];
     if(!in_array($ext,$allow))      $msg='Ekstensi harus jpg/jpeg/png/webp';
     elseif($size>$MAX_UPLOAD)        $msg='Ukuran gambar maksimal 5 MB';
     else{
       $fname=time().'_'.rand(100,999).".$ext";
       $dest=__DIR__.'/../uploads/'.$fname;
       if(move_uploaded_file($_FILES['gambar']['tmp_name'],$dest)) $gambar='uploads/'.$fname;
       else $msg='Gagal menyimpan gambar.';
     }
  }

  /* simpan ke DB */
  if(!$msg && $judul && $isi && $katID){
     $stmt=$conn->prepare("INSERT INTO posts (judul,slug,isi,gambar,kategori_id,penulis_id) VALUES (?,?,?,?,?,?)");
     $adminID=$_SESSION['admin'];
     $stmt->bind_param('ssssii',$judul,$slug,$isi,$gambar,$katID,$adminID);
     if($stmt->execute()){
        $msg='Berita berhasil disimpan!';
        $judul=$isi=''; $katID=0; $slug='';
     }else{
        $msg='Gagal menambahkan berita!';
     }
     $stmt->close();
  }elseif(!$msg){ $msg='Semua field wajib diisi.'; }
}
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="utf-8"><title>Tambah Berita</title>
<link rel="stylesheet" href="/portal/style.css">
<script src="/portal/vendor/tinymce/tinymce.min.js"></script>
<script>
  tinymce.init({
    selector:'#isi',height:520,menubar:'file edit view insert format tools table help',
    plugins:'preview code lists autolink link image media table autoresize',
    toolbar:'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code preview',
    branding:false
  });
  function slugify(s){return s.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');}
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
         if(f.size><?php echo $MAX_UPLOAD; ?>){alert('Ukuran gambar > 5MB');fileInp.value='';prev.style.display='none';note.textContent='';return;}
         prev.src=URL.createObjectURL(f);prev.style.display='block';note.textContent=Math.round(f.size/1024)+' KB';
       }
     });
  });
</script>
<style>
.form-wrapper{max-width:860px;margin:30px auto;background:#fff;padding:40px;border-radius:8px;box-shadow:0 4px 14px rgba(0,0,0,.08);} 
.form-group{margin-bottom:20px;} label{font-weight:600;margin-bottom:8px;display:block;font-size:15px;} input,select,textarea{width:100%;padding:12px;font-size:15px;border:1px solid #ccc;border-radius:4px;} textarea{min-height:280px;} .btn-submit{padding:12px 28px;background:#0057b8;color:#fff;border:none;border-radius:4px;font-size:16px;font-weight:600;cursor:pointer;} .btn-submit:hover{background:#00408a;}
.alert{background:#e9ffe9;color:#155724;padding:12px 16px;border-radius:4px;margin-bottom:22px;text-align:center;}
.preview-img{max-width:260px;margin-top:8px;border:1px solid #ddd;border-radius:4px}
.note{font-size:12px;color:#666;margin-top:4px;}
</style>
</head><body>
<div class="form-wrapper">
<h1 style="text-align:center;color:#0057b8;margin-top:0">Tambah Berita</h1>
<?php if($msg):?><div class="alert"><?=htmlspecialchars($msg)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data" novalidate>
  <div class="form-group"><label for="judul">Judul Berita</label><input id="judul" name="judul" value="<?=htmlspecialchars($judul)?>" required></div>
  <div class="form-group"><label for="slug">Slug</label><input id="slug" name="slug" value="<?=htmlspecialchars($slug)?>" readonly></div>
  <div class="form-group"><label for="kategori">Kategori</label><select id="kategori" name="kategori" required><option value="">--Pilih Kategori--</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=($katID==$c['id']?'selected':'')?>><?=$c['name']?></option><?php endforeach;?></select></div>
  <div class="form-group"><label for="isi">Isi Berita</label><textarea id="isi" name="isi" required><?=htmlspecialchars($isi)?></textarea></div>
  <div class="form-group"><label for="gambar">Gambar Utama (800×450, &lt;5 MB)</label><input type="file" id="gambar" name="gambar" accept="image/*">
     <img id="prev" class="preview-img" style="display:none"><div id="note" class="note"></div></div>
  <button class="btn-submit">Simpan</button>
</form>
<div style="margin-top:20px;text-align:center;">
  <a href="/portal/admin/dashboard.php" style="text-decoration:none;background:#ccc;color:#333;padding:10px 20px;border-radius:4px;font-weight:bold;display:inline-block;">← Kembali ke Dashboard</a>
</div>
</body></html>

<?php require_once __DIR__.'/../inc/footer.php'; ?>