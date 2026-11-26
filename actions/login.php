<?php
require_once __DIR__ . '/../config/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('../login.html');
$nim=trim($_POST['nim']??''); $password=$_POST['password']??'';
$stmt=$pdo->prepare('SELECT id,nama,password FROM users WHERE nim=? LIMIT 1'); $stmt->execute([$nim]); $u=$stmt->fetch();
if(!$u || !password_verify($password,$u['password'])) exit('NIM atau password salah.');
session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; $_SESSION['user_nama']=$u['nama'];
redirect('../profilesaya.php');
