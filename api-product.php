<?php
require_once __DIR__ . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
$id = (int) ($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID produk wajib']); exit; }
$stmt = $pdo->prepare("SELECT p.*, u.nama, u.fakultas, u.prodi, u.id AS seller_id, COALESCE(NULLIF(p.whatsapp, ''), u.whatsapp) AS seller_whatsapp FROM produk p JOIN users u ON u.id=p.user_id WHERE p.id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); echo json_encode(['error'=>'Produk tidak ditemukan']); exit; }
$product['id'] = (int) $product['id'];
$product['user_id'] = (int) $product['user_id'];
$product['seller_id'] = (int) $product['seller_id'];
$product['seller_whatsapp'] = normalize_whatsapp($product['seller_whatsapp']);
echo json_encode(['product'=>$product], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
