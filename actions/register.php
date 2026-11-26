<?php
$bootstrap = __DIR__ . '/config/bootstrap.php';

if (!file_exists($bootstrap)) {
    die('File bootstrap tidak ditemukan: ' . $bootstrap);
}

require_once $bootstrap;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../akun.html');
$required=['nama','email','password','konfirmasi_password','whatsapp','nim','fakultas','prodi','alamat'];
foreach($required as $k) if(trim($_POST[$k] ?? '')==='') exit("Kolom $k wajib diisi.");
if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) exit('Format email tidak valid.');
if($_POST['password'] !== $_POST['konfirmasi_password']) exit('Konfirmasi password tidak cocok.');
if(empty($_POST['terms'])) exit('Anda harus menyetujui syarat dan ketentuan.');
if(strlen($_POST['password']) < 8) exit('Password minimal 8 karakter.');
$nim=trim($_POST['nim']); $email=trim($_POST['email']);
$check=$pdo->prepare('SELECT id FROM users WHERE nim=? OR email=? LIMIT 1');
$check->execute([$nim,$email]);
if($check->fetch()) exit('NIM atau email sudah terdaftar.');
$ktm=null;
if(isset($_FILES['ktm']) && $_FILES['ktm']['error']!==UPLOAD_ERR_NO_FILE){
  if($_FILES['ktm']['error']!==UPLOAD_ERR_OK) exit('Upload KTM gagal.');
  if($_FILES['ktm']['size']>3*1024*1024) exit('Ukuran KTM maksimal 3MB.');
  $mime=(new finfo(FILEINFO_MIME_TYPE))->file($_FILES['ktm']['tmp_name']);
  $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  if(!isset($allowed[$mime])) exit('KTM harus berupa JPG, PNG, atau WEBP.');
  $ktm=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
  if(!move_uploaded_file($_FILES['ktm']['tmp_name'],__DIR__.'/../uploads/ktm/'.$ktm)) exit('Gagal menyimpan file KTM.');
}
$sql='INSERT INTO users (nama,email,password,whatsapp,nim,fakultas,prodi,alamat,ktm) VALUES (?,?,?,?,?,?,?,?,?)';
$pdo->prepare($sql)->execute([trim($_POST['nama']),$email,password_hash($_POST['password'],PASSWORD_DEFAULT),trim($_POST['whatsapp']),$nim,trim($_POST['fakultas']),trim($_POST['prodi']),trim($_POST['alamat']),$ktm]);
$_SESSION['user_id']=(int)$pdo->lastInsertId(); $_SESSION['user_nama']=trim($_POST['nama']);
redirect('../profilesaya.php');
