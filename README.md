# Product Manager - Toko Bangunan

Aplikasi web PHP-MySQL untuk mengelola produk toko bangunan.
CRUD lengkap, validasi server-side, CSRF protection, PRG pattern, search/filter, pagination, dan upload gambar.

## Prasyarat

- **Laragon** (Apache + PHP 8.1+ + MySQL/MariaDB)
- **phpMyAdmin** (bawaan Laragon)

## Setup

### 1. Clone / Copy Project

Letakkan folder `ProductManager` di `C:\laragon\www\` sehingga dapat diakses via `http://localhost/ProductManager/`.

Jika project berada di lokasi lain (misal `D:\Project\ProductManager`), buat Virtual Host di Laragon:
- Klik kanan tray Laragon > **Apache** > **sites-enabled** > tambah config
- Atau gunakan fitur **Quick app** > **Virtual Host**

### 2. Import Database

1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Klik tab **Import**
3. Pilih file `database/store_db.sql`
4. Klik **Go**

Atau via terminal Laragon:
```bash
mysql -u root < database/store_db.sql
```

### 3. Cek Koneksi Database

File `config/db.php` sudah disetel untuk default Laragon:
- Host: `localhost`
- User: `root`
- Password: *(kosong)*
- Database: `store_db`

Ubah jika konfigurasi berbeda.

### 4. Pastikan Folder Uploads Writable

Folder `uploads/` harus dapat ditulis oleh web server. Di Laragon Windows, ini biasanya sudah otomatis.

### 5. Akses Aplikasi

Buka browser: `http://localhost/ProductManager/`

## Struktur Project

```
ProductManager/
  config/
    db.php              PDO connection
  includes/
    functions.php       CSRF, validasi, flash, upload, helpers
    header.php          Template header + navigasi
    footer.php          Template footer + JS
  assets/
    css/
      style.css         Stylesheet (dark theme)
  uploads/
    .htaccess           Blokir eksekusi PHP
  sql/
    store_db.sql          Skema database + seed data
  index.php             Daftar produk (Read)
  create.php            Tambah produk (Create)
  edit.php              Edit produk (Update)
  delete.php            Hapus produk (Delete)
  README.md
```

## Fitur

| Fitur | Detail |
|---|---|
| **Create** | Form tambah produk + upload gambar, validasi server, PRG redirect |
| **Read** | Card grid responsif (Flexbox), search by nama, filter by kategori, pagination |
| **Update** | Form edit terisi data, ganti gambar, validasi unik exclude self |
| **Delete** | POST + CSRF token, konfirmasi browser, hapus file gambar |
| **Validasi** | Nama 3-100 karakter + unik, harga > 0, stok >= 0, kategori valid |
| **Keamanan** | PDO prepared statements, htmlspecialchars output, CSRF token, upload MIME check |
| **Upload** | JPEG/PNG/WebP, maks 2MB, validasi tipe via finfo |
| **Responsif** | 1-4 kolom sesuai viewport, mobile-first |

## Kategori Produk

Semen & Beton, Besi & Baja, Kayu & Papan, Cat & Finishing, Pipa & Sanitasi, Listrik & Kabel, Atap & Genteng, Peralatan Tangan, Pasir & Batu, Pintu & Jendela.

## Pengujian

### Kasus Normal
- Tambah produk baru dengan semua field valid
- Edit produk, pastikan data lama muncul di form
- Hapus produk, pastikan terhapus dari daftar
- Search produk, filter kategori, navigasi halaman

### Kasus Abnormal
- Submit form kosong (validasi muncul)
- Nama duplikat (error unik muncul)
- Harga 0 atau negatif (ditolak)
- Upload file > 2MB atau format salah (ditolak)
- Refresh setelah submit (PRG mencegah duplikasi)
- Akses delete.php via GET (redirect ke index)
- CSRF token invalid (ditolak, redirect)
