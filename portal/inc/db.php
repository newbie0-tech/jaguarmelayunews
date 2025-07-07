<?php
$host = getenv('MYSQLHOST')      ?: getenv('DB_HOST')      ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT')      ?: getenv('DB_PORT')      ?: 3306;
$user = getenv('MYSQLUSER')      ?: getenv('DB_USER')      ?: 'root';
$pass = getenv('MYSQLPASSWORD')  ?: getenv('DB_PASS')      ?: '';
$db   = getenv('MYSQLDATABASE')  ?: getenv('DB_NAME')      ?: 'railway';

$conn = new mysqli($host, $user, $pass, $db, (int) $port);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
