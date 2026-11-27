<?php
require_once __DIR__ . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

$q = trim($_GET['q'] ?? '');
$sql = "SELECT p.*, u.nama, u.fakultas, u.prodi, u.id AS seller_id,
         COALESCE(NULLIF(p.whatsapp, ''), u.whatsapp) AS seller_whatsapp
  FROM produk p JOIN users u ON u.id = p.user_id";
$params = [];
if ($q !== '') {
  $sql .= ' WHERE p.nama_produk LIKE ? OR p.deskripsi_produk LIKE ? OR u.nama LIKE ?';
  $term = '%' . $q . '%';
  $params = [$term, $term, $term];
}
$sql .= ' ORDER BY p.created_at DESC, p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
foreach ($rows as &$row) {
  $row['id'] = (int) $row['id'];
  $row['user_id'] = (int) $row['user_id'];
  $row['seller_id'] = (int) $row['seller_id'];
  $row['seller_whatsapp'] = normalize_whatsapp($row['seller_whatsapp']);
}
unset($row);

echo json_encode(['products' => $rows], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
