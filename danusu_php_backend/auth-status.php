<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

$user = $_SESSION['user'] ?? null;
$loggedIn = is_array($user) || !empty($_SESSION['user_id']);

echo json_encode([
    'loggedIn' => $loggedIn,
    'nama' => $user['nama'] ?? $_SESSION['user_nama'] ?? null,
    'csrf' => $loggedIn ? token() : null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);