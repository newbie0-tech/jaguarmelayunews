<?php
// Aktifkan laporan error saat pengembangan (nonaktifkan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Buat koneksi ke database
$conn = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    getenv('DB_PORT') ?: 3306
);

// Cek koneksi
if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
