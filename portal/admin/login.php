<?php
session_start();
require_once __DIR__.'/../inc/db.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id,password FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && password_verify($pass, $res['password'])) {
        $_SESSION['admin'] = $res['id'];
        header('Location: dashboard.php');
        exit;
    }
    $msg = 'Username atau password salah';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Login Admin</title>
<link rel="stylesheet" href="/portal/style.css">
<style>
/* --- Halaman Login Centered Card --- */
body{display:flex;align-items:center;justify-content:center;height:100vh;background:#f4f6fb;}
.login-card{background:#fff;padding:32px 36px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.08);width:100%;max-width:360px;}
.login-card h2{margin-top:0;margin-bottom:24px;font-size:24px;text-align:center;color:#0057b8;}
.form-group{margin-bottom:18px;}
.form-group label{display:block;margin-bottom:6px;font-weight:600;font-size:14px;color:#333;}
.form-group input{width:100%;padding:8px 10px;font-size:14px;border:1px solid #ccc;border-radius:4px;}
.btn-submit{width:100%;padding:10px 0;background:#0057b8;color:#fff;border:none;border-radius:4px;font-size:15px;font-weight:600;cursor:pointer;transition:background .2s;}
.btn-submit:hover{background:#00408a;}
.alert{background:#f8d7da;color:#842029;padding:10px 14px;border-radius:4px;margin-bottom:18px;font-size:14px;text-align:center;}
</style>
</head>
<body>
  <div class="login-card">
    <h2>Login Admin</h2>
    <?php if($msg):?><div class="alert"><?=htmlspecialchars($msg)?></div><?php endif;?>
    <form method="post" novalidate>
      <div class="form-group">
        <label for="user">Username</label>
        <input id="user" name="username" required autofocus>
      </div>
      <div class="form-group">
        <label for="pass">Password</label>
        <input type="password" id="pass" name="password" required>
      </div>
      <button class="btn-submit">Login</button>
    </form>
  </div>
</body>
</html>
