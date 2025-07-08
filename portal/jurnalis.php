<?php
// Menangani penyimpanan data
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $biografi = $_POST['biografi'] ?? '';

    // Validasi input
    if (!$nama || !$email || !$telepon || !$alamat || !$biografi) {
        $msg = 'Semua kolom harus diisi!';
    } else {
        // Format data sebagai string
        $data = "Nama: $nama\nEmail: $email\nTelepon: $telepon\nAlamat: $alamat\nBiografi: $biografi\n\n";
        
        // Tentukan lokasi file untuk menyimpan data
        $folderPath = __DIR__ . '/data/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // Membuat folder 'data' jika belum ada
        }
        
        // Simpan data ke file dengan nama unik
        $fileName = time() . '_' . preg_replace('/[^a-z0-9]+/i', '-', $nama) . '.txt';
        $filePath = $folderPath . $fileName;
        
        if (file_put_contents($filePath, $data)) {
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
        body { font-family: Verdana, sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
        .form-wrapper { max-width: 600px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); }
        .form-group { margin-bottom: 20px; }
        label { font-weight: 600; margin-bottom: 8px; display: block; font-size: 15px; }
        input, textarea { width: 100%; padding: 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { min-height: 150px; }
        .btn-submit { padding: 12px 28px; background: #0057b8; color: #fff; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background: #00408a; }
        .alert { background: #e9ffe9; color: #155724; padding: 12px 16px; border-radius: 4px; margin-bottom: 22px; text-align: center; }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <h1 style="text-align:center;color:#0057b8;margin-top:0">Pendaftaran Jurnalis</h1>
        <?php if ($msg): ?>
            <div class="alert"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="text" id="telepon" name="telepon" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" required>
            </div>
            <div class="form-group">
                <label for="biografi">Biografi</label>
                <textarea id="biografi" name="biografi" required></textarea>
            </div>
            <button class="btn-submit" type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>
