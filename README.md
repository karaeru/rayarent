# RAYARENT

Aplikasi web pemesanan sewa mobil dan minibus untuk **RAYARENT (Ragil Raya Trans), Yogyakarta**.
Pengunjung bisa melihat armada dan harga tanpa login, pelanggan memesan dan membayar secara online,
dan admin mengelola pesanan, pembayaran, armada, serta laporan pendapatan.

> Prototype untuk tugas. Akun dan data di bawah adalah **data demo**.

---

## Arah desain

Panduan warna, tipografi, gerak, dan aturan aksesibilitas ada di [`DESIGN.md`](DESIGN.md). Baca file itu sebelum mengubah tampilan.

---

## Fitur

### Pengunjung (tanpa login)
- **Landing page**: slide logo / foto pelanggan / kontak, daftar armada dengan filter kategori, daftar harga per layanan, cara sewa, ketentuan singkat.
- **Halaman detail kendaraan** publik (`/armada/innova-zenix`) dengan data harga untuk Google (schema.org).
- Halaman **Tentang**, **Kontak** (Google Maps + form tanya via WhatsApp), dan **Syarat & Ketentuan**.
- Tombol WhatsApp melayang, menu responsif untuk HP.

### Pelanggan
- Daftar, masuk, **lupa password** (link reset via email).
- Katalog, pemesanan dengan **cek unit kosong otomatis** (tidak bisa pesan dobel di jam yang sama).
- Pilih bayar **DP atau lunas**, unggah bukti transfer, lihat riwayat pembayaran dan sisa tagihan.
- Wajib menyetujui **Syarat & Ketentuan** saat memesan (waktu dan versi tercatat).
- Pesanan tanpa bukti bayar **batal otomatis** setelah batas waktu (bawaan 2 jam).
- **Invoice** siap cetak / simpan PDF.

### Admin
- **Dashboard**: grafik pendapatan 30 hari, pesanan terlambat, jadwal serah terima, pembayaran yang menunggu verifikasi.
- Kelola pesanan: verifikasi pembayaran, konfirmasi, serah terima, **pengembalian** (denda telat per jam + biaya kerusakan otomatis), pelunasan tunai, batal.
- Kelola armada: kendaraan, paket harga, unit (plat nomor), foto (otomatis dikecilkan dan dibersihkan dari data GPS), hapus kendaraan.
- **Laporan** dengan filter tanggal/status dan **ekspor CSV**.
- **Pengaturan**: persen DP, denda per jam, batas waktu bayar.

### Alur status pesanan
```
menunggu_konfirmasi ──(bayar diverifikasi + dikonfirmasi)──► dikonfirmasi ──(serah terima)──► sedang_disewa ──(dikembalikan + lunas)──► selesai
        │                                                         │
        └── dibatalkan (pelanggan / admin / otomatis) ◄───────────┘
```

---

## Teknologi
- PHP 8.3, **Laravel 13**
- MySQL / MariaDB
- Blade + **Tailwind CSS v4** (dibangun dengan Vite) dan JavaScript murni
- Migrasi bertahap: halaman publik, Masuk/Daftar, dan halaman pelanggan sudah Tailwind; halaman admin masih memakai `public/css/rayarent.css`
- PHPUnit untuk test

---

## Instalasi (Laragon / XAMPP)

```bash
git clone https://github.com/karaeru/rayarent.git
cd rayarent
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
npm install                   # sekali saja, butuh Node.js (sudah ada di Laragon)
npm run build                 # wajib: membangun tampilan Tailwind ke public/build
```

> Saat sedang mengubah tampilan, jalankan `npm run dev` dan biarkan terminalnya terbuka, supaya perubahan langsung terlihat.
> Bila halaman publik menampilkan pita kuning "Tampilan baru belum dibangun", jalankan `npm run build`.

1. Buat database **`rayarent`** (untuk aplikasi) dan **`rayarent_test`** (untuk test) di phpMyAdmin.
2. Sesuaikan `DB_USERNAME` / `DB_PASSWORD` di `.env` bila perlu.
3. Buat tabel dan isi data demo:

```bash
php artisan migrate:fresh --seed
```

4. Buka **http://rayarent.test** (Laragon) atau jalankan `php artisan serve` lalu buka http://127.0.0.1:8000.

> Bila muncul *`php` is not recognized*, buka terminal dari tombol **Terminal** di Laragon.

---

