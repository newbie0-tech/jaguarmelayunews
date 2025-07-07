<?php
// db.php
ini_set('display_errors',1);
error_reporting(E_ALL);

$conn = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    getenv('DB_PORT') ?: 3306
);

if ($conn->connect_error) {
    die('Koneksi gagal: '.$conn->connect_error);
}
