# RAYARENT

Aplikasi web pemesanan sewa mobil dan minibus untuk **RAYARENT (Ragil Raya Trans), Yogyakarta**.
Pengunjung bisa melihat armada dan harga tanpa login, pelanggan memesan dan membayar secara online,
dan admin mengelola pesanan, pembayaran, armada, serta laporan pendapatan.

> Prototype untuk tugas. Akun dan data di bawah adalah **data demo**.

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
- Blade + CSS dan JavaScript murni (tanpa framework front-end, tanpa build step)
- PHPUnit untuk test

---

## Instalasi (Laragon / XAMPP)

```bash
git clone https://github.com/karaeru/rayarent.git
cd rayarent
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

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
**jalankan `php artisan migrate:fresh --seed` sesaat sebelum demo**, atau ubah batas waktu bayar ke `0`
di *Admin → Pengaturan* supaya tidak batal sendiri.

Foto kendaraan tidak termasuk data demo. Unggah lewat *Admin → Kendaraan → Kelola → Foto*.

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
RAYARENT_REKENING="BCA 1234567890 a.n. RAYARENT"
RAYARENT_SEJAK=2019
RAYARENT_SK_VERSI=2026-11-25          # ganti setiap isi Syarat & Ketentuan diubah
```

Email (lupa password) secara bawaan hanya ditulis ke `storage/logs/laravel.log` (`MAIL_MAILER=log`).

---

## Gambar

| Lokasi | Isi |
|---|---|
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
| `php artisan pesanan:batal-kedaluwarsa` | Batalkan pesanan tanpa bukti bayar yang lewat batas waktu |
| `php artisan schedule:work` | Menjalankan jadwal otomatis (batal kedaluwarsa tiap 5 menit) selama dibiarkan terbuka |
| `php artisan test` | Menjalankan semua test (memakai database `rayarent_test`) |

Batal otomatis juga dicek setiap ada pengecekan ketersediaan atau pesanan baru, jadi tetap jalan
walau `schedule:work` tidak dinyalakan.

---

## Struktur penting

```
app/
├── Console/Commands/        admin:buat, armada:impor, pesanan:batal-kedaluwarsa
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
public/css/rayarent.css      semua gaya tampilan (warna brand di blok :root paling atas)
```

---

## Catatan keamanan (prototype)
- Akun demo **tidak dibuat** saat `APP_ENV=production`; gunakan `php artisan admin:buat`.
- Peran pengguna selalu `pelanggan` saat daftar dan tidak bisa diisi dari form.
- Bukti transfer dan foto disimpan di disk privat dan hanya bisa dibuka pemilik pesanan / admin.
- Jangan commit `.env` dan file `*.sql`.
