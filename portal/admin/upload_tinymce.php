<?php
session_start();
if (!isset($_SESSION['admin'])) {
  http_response_code(403); exit('Forbidden');
}

$uploadDir = __DIR__ . '/../../data/uploads'; // Sesuaikan
$baseUrl   = '/portal/uploads/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (!empty($_FILES['file']['name'])) {
  $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
  $allow = ['jpg','jpeg','png','webp'];
  if (!in_array($ext, $allow)) {
    http_response_code(400);
    echo json_encode(['error'=>'Format tidak didukung']);
    exit;
  }

  $fname = time().'_'.rand(100,999).".$ext";
  $dest  = $uploadDir.'/'.$fname;

  if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    echo json_encode(['location' => $baseUrl.$fname]);
  } else {
    http_response_code(500);
    echo json_encode(['error'=>'Gagal upload']);
  }
} else {
  http_response_code(400);
  echo json_encode(['error'=>'No file uploaded']);
}
?>
