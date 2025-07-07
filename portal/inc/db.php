<?php
// db.php
ini_set('display_errors',1);
error_reporting(E_ALL);

$conn = new mysqli(
    getenv('containers-us-west-1.railway.internal'),
    getenv('railway'),
    getenv('linkid99'),
    getenv('railway'),
    getenv('3306') ?: 3306
);

if ($conn->connect_error) {
    die('Koneksi gagal: '.$conn->connect_error);
}
