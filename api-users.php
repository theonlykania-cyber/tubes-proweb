<?php
require_once __DIR__ . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$query = trim($_GET['q'] ?? '');
$sql = 'SELECT id, nama, nim, fakultas, prodi, role FROM users';
$params = [];
if ($query !== '') {
  $sql .= ' WHERE nama LIKE ? OR fakultas LIKE ? OR prodi LIKE ?';
  $term = '%' . $query . '%';
  $params = [$term, $term, $term];
}
$sql .= ' ORDER BY nama ASC LIMIT 50';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
foreach ($users as &$user) {
  $user['id'] = (int) $user['id'];
}
unset($user);

echo json_encode(['users' => $users], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
