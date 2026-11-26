# tubes-proweb
Kania,Abrar,Alif,Raniyah,Azzahra,Daiyan

## Database dan deployment

Registrasi, profil, katalog produk, dan nomor WhatsApp disimpan melalui PHP/PDO ke database MySQL yang dikonfigurasi di `danusu_php_backend/config/database.php`. Untuk deployment online, host semua halaman HTML dan backend PHP pada server yang sama dan arahkan semua request ke satu database MySQL bersama dengan environment variables berikut:

- `DB_HOST`: host MySQL server
- `DB_PORT`: port MySQL (default `3306`)
- `DB_NAME`: nama database (default lokal: `db_login`)
- `DB_USER`: akun database
- `DB_PASSWORD`: kata sandi akun database

Jalankan `danusu_php_backend/database.sql` pada database tersebut. Jangan membuka halaman dengan `file://` atau memakai Live Server statis saja; PHP API perlu berjalan di web server. Server lokal `localhost` hanya dapat diakses dari komputer itu sendiri sampai aplikasi dipasang pada hosting/domain yang bisa dijangkau pengguna lain.
