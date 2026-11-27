<?php
require '../config/bootstrap.php';
if (!me()) redirect('../../login.html?next=tambahkan.html');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../tambahkan.html');
verify_token();
if (me()['role'] !== 'penjual') {
	flash('Hanya penjual yang dapat memasukkan produk', 'error');
	redirect('../index.html');
}

try {
	$name = trim($_POST['nama_produk'] ?? '');
	$description = trim($_POST['deskripsi_produk'] ?? '');
	$productStatus = $_POST['status_produk'] ?? '';
	$whatsapp = normalize_whatsapp($_POST['whatsapp'] ?? '');
	if ($name === '' || $description === '' || !in_array($productStatus, ['ready_stock', 'pre_order', 'sold_out'], true)) {
		throw new Exception('Data produk tidak valid.');
	}
	if (strlen($whatsapp) < 8 || strlen($whatsapp) > 15) {
		throw new Exception('Nomor WhatsApp tidak valid.');
	}

	$image = upload_img('gambar_produk', 'products');
	$stmt = $pdo->prepare('INSERT INTO produk(user_id,nama_produk,deskripsi_produk,gambar_produk,status_produk,whatsapp) VALUES(?,?,?,?,?,?)');
	$stmt->execute([me()['id'], $name, $description, $image, $productStatus, $whatsapp]);
	flash('Produk berhasil ditambahkan.');
} catch (Throwable $error) {
	flash($error instanceof PDOException ? 'Pastikan database.sql sudah dijalankan.' : $error->getMessage(), 'error');
}
redirect('../my-products.php');