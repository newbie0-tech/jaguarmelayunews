<?php
/**
 * Koneksi database langsung memakai kredensial publik Railway
 * (mysql://root:WivwFhKvgkNpnRnGAfhfGgPkxntmNFKU@maglev.proxy.rlwy.net:51241/railway)
 * 
 * Catatan produksi: lebih aman simpan kredensial ini di ENV.
 */

$host = 'maglev.proxy.rlwy.net';
$port = 51241;
$user = 'root';
$pass = 'UCUNGqgclDfEgpSxKPqoVCmTujycSVWR';
$db   = 'railway';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
