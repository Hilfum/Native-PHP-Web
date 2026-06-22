# File Monitoring System

Aplikasi web sederhana untuk mengelola data berkas (dokumen) menggunakan PHP dan MySQL. Awalnya dibuat untuk mencatat dan memantau berkas pajak, tapi strukturnya cukup umum untuk dipakai mencatat berkas apa saja.

## Apa yang bisa dilakukan

- Login dan manajemen user
- Input data berkas baru
- Lihat daftar berkas yang sudah tersimpan
- Edit dan hapus data berkas
- Konfirmasi sebelum data dihapus/diubah

## Teknologi yang dipakai

PHP native (tanpa framework), MySQL, plus CSS dan sedikit JavaScript untuk tampilan.

## Struktur file

```
├── css/              -> styling
├── javascript/        -> script JS
├── gambar/            -> aset gambar
├── database/          -> file SQL untuk bikin tabel
├── koneksi.php         -> koneksi ke database
├── login.php / logout.php
├── add_user.php
├── input.php           -> form tambah berkas
├── show.php / show_data.php  -> tampilkan data berkas
├── edit.php
├── delete.php
└── confirm.php
```

## Cara jalankan di lokal

1. Siapkan XAMPP (atau sejenisnya) yang sudah ada PHP dan MySQL
2. Clone repo ini ke folder `htdocs`
3. Buat database baru, lalu import file SQL yang ada di folder `database/`
4. Buka `koneksi.php`, sesuaikan nama database, username, dan password sesuai punyamu
5. Jalankan Apache & MySQL dari XAMPP
6. Akses lewat browser, mulai dari `login.php` atau `index.php`

## Catatan

Project ini dibuat untuk keperluan belajar/tugas, jadi belum ada validasi keamanan yang ketat. Kalau mau dipakai untuk hal yang lebih serius, sebaiknya tambahkan validasi input dan proteksi terhadap SQL injection terlebih dahulu.

## Lisensi

MIT License — bebas dipakai dan dimodifikasi.
