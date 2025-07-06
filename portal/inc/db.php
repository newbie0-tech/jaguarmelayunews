<?php
$host = 'localhost';
$user = 'berita_user';    
$pass = 'Berita$2025';     
$db   = 'portal_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die('DB gagal: '.$conn->connect_error);
mysqli_set_charset($conn,'utf8mb4');
?>
