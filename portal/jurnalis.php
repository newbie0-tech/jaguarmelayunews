<?php
/* -----------------------------------------------------------
   Form Pendaftaran Jurnalis  –  jurnalis.php
   ----------------------------------------------------------- */

$msg = '';
$MAX_IMG = 2 * 1024 * 1024;   // 2 MB
$uploadDir = __DIR__.'/data';  // folder penyimpanan (data + gambar)
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil input
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telepon  = trim($_POST['telepon']  ?? '');
    $alamat   = trim($_POST['alamat']   ?? '');
    $biografi = trim($_POST['biografi'] ?? '');

    // validasi dasar
    if (!$nama || !$email || !$telepon || !$alamat || !$biografi) {
        $msg = 'Semua kolom wajib diisi!';
    }

    /* ---- HANDLE UPLOAD FOTO ---- */
    $fotoPath = '';
    if (!$msg && !empty($_FILES['foto']['name'])) {
        $ext   = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];
        $size  = $_FILES['foto']['size'];
        if (!in_array($ext, $allow))      $msg = 'Ekstensi foto harus jpg/jpeg/png/webp';
        elseif ($size > $MAX_IMG)          $msg = 'Ukuran foto maksimal 2 MB';
        elseif ($_FILES['foto']['error'])  $msg = 'Error upload foto.';
        else {
            $fname = time().'_'.preg_replace('/[^a-z0-9]+/i','-', strtolower($nama)).".$ext";
            $dest  = $uploadDir.'/'.$fname;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                $fotoPath = 'data/'.$fname; // path relatif untuk preview nanti
            } else {
                $msg = 'Gagal menyimpan foto.';
            }
        }
    }

    /* ---- SIMPAN DATA ---- */
    if (!$msg) {
        $fileName = time().'_'.preg_replace('/[^a-z0-9]+/i','-', strtolower($nama)).'.txt';
        $content  = "Nama       : $nama\n".
                    "Email      : $email\n".
                    "Telepon    : $telepon\n".
                    "Alamat     : $alamat\n".
                    "Biografi   : $biografi\n".
                    ($fotoPath ? "Foto       : $fotoPath\n" : '')."\n";
        if (file_put_contents($uploadDir.'/'.$fileName, $content)) {
            $msg = 'Pendaftaran berhasil!';
        } else {
            $msg = 'Gagal menyimpan data!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Jurnalis</title>
<style>
body{font-family:Verdana,sans-serif;background:#f8f9fa;margin:0}
.form-wrapper{max-width:600px;margin:50px auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 4px 14px rgba(0,0,0,.08)}
.form-group{margin-bottom:20px}
label{font-weight:600;margin-bottom:8px;display:block;font-size:15px}
input,textarea{width:100%;padding:12px;font-size:15px;border:1px solid #ccc;border-radius:4px}
textarea{min-height:150px}
.btn-submit{padding:12px 28px;background:#0057b8;color:#fff;border:none;border-radius:4px;font-size:16px;font-weight:600;cursor:pointer}
.btn-submit:hover{background:#00408a}
.alert{background:#e9ffe9;color:#155724;padding:12px 16px;border-radius:4px;margin-bottom:22px;text-align:center}
.preview{max-width:120px;margin-top:6px;border-radius:4px;border:1px solid #ddd;display:none}
.note{font-size:12px;color:#666}
</style>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const fotoInp = document.getElementById('foto');
  const prevImg = document.getElementById('prev');
  const note    = document.getElementById('note');
  fotoInp.addEventListener('change',()=>{
    if(fotoInp.files[0]){
      const f = fotoInp.files[0];
      if(f.size > <?= $MAX_IMG ?>){
        alert('Ukuran foto > 2MB');
        fotoInp.value='';prevImg.style.display='none';note.textContent='';return;
      }
      prevImg.src = URL.createObjectURL(f);
      prevImg.style.display='block';
      note.textContent = Math.round(f.size/1024)+' KB';
    }
  });
});
</script>
</head>
<body>
<div class="form-wrapper">
<h1 style="text-align:center;color:#0057b8;margin-top:0">Pendaftaran Jurnalis</h1>
<?php if($msg):?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif;?>
<form method="post" enctype="multipart/form-data" novalidate>
  <div class="form-group"><label for="nama">Nama Lengkap</label><input type="text" id="nama" name="nama" required></div>
  <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" required></div>
  <div class="form-group"><label for="telepon">Telepon</label><input type="text" id="telepon" name="telepon" required></div>
  <div class="form-group"><label for="alamat">Alamat</label><input type="text" id="alamat" name="alamat" required></div>
  <div class="form-group"><label for="biografi">Biografi</label><textarea id="biografi" name="biografi" required></textarea></div>
  <div class="form-group"><label for="foto">Foto Profil (jpg/png, &lt;2 MB)</label><input type="file" id="foto" name="foto" accept="image/*">
    <img id="prev" class="preview"><div id="note" class="note"></div></div>
  <button class="btn-submit" type="submit">Daftar</button>
</form>
</div>
</body>
</html>
