<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__.'/../inc/db.php';
require_once __DIR__.'/../inc/header.php';

/* — Pagination — */
$perPage = 15;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$total = $conn->query("SELECT COUNT(*) AS jml FROM posts")->fetch_assoc()['jml'];
$pages = ceil($total / $perPage);

/* — Ambil berita + kategori — */
$sql = "SELECT p.id, p.judul, p.slug, p.tanggal, p.gambar,
               c.name AS kategori
        FROM posts p
        JOIN categories c ON c.id = p.kategori_id
        ORDER BY p.tanggal DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Daftar Berita</title>
<link rel="stylesheet" href="/portal/css/style.css">
<style>
.table-wrap      {max-width:1000px;margin:30px auto;padding:0 12px;}
.table           {width:100%;border-collapse:collapse}
.table th,.table td{border:1px solid #ddd}
.table thead th  {background:#0057b8;color:#fff;padding:10px;font-size:14px;text-align:left}
.table tbody td  {padding:10px;font-size:14px;vertical-align:top}
.thumb           {width:80px;height:60px;object-fit:cover;border-radius:4px}
.action a        {margin-right:8px;font-size:13px;text-decoration:none}
.btn-blue        {background:#0d6efd;color:#fff;padding:4px 10px;border-radius:4px}
.btn-red         {background:#dc3545;color:#fff;padding:4px 10px;border-radius:4px}
.pagination      {margin:18px 0;text-align:center}
.pagination a    {display:inline-block;margin:0 4px;padding:6px 10px;background:#0057b8;color:#fff;border-radius:4px;font-size:13px;text-decoration:none}
.pagination a.active{background:#00408a;pointer-events:none}
.center          {text-align:center}
</style>
</head>
<body>
<div class="table-wrap">
  <h1 class="page-title" style="margin-top:0">Daftar Berita</h1>

  <table class="table">
    <thead>
      <tr>
        <th>Img</th>
        <th>Judul</th>
        <th>Kategori</th>
        <th>Tanggal</th>
        <th class="center">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <?php
        /* --- tentukan URL gambar --- */
        if (preg_match('#^https?://#',$row['gambar'])) {
           $thumb = $row['gambar'];                         // Cloudinary / CDN
        } elseif ($row['gambar']) {
           $thumb = '/portal/'.ltrim($row['gambar'],'/');  // upload lokal
        } else {
           $thumb = '/portal/assets/placeholder.jpg';      // fallback
        }
      ?>
      <tr>
        <td><img src="<?= htmlspecialchars($thumb) ?>" class="thumb"></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['kategori']) ?></td>
        <td><?= date('d M Y H:i', strtotime($row['tanggal'])) ?></td>
        <td class="action center">
          <a href="edit_berita.php?id=<?= $row['id'] ?>"  class="btn-blue">Edit</a>
          <a href="hapus_berita.php?id=<?= $row['id'] ?>" class="btn-red"
             onclick="return confirm('Hapus berita ini?')">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
    <div class="pagination">
      <?php for ($i=1;$i<=$pages;$i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i==$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
<?php require_once __DIR__.'/../inc/footer.php'; ?>
