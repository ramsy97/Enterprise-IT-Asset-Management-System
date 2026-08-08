# Deploy ke InfinityFree / Byet.host (gratis, tanpa kartu kredit)

InfinityFree adalah shared hosting **gratis selamanya** untuk PHP + MySQL,
tanpa SSH dan tanpa Composer. Karena itu paket deploy dibuat di komputer lokal
lalu di-upload lewat File Manager. Skrip build sudah disiapkan:

- `deploy/infinityfree/build-deploy-package.php` — membuat `build/itams-deploy.zip`
- `deploy/infinityfree/templates/setup.php` — installer satu kali (migrate + seed) via browser
- `deploy/infinityfree/templates/htdocs.htaccess` — rewrite `htdocs/` → `public/`
- `deploy/infinityfree/templates/env.production` — template `.env` produksi

## Prasyarat (di komputer kamu)

- PHP >= 8.2 dengan ekstensi `zip` (untuk membuat paket)
- Composer (dependency Laravel)
- Node.js (untuk build asset frontend, opsional jika `public/build` sudah ada)

## Langkah 1 — Buat paket deploy

Di folder repo:

```bash
php deploy/infinityfree/build-deploy-package.php --no-build
```

Proses yang berjalan:

1. `composer install --no-dev` dilakukan **di dalam staging** — vendor lokal
   (berisi alat dev) tidak disentuh.
2. Menghasilkan `APP_KEY`, `APP_SETUP_TOKEN`, dan `CRON_TOKEN` (acak).
3. Menyalin seluruh aplikasi (tanpa `.git`, `tests`, `docs`, `node_modules`)
   ke `build/itams-deploy/`, lalu men-zip menjadi `build/itams-deploy.zip`.

Skrip menampilkan semua langkah + token di akhir. **Simpan outputnya.**

> Tanpa `--no-build`, skrip menjalankan `npm ci && npm run build` bila
> `public/build/manifest.json` belum ada.

## Langkah 2 — Buat akun & website

1. Daftar di https://www.infinityfree.com (bebas kartu kredit).
2. **Create Account** → ikuti verifikasi email.
3. Buat website baru → catat subdomain gratis kamu, contoh
   `username.infinityfreeapp.com`.

## Langkah 3 — Siapkan database MySQL

1. Control Panel → **MySQL Databases** → **New Database**.
2. Catat 4 hal ini (dipakai di `.env`):
   - `DB host` — contoh `sql301.epizy.com`
   - `DB name` — contoh `if0_12345678_itams`
   - `DB user` — contoh `if0_12345678`
   - `DB password`

## Langkah 4 — Pilih versi PHP

Control Panel → **PHP Settings** → pilih **PHP 8.2** atau **8.3**
(Laravel 12 butuh minimal 8.2).

## Langkah 5 — Upload aplikasi

1. Control Panel → **File Manager** → masuk ke folder `htdocs/`.
2. **Upload** `build/itams-deploy.zip` → tunggu selesai.
3. Klik kanan zip → **Extract** → tunggu (ribuan file). Setelah selesai,
   **hapus** file zip-nya.
4. Struktur hasilnya harusnya:
   ```
   htdocs/
   ├── .env
   ├── .htaccess        <- rewrite ke public/
   ├── setup.php
   ├── artisan
   ├── app/
   ├── public/
   └── vendor/
   ```

## Langkah 6 — Isi `.env`

File Manager → buka `htdocs/.env` → **Edit**, isi:

```
APP_URL=https://username.infinityfreeapp.com
DB_HOST=sql301.epizy.com
DB_DATABASE=if0_12345678_itams
DB_USERNAME=if0_12345678
DB_PASSWORD=<password dari panel>
```

`APP_KEY`, `APP_SETUP_TOKEN`, dan `CRON_TOKEN` sudah terisi otomatis oleh skrip
build — jangan diubah.

## Langkah 7 — Jalankan installer (sekali saja)

Buka di browser:

```
https://username.infinityfreeapp.com/setup.php?token=<APP_SETUP_TOKEN>
```

Output yang benar:

```
optimize:clear ... OK
migrate .......... OK
db:seed .......... OK
storage:link ..... OK   (atau "skipped" — tidak masalah)
Setup finished. Login: admin@itams.local / password
```

> `storage:link` bisa gagal bila host menonaktifkan symlink. Tidak masalah:
> QR code dan bukti audit disajikan lewat route, bukan file `public/storage`.

**WAJIB:** setelah sukses, hapus `htdocs/setup.php` dari File Manager.

## Langkah 8 — Coba akses

- Buka `https://username.infinityfreeapp.com` → login
  `admin@itams.local` / `password`.
- Scan QR / lihat `https://.../qr/<asset_code>/image`.

## Langkah 9 — Scheduler reminder (gratis)

InfinityFree gratis tidak punya cron, jadi pakai cron eksternal gratis:

1. Daftar di https://cron-job.org.
2. **Create cronjob**:
   - URL: `https://username.infinityfreeapp.com/cron/<CRON_TOKEN>`
   - Schedule: *every 5 minutes* (memastikan `itams:send-reminders`
     berjalan jam 07:00 via `schedule:run`).

## Catatan & limitasi

- **Email**: InfinityFree gratis memblokir SMTP keluar. Default `MAIL_MAILER=log`
  menulis email ke `storage/logs`. Untuk kirim sungguhan, pasang
  `resend/resend-laravel` dan set `MAIL_MAILER=resend` + `RESEND_API_KEY`.
- **Database**: pakai MySQL/MariaDB dari panel; driver `database` untuk
  session, cache, dan queue dipakai (tabel sudah dibuat migration).
- **Upload file**: file bukti audit tersimpan di `storage/app/public/evidence`
  dan disajikan lewat route, jadi tetap berfungsi tanpa `storage:link`.
- **Waktu**: proses build + upload pertama agak lama karena ribuan file.
  Untuk update berikutnya cukup ganti file yang berubah (mis. `app/`,
  `routes/`, `vendor/` versi baru, dan `composer.json`/`composer.lock`).
- Jika ingin cepat, pakai **FTP** (tersedia di InfinityFree) untuk upload,
  atau buat ulang zip dan **hapus dulu folder `htdocs/vendor` lama** lalu
  extract — karena menghapus jutaan file via File Manager lambat.

## Update aplikasi (versi berikutnya)

1. Jalankan lagi skrip build (akan pakai `--no-build` bila asset tak berubah).
2. Upload zip baru, extract, **jangan jalankan setup.php lagi** (DB sudah ada —
   running setup akan gagal/tidak merusak karena migrate idempotent, tapi
   `db:seed` akan menambah duplikat admin; hindari).
3. Untuk update schema saja: jalankan
   `https://.../setup.php?token=...` HANYA bila kamu yakin belum ada data
   penting, atau lakukan manual via phpMyAdmin.
