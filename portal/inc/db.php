<?php
$host = 'mysql.railway.internal';
$port = 3306;
$user = 'root';
$pass = 'WivwFhKvgkNpnRnGAfhfGgPkxntmNFKU';
$db   = 'railway';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
