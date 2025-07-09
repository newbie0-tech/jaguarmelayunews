<?php
/* inc/db.php */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  // ← deklarasikan dulu

$host = 'mysql.railway.internal';   // hanya dapat di‑resolve di dalam container Railway
$port = 3306;
$user = 'root';
$pass = 'WivwFhKvgkNpnRnGAfhfGgPkxntmNFKU';
$db   = 'railway';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
