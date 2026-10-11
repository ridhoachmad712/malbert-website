# Website Sekolah

Website publik sekolah yang berdiri terpisah dari aplikasi TEBAK AKSI. Dibuat dengan Laravel 13 dan PHP 8.3. Warna, tipografi, dan pola kartu mengikuti identitas visual awal AksiKita; data aplikasi dan akun tidak dibagikan.

## Menjalankan secara lokal

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Buat berkas kosong `database/database.sqlite`, lalu jalankan:

```sh
php artisan migrate
php artisan storage:link
php artisan site:create-admin
php artisan serve
```

Buka alamat yang ditampilkan `artisan serve`. Panel pengelola berada di `/masuk`. Perintah `site:create-admin` meminta nama, email, dan kata sandi secara interaktif tanpa menampilkannya di terminal.

## Isi dan peran

Halaman publik meliputi beranda, profil sekolah, direktori dan profil guru, berita, pengumuman, galeri, dan kontak. Nama serta informasi sekolah diisi melalui panel pengelola. Profil guru baru tampil setelah persetujuan publikasi dicatat. Konten dimulai sebagai draf; penulis dapat mengirimnya untuk peninjauan, sedangkan editor atau admin dapat menerbitkan. Admin juga mengelola profil sekolah, guru, dan akun staf.

Jangan masukkan data murid atau rahasia aplikasi TEBAK AKSI ke proyek ini. Untuk produksi, isi `.env` khusus website, atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS, database, dan akses tulis `storage` serta `bootstrap/cache`. Arahkan document root hosting ke folder `public`. Isi konten asli dan pastikan peringatan keamanan domain yang pernah muncul sudah diselesaikan sebelum dibagikan kepada publik.

## Pemeriksaan

```sh
php artisan test --compact
php artisan view:cache
```