## Akun demo

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@rayarent.id` | `admin123` |
| Pelanggan | `budi@mail.com` | `budi123` |
| Pelanggan | `siti@mail.com`, `andi@mail.com`, `dewi@mail.com`, `rizky@mail.com` | `demo123` |

### Isi data demo
- 28 kendaraan dengan paket harga dan 74-an unit berplat nomor acak.
- 25 pesanan di semua status:
  - 16 **selesai** (sebagian kena denda telat atau biaya kerusakan)
  - 3 **sedang disewa** (1 sudah lewat jadwal kembali, muncul di dashboard)
  - 2 **dikonfirmasi** (jadwal serah terima)
  - 3 **menunggu**: bukti menunggu verifikasi, bukti ditolak, belum bayar
  - 1 **dibatalkan** otomatis

Pesanan "menunggu" dibuat 20–90 menit sebelum seeder dijalankan. Karena ada batas bayar 2 jam,
pesanan yang belum bayar akan batal sendiri setelah itu. Untuk presentasi, ubah batas waktu bayar ke `0`
di *Admin → Pengaturan*, atau reset data sesaat sebelum demo (baca peringatan di bawah dulu).

> ⚠️ **`migrate:fresh` menghapus seluruh isi database** (akun, pesanan, dan hubungan foto–kendaraan).
> Untuk update biasa setelah `git pull`, cukup jalankan `php artisan migrate`.
> Foto kendaraan tetap aman karena dipasang ulang otomatis dari `peta-foto.json` (lihat bagian *Foto kendaraan*).

---

## Konfigurasi `.env` (opsional)

Identitas yang tampil di website. Yang dikosongkan tidak ditampilkan.

```env
RAYARENT_NAMA=RAYARENT
RAYARENT_TAGLINE="Sewa kendaraan mudah dan terpercaya"
RAYARENT_KOTA=Yogyakarta
RAYARENT_WHATSAPP=6281234567890
RAYARENT_EMAIL=halo@rayarent.id
RAYARENT_INSTAGRAM=rayarent.id
RAYARENT_ALAMAT="Jl. Contoh No. 1, Bantul, Yogyakarta"
RAYARENT_MAPS="-7.83,110.33"          # koordinat untuk pin peta
RAYARENT_JAM="Setiap hari, 07.00-21.00"
RAYARENT_SEJAK=2019
RAYARENT_SK_VERSI=2026-11-25          # ganti setiap isi Syarat & Ketentuan diubah
```

**Rekening tujuan transfer** diatur di `config/rayarent.php` bagian `'rekening'`. Saat ini berisi
3 rekening **dummy** (BCA, BNI, Mandiri). Ganti dengan rekening asli bila aplikasi dipakai sungguhan.

Email (lupa password) secara bawaan hanya ditulis ke `storage/logs/laravel.log` (`MAIL_MAILER=log`).

---

## Gambar

| Lokasi | Isi | Ikut Git? |
|---|---|---|
| `public/images/logo.png` | Logo di header, footer, invoice, slide pertama | ✅ |
| `public/images/pelanggan/` | Foto pelanggan untuk slide 2–5. Nama file jadi keterangan, contoh `01-rombongan-ke-dieng.jpg` | ✅ |
| `storage/app/private/vehicles/` | Foto kendaraan (diunggah lewat admin, ditampilkan lewat `/foto/{id}`) | ✅ |
| `storage/app/private/vehicles/peta-foto.json` | Daftar "nama kendaraan → file foto" | ✅ |
| `storage/app/private/proofs/` | Bukti transfer pelanggan (privat) | ❌ sengaja tidak |

### Foto kendaraan
Website membaca foto dari kolom `photo` di tabel `vehicles`, lalu mengambil filenya dari
`storage/app/private/vehicles/`. Karena isi database tidak ikut Git, setiap kali foto diunggah, diganti,
atau dihapus lewat admin, aplikasi juga menulis **`peta-foto.json`** yang ikut Git.

Peta ini dipakai otomatis oleh `php artisan migrate:fresh --seed` / `db:seed`, jadi foto langsung muncul lagi
setelah reset data atau di laptop lain. Kendaraan dicocokkan berdasarkan **nama**, jadi jangan ganti nama
kendaraan di seeder tanpa mengunggah ulang fotonya.

| Situasi | Perintah |
|---|---|
| Foto hilang setelah reset / di laptop teman | `php artisan armada:foto` |
| Foto di database sudah benar tapi peta belum ada (pertama kali) | `php artisan armada:foto --simpan`, lalu commit `peta-foto.json` |

---|---|
| `public/images/logo.png` | Logo di header, footer, invoice, slide pertama |
| `public/images/pelanggan/` | Foto pelanggan untuk slide 2–5. Nama file jadi keterangan, contoh `01-rombongan-ke-dieng.jpg` |
| `storage/app/private/vehicles/` | Foto kendaraan (diunggah lewat admin, disajikan lewat `/foto/{id}`) |
| `storage/app/private/proofs/` | Bukti transfer pelanggan (privat, tidak masuk Git) |

---

## Perintah artisan

| Perintah | Fungsi |
|---|---|
| `php artisan migrate:fresh --seed` | Reset database dan isi data demo |
| `php artisan admin:buat email@domain.com` | Buat akun admin baru (password ditanyakan) |
| `php artisan armada:impor file.csv [--cek] [--ganti]` | Impor plat nomor unit dari CSV (kolom: kendaraan, plat, status) |
| `php artisan armada:foto [--simpan]` | Pasang ulang foto kendaraan dari `peta-foto.json` / tulis peta dari database |
| `php artisan pesanan:batal-kedaluwarsa` | Batalkan pesanan tanpa bukti bayar yang lewat batas waktu |
| `php artisan schedule:work` | Menjalankan jadwal otomatis (batal kedaluwarsa tiap 5 menit) selama dibiarkan terbuka |
| `php artisan test` | Menjalankan semua test (memakai database `rayarent_test`) |

Batal otomatis juga dicek setiap ada pengecekan ketersediaan atau pesanan baru, jadi tetap jalan
walau `schedule:work` tidak dinyalakan.

---

## Bekerja berdua (sinkron lewat Git)

**Yang mengirim perubahan:**
```bash
git status            # pastikan tidak ada .env atau *.sql
git add .
git commit -m "pesan singkat"
git push
```

**Yang mengambil perubahan** (terminal Laragon):
```bash
git pull origin main
composer install
php artisan migrate
php artisan armada:foto
npm install
npm run build
php artisan optimize:clear
```

| Ikut Git | Tidak ikut Git (kirim manual / buat sendiri) |
|---|---|
| Kode, tampilan, migration, seeder | `.env` (kirim file langsung bila isinya berubah) |
| Logo, foto pelanggan, foto kendaraan + `peta-foto.json` | Isi database: akun & pesanan buatan sendiri (export/import `.sql` lewat phpMyAdmin) |
| | Bukti transfer pelanggan |
| | Database `rayarent_test` (buat kosong untuk menjalankan test) |
| | `node_modules/` dan `public/build/` (dibuat ulang dengan `npm install` + `npm run build`) |

Bila `git pull` menolak karena *"local changes would be overwritten"*, backup file yang disebut,
lalu jalankan `git restore <file>` atau `git stash`, baru `git pull` lagi.

---

## Struktur penting

```
app/
├── Console/Commands/        admin:buat, armada:impor, armada:foto, pesanan:batal-kedaluwarsa
├── Http/Controllers/
│   ├── HomeController       landing, detail armada publik, tentang, kontak, S&K
│   ├── BookingController    form pesan + cek ketersediaan
│   ├── OrderController      pesanan pelanggan, bayar, invoice, bukti
│   ├── AdminController      dashboard, pesanan, laporan
│   └── AdminVehicleController / AdminSettingController
└── Models/                  Vehicle, VehicleUnit, RentalPackage, Order, Payment, Setting, User
resources/views/
├── app.blade.php            layout (header, footer, menu)
├── home.blade.php           landing page
├── armada / tentang / kontak / syarat.blade.php
├── invoice.blade.php
└── admin/
resources/css/app.css        Tailwind: token warna/font dari DESIGN.md + komponen bergerak
resources/views/components/  vehicle-card, crumbs, section-head, page-head, auth-shell, car-icon
public/css/rayarent.css      gaya lama untuk halaman yang belum dimigrasi (ikut dimuat lewat app.css)
```

---

## Catatan keamanan (prototype)
- Akun demo **tidak dibuat** saat `APP_ENV=production`; gunakan `php artisan admin:buat`.
- Peran pengguna selalu `pelanggan` saat daftar dan tidak bisa diisi dari form.
- Bukti transfer dan foto disimpan di disk privat dan hanya bisa dibuka pemilik pesanan / admin.
- Jangan commit `.env` dan file `*.sql` (berisi data pelanggan dan akun).
