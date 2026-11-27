<?php
require_once __DIR__ . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
  http_response_code(400);
  echo json_encode(['error' => 'ID akun tidak valid']);
  exit;
}

$stmt = $pdo->prepare('SELECT id, nama, nim, fakultas, prodi, role FROM users WHERE id=?');
$stmt->execute([$id]);
$profile = $stmt->fetch();
if (!$profile) {
  http_response_code(404);
  echo json_encode(['error' => 'Akun tidak ditemukan']);
  exit;
}
$profile['id'] = (int) $profile['id'];

$stmt = $pdo->prepare('SELECT p.id, p.user_id, p.nama_produk, p.deskripsi_produk, p.gambar_produk, p.status_produk, p.whatsapp, u.whatsapp AS seller_whatsapp FROM produk p JOIN users u ON u.id=p.user_id WHERE p.user_id=? ORDER BY p.created_at DESC, p.id DESC');
$stmt->execute([$id]);
$products = $stmt->fetchAll();
foreach ($products as &$product) {
  $product['id'] = (int) $product['id'];
  $product['user_id'] = (int) $product['user_id'];
  $product['whatsapp'] = normalize_whatsapp($product['whatsapp'] ?: $product['seller_whatsapp']);
  unset($product['seller_whatsapp']);
}
unset($product);

echo json_encode(['profile' => $profile, 'products' => $products], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
