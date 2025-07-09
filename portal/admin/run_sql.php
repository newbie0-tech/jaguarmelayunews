<?php
session_start();          // opsional, jika inc/db.php butuh
require_once __DIR__.'/../inc/db.php';

$sql = "ALTER TABLE posts ADD COLUMN views INT UNSIGNED DEFAULT 0";
try {
    $conn->query($sql);
    echo "✅  Kolom 'views' berhasil ditambahkan.";
} catch (mysqli_sql_exception $e) {
    echo "❌  Error: ".$e->getMessage();
}
