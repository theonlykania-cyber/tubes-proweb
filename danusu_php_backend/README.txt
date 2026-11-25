DANUSUPROJECT PHP BACKEND (PHP 8+, MySQL)

INSTALL DI XAMPP:
1. Extract folder dan letakkan di C:/xampp/htdocs/danusu_php_backend
2. Start Apache dan MySQL.
3. Di phpMyAdmin, pilih database db_login.
4. Jalankan database.sql untuk membuat tabel produk (gunakan hanya jika tabel produk belum memiliki kolom ini).
5. Cek config/database.php: db_login, username root, password default kosong. Ubah jika konfigurasi lokal berbeda.
6. Buka http://localhost/danusu_php_backend/

TERINTEGRASI:
- Registrasi user sesuai kolom pada screenshot users: nama,email,password,role,nim,fakultas,prodi,whatsapp,ktm,alamat; foto_profil dapat diubah.
- Role pembeli/penjual, login NIM dan password hash, logout.
- Saat registrasi penjual, row toko dibuat menggunakan user_id,nama_toko,deskripsi.
- Edit toko dan profil.
- Produk katalog/detail, upload gambar, tambah/edit/hapus dengan ownership.
- Hubungi penjual via WhatsApp.
- PDO prepared statements, CSRF token, validasi upload 3 MB JPG/PNG/WEBP.

STRUKTUR TABEL PRODUK diasumsikan sesuai database.sql:
id,user_id,nama_produk,deskripsi_produk,gambar_produk,status_produk,whatsapp,created_at,updated_at.
Tabel users dan toko mengikuti struktur yang kamu sebutkan. Bila database produk yang kamu buat berbeda, sesuaikan nama kolom di SQL/PHP.

HTML asli kamu disertakan di original_html sebagai referensi. Aplikasi ini menyediakan halaman PHP operasional dengan alur backend tersambung; silakan migrasikan styling spesifik halaman HTML jika ingin tampilan persis sama.
