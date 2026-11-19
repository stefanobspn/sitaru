# SITARU SPMB Jawa Tengah

SITARU adalah aplikasi penerimaan murid baru daring untuk SMA dan SMK Negeri di Provinsi Jawa Tengah. Aplikasi mendukung pendaftaran, verifikasi dan aktivasi akun, pemilihan sekolah atau jurusan, seleksi, serta pengelolaan oleh operator dan admin.

## Requirements

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL / Database yang didukung Laravel

## Installation & Running the App

```bash
composer install
composer run dev
```

Atur koneksi database pada file `.env` sebelum menjalankan setup. Perintah setup memasang dependensi, menyiapkan kunci aplikasi, menjalankan migrasi, dan membangun aset frontend.

## Pengujian

```bash
php artisan test
```

## Panduan Penggunaan
### Untuk calon murid
1. Buat akun dan verifikasi data
   Daftar menggunakan data diri, lalu ikuti proses verifikasi dan aktivasi akun.
2. Lengkapi pendaftaran
   Isi data yang diminta dan pastikan informasinya sesuai dokumen pendukung.
3. Pilih sekolah atau jurusan
   Telusuri pilihan yang tersedia, lalu tentukan sekolah atau jurusan sesuai jalur pendaftaran.
4. Pantau proses seleksi
   Periksa status pendaftaran secara berkala melalui akun.
5. Ikuti pengumuman dan tahap berikutnya
   Jika dinyatakan lolos, ikuti petunjuk daftar ulang atau proses lanjutan yang ditampilkan.

### Untuk operator sekolah dan admin
- Masuk menggunakan akun petugas.
- Periksa dan verifikasi data pendaftaran sesuai kewenangan.
- Kelola informasi sekolah, pilihan, atau proses seleksi sesuai peran.
- Pantau status pendaftaran dan tindak lanjuti data yang memerlukan pemeriksaan.

## Take a peek!
![Tampilan SITARU](docs/img/Screenshot%20From%202026-11-19%2010-07-58.png)

![Tampilan SITARU](docs/img/Screenshot%20From%202026-11-19%2010-08-10.png)

![Tampilan SITARU](docs/img/Screenshot%20From%202026-11-19%2010-08-20.png)

![Tampilan SITARU](docs/img/Screenshot%20From%202026-11-19%2010-08-31.png)
