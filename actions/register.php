<?php
<<<<<<< HEAD:danusu_php_backend/actions/register.php
require_once __DIR__ . '/../config/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../../akun.html');
=======
$bootstrap = __DIR__ . '/config/bootstrap.php';

if (!file_exists($bootstrap)) {
    die('File bootstrap tidak ditemukan: ' . $bootstrap);
}

require_once $bootstrap;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../akun.html');
>>>>>>> 2b60d3ec485e1a99f48c50cbb2e9195c609e9ad5:actions/register.php
$required=['nama','email','password','konfirmasi_password','whatsapp','nim','fakultas','prodi','alamat'];
foreach($required as $k) if(trim($_POST[$k] ?? '')==='') exit("Kolom $k wajib diisi.");
if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) exit('Format email tidak valid.');
if($_POST['password'] !== $_POST['konfirmasi_password']) exit('Konfirmasi password tidak cocok.');
if(empty($_POST['terms'])) exit('Anda harus menyetujui syarat dan ketentuan.');
if(strlen($_POST['password']) < 8) exit('Password minimal 8 karakter.');
$nim=trim($_POST['nim']); $email=trim($_POST['email']);
$role=($_POST['role']??'')==='penjual'?'penjual':'pembeli';
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
  $ktmName=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
  if(!move_uploaded_file($_FILES['ktm']['tmp_name'],__DIR__.'/../uploads/ktm/'.$ktmName)) exit('Gagal menyimpan file KTM.');
  $ktm='uploads/ktm/'.$ktmName;
}
$sql='INSERT INTO users (nama,email,password,whatsapp,nim,fakultas,prodi,alamat,ktm,role) VALUES (?,?,?,?,?,?,?,?,?,?)';
$pdo->prepare($sql)->execute([trim($_POST['nama']),$email,password_hash($_POST['password'],PASSWORD_DEFAULT),trim($_POST['whatsapp']),$nim,trim($_POST['fakultas']),trim($_POST['prodi']),trim($_POST['alamat']),$ktm,$role]);
$next=$_POST['next']??'';
$query=in_array($next,['tambahkan.html','profilesaya.html','produksaya.html','edit.html'],true)?'?next='.rawurlencode($next):'';
redirect('../../login.html'.$query);
