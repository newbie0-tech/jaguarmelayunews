<?php
/**
 * Koneksi database langsung memakai kredensial publik Railway
 * (mysql://root:WivwFhKvgkNpnRnGAfhfGgPkxntmNFKU@maglev.proxy.rlwy.net:51241/railway)
 * 
 * Catatan produksi: lebih aman simpan kredensial ini di ENV.
 */

$host = 'mysql.railway.internal';
$port = 3306;
$user = 'root';
$pass = 'WivwFhKvgkNpnRnGAfhfGgPkxntmNFKU';
$db   = 'railway';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
