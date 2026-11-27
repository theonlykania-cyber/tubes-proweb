<?php
require_once __DIR__ . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

$user = me();
if (!$user) {
  http_response_code(401);
  echo json_encode(['error' => 'Belum login']);
  exit;
}
$stmt = $pdo->prepare('SELECT id,nama,email,role,nim,fakultas,prodi,whatsapp,alamat,ktm FROM users WHERE id=?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
if (!$profile) {
  http_response_code(404);
  echo json_encode(['error' => 'Profil tidak ditemukan']);
  exit;
}

$stmt = $pdo->prepare('SELECT * FROM produk WHERE user_id=? ORDER BY created_at DESC, id DESC');
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();

echo json_encode(['profile' => $profile, 'products' => $products], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
