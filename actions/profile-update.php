<?php
require_once __DIR__ . '/../config/bootstrap.php';
if (!me()) redirect('../../login.html');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../../profilesaya.html');
verify_token();

try {
  $nama = trim($_POST['nama'] ?? '');
  $email = trim($_POST['email'] ?? '');
  if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Nama dan email wajib diisi dengan format yang benar.');

  $sql = 'UPDATE users SET nama=?,email=?,whatsapp=?,nim=?,fakultas=?,prodi=?,alamat=? WHERE id=?';
  $params = [
    $nama, $email,
    trim($_POST['whatsapp'] ?? ''),
    trim($_POST['nim'] ?? ''),
    trim($_POST['fakultas'] ?? ''),
    trim($_POST['prodi'] ?? ''),
    trim($_POST['alamat'] ?? '')
  ];
  $params[] = me()['id'];
  $pdo->prepare($sql)->execute($params);

  $_SESSION['user']['nama'] = $nama;
  flash('Profil berhasil diperbarui.', 'success');
  redirect('../../profilesaya.html');
} catch (Throwable $e) {
  flash($e instanceof PDOException ? 'Gagal menyimpan profil. NIM atau email mungkin sudah digunakan.' : $e->getMessage(), 'error');
  redirect('../../profilesaya.html');
}
