<?php
require '../config/bootstrap.php';
if (!me()) redirect('../../login.html?next=produksaya.html');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../my-products.php');
verify_token();
$id = (int) ($_POST['id'] ?? 0);

try {
	$stmt = $pdo->prepare('SELECT * FROM produk WHERE id=? AND user_id=?');
	$stmt->execute([$id, me()['id']]);
	$old = $stmt->fetch();
	if (!$old) throw new Exception('Produk tidak ditemukan.');

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

	$image = upload_img('gambar_produk', 'products') ?? $old['gambar_produk'];
	$stmt = $pdo->prepare('UPDATE produk SET nama_produk=?,deskripsi_produk=?,gambar_produk=?,status_produk=?,whatsapp=? WHERE id=? AND user_id=?');
	$stmt->execute([$name, $description, $image, $productStatus, $whatsapp, $id, me()['id']]);
	if ($image !== $old['gambar_produk']) del_img($old['gambar_produk']);
	flash('Produk diperbarui.');
} catch (Throwable $error) {
	flash($error instanceof PDOException ? 'Gagal memperbarui database.' : $error->getMessage(), 'error');
}
redirect('../my-products.php');