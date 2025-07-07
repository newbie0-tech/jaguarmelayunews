<?php
/*
 | ----------------------------------------------------------------
 | Koneksi Database (Railway MySQL)
 | ----------------------------------------------------------------
 | Mengambil variabel lingkungan yang otomatis disuntikkan Railway
 | saat layanan web di‑link dengan layanan MySQL. Tetap memberi
 | fallback ke DB_* jika kamu memilih menamai ulang variabel sendiri.
 */

$host = getenv('MYSQLHOST')      ?: getenv('DB_HOST')      ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT')      ?: getenv('DB_PORT')      ?: 3306;
$user = getenv('MYSQLUSER')      ?: getenv('DB_USER')      ?: 'root';
$pass = getenv('MYSQLPASSWORD')  ?: getenv('DB_PASS')      ?: '';
$db   = getenv('MYSQLDATABASE')  ?: getenv('DB_NAME')      ?: 'railway';

// Buat koneksi menggunakan mysqli & aktifkan mode error
$conn = new mysqli($host, $user, $pass, $db, (int) $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
