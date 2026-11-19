# SITARU SPMB Jawa Tengah

SITARU adalah aplikasi penerimaan murid baru daring untuk SMA dan SMK Negeri di Provinsi Jawa Tengah. Aplikasi mendukung pendaftaran, verifikasi dan aktivasi akun, pemilihan sekolah atau jurusan, seleksi, serta pengelolaan oleh operator dan admin.

## Persyaratan

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- Database yang didukung Laravel

## Menjalankan aplikasi

```bash
composer run setup
composer run dev
```

Atur koneksi database pada file `.env` sebelum menjalankan setup. Perintah setup memasang dependensi, menyiapkan kunci aplikasi, menjalankan migrasi, dan membangun aset frontend.

## Pengujian

```bash
composer test
```
