# DESIGN.md · RAYARENT

Arah desain untuk semua halaman RAYARENT. File ini dibaca sebelum mengubah tampilan, supaya setiap keputusan punya alasan yang sama dan tidak ditebak ulang setiap kali.

Status: **v1.1, disetujui sebagian.** Dial, gradasi, dan fase ditunda sudah diputuskan pemilik. Yang masih terbuka ada di bagian 9.

## 1. Karakter

- **Tenang, jelas, terpercaya.** Seperti kantor rental yang rapi, bukan aplikasi gaya hidup.
- Pelanggan memutuskan dengan cepat dan membayar di muka, jadi **harga, jadwal, dan status** harus paling mudah ditemukan.
- Pengguna utama: pelanggan di Yogyakarta yang membuka dari **HP**, dan admin yang bekerja di **laptop**.

Gaya dasar: **Minimalism & Swiss + Flat Design** (rekomendasi data ui-ux-pro-max untuk aplikasi booking dan transportasi). Pola landing: **Conversion-Optimized**, yaitu tombol pesan dan harga selalu dekat.

Yang dihindari: mode gelap sebagai bawaan, glassmorphism di banyak elemen, hero 3D, foto stok orang, testimoni atau angka karangan.

## 2. Warna

| Peran | Nilai | Pemakaian | Kontras |
|---|---|---|---|
| Brand | `#0077B6` | Tombol utama, tautan | 4,87:1 di atas putih |
| Brand gelap | `#005B8C` | Hover, teks brand di atas latar toska muda, ring fokus | 7,3:1 di putih · 6,5:1 di `#E2F6FA` |
| Brand lembut | `#E2F6FA` | Latar tab aktif dan sorotan baris | Teks di atasnya wajib brand gelap |
| Teks | `#111827` / `#374151` | Isi dan judul | > 10:1 |
| Teks redup | `#4B5563` | Keterangan, label kecil | 7,6:1 di putih · 7,0:1 di `#F5F7FA` |
| Sukses / peringatan / bahaya | `#166534` / `#92400E` / `#B91C1C` | Status pesanan dan pembayaran | > 5:1 di latar masing-masing |
| Tombol peringatan | `#B45309` | "Cek bukti bayar" | 5,0:1 dengan teks putih |
| WhatsApp | `#0E7A40` | Tombol melayang | 5,4:1 dengan teks putih |
| Bidang brand | `#005B8C` | Kotak ajakan penutup, slide kontak, kartu pendapatan dasbor | 7,3:1 dengan teks putih |

**Aturan:** satu warna brand (dengan varian gelap dan lembut) dan warna status. **Tidak ada gradasi.** Tidak ada warna lain.

**Keputusan pemilik:** gradasi logo diganti warna solid. Identitas tetap dibawa oleh logo itu sendiri dan oleh warna brand yang diambil dari logo.

## 3. Tipografi

- **Plus Jakarta Sans** untuk judul dan isi. Alasan (R-06): bentuknya ramah dan mudah dibaca, angka tabularnya rapi untuk harga dan jam, tersedia gratis di Google Fonts, dan masuk daftar pasangan font ui-ux-pro-max untuk aplikasi web dan dasbor.
- Ukuran dasar 16 px, tinggi baris 1,5. Isi tidak memakai teks di bawah 12 px.
- Setiap halaman punya **satu `h1`**, dan urutan judul tidak melompat.
- Tidak ada label huruf kapital dengan jarak lebar di atas judul (eyebrow).

## 4. Bentuk dan kedalaman

- Radius: 8 px untuk kontrol, 12 px untuk kartu, kapsul (999 px) **hanya** untuk badge status dan penghitung di tab.
- Kartu memakai **border, bukan bayangan**. Bayangan hanya untuk elemen yang benar-benar melayang di atas halaman: menu titik tiga, dialog, bar simpan Pengaturan, tombol WhatsApp (R-12).
- Ikon: siluet mobil SVG satu warna dan ikon sosial. Tidak ada emoji.

## 5. Dial desain

| Dial | Nilai | Arti |
|---|---|---|
| ENERGY | 3 / 5 | Hidup tapi jelas: judul lebih tegas, foto armada lebih besar, satu titik fokus per bagian |
| RHYTHM | 3 / 5 | Landing berselang antara bagian padat (harga) dan bagian lega (cara sewa); halaman admin padat dan seragam |
| MOTION | 3 / 5 | Gerak bermakna boleh lebih terasa (transisi slide, muncul berurutan, umpan balik filter dan pilihan); efek pamer tetap tidak dipakai; semua berhenti di `prefers-reduced-motion` |

Diputuskan pemilik: **3 / 3 / 3**.

## 6. Gerak yang dipertahankan (R-19)

| Gerak | Alasan |
|---|---|
| Muncul saat di-scroll | Menandai urutan baca di landing; berhenti di reduced motion |
| Pergantian slide pembuka | Satu kotak menampilkan logo, foto, dan kontak tanpa memanjangkan halaman; bisa dihentikan dengan kursor dan tombol |
| Scroll halus ke bagian | Memberi tahu pengunjung bahwa ia tetap di halaman yang sama |
| Hitung mundur batas bayar | Informasi waktu nyata yang menentukan pesanan batal atau tidak |
| Garis urutan Cara sewa | Garis terisi lalu langkah 01–04 muncul berurutan, menegaskan urutan |
| Kartu armada saat filter | Kartu yang lolos filter muncul ulang, jadi pengunjung tahu daftar berubah |
| Judul bagian yang dituju | Judul menyala sebentar setelah scroll dari menu, menandai tempat tiba |
| Bar dan grafik dasbor | Tidak dianimasikan; angka langsung tampil |

Dihapus karena hanya hiasan: mobil melaju di garis jalan, angka berputar seperti odometer, kartu miring mengikuti kursor, cahaya mengikuti kursor, riak di sekitar logo, denyut tombol WhatsApp, titik berdenyut di dasbor.

## 7. Konten

- Angka hanya dari database. Tidak ada statistik, testimoni, atau klaim keamanan karangan (R-17, R-18, R-36).
- Foto pelanggan hanya dengan **izin tertulis** dan dari berkas yang diunggah pemilik (R-23). Sebelum ada, slide memakai foto armada.
- Teks: tanpa em dash, tanpa kata pemasaran kosong. Tombol menyebut aksi yang spesifik ("Pilih kendaraan", "Lihat harga", "Kirim Bukti Pembayaran").
- Panah dipakai hanya bila bermakna: rentang waktu (`26 Nov 08:00 → 27 Nov 08:00`), contoh perhitungan di Pengaturan, dan `↗` untuk tautan yang membuka tab baru.

## 8. Aksesibilitas (wajib)

- Semua teks ≥ 4,5:1 (teks besar ≥ 3:1). Cek dengan `contrast-check.py` di skill `antislop-human`.
- Semua kontrol bisa dicapai dengan Tab, ring fokus 2 px brand gelap.
- Tombol aksi memakai `button`, navigasi memakai `a href` ke tujuan nyata.
- Semua `img` punya `width` dan `height`; gambar dekoratif `alt=""`.
- Area sentuh minimal 44 px di layar sentuh.

## 9. Keputusan (sebelumnya terbuka)

1. Pedoman merek resmi: **tidak ada**. Warna dari logo dan Plus Jakarta Sans seperti di dokumen ini.
2. Foto pelanggan: **sudah berizin**. Berkas di `public/images/pelanggan/`, nama file menjadi keterangan slide.
3. Stack: **Tailwind CSS v4**, dimigrasi bertahap: (1) publik + Masuk/Daftar, (2) pelanggan, (3) admin.
   Token di `resources/css/app.css` (`@theme`), dipakai sebagai kelas `bg-brand-strong`, `text-muted`, `border-line`, dan seterusnya.
   Gaya lama dimuat di layer `legacy` sehingga kelas Tailwind selalu menang. Bagian lama dihapus per paket setelah halamannya pindah.

## 10. Riwayat

- v1.0: draf awal.
- v1.1: dial 3 / 3 / 3, gradasi diganti warna solid, Fase 4 (merapikan CSS) ditunda.
- v1.2: pindah ke Tailwind v4 bertahap (Fase 4 digabung ke migrasi), foto pelanggan berizin, tanpa pedoman merek resmi.
- v1.2 paket 2: halaman pelanggan (katalog, detail, pesan, pesanan saya, detail pesanan) pindah ke Tailwind; 169 kelas lama dihapus dari `rayarent.css` (118 KB jadi 68 KB). Badge status, plat nomor, dan daftar rekening masih gaya lama karena dipakai admin.
- v1.2 paket 3: `public/css/rayarent.css` dihapus. Sisa gaya dipecah ke `resources/css/rayarent/` (base, layout, rekening, admin), variabelnya menunjuk token `@theme`, semua halaman lewat `npm run build`. Kelas lama yang bentrok dengan Tailwind diganti: `container` jadi `rr-container`, `sticky` jadi `rr-sticky`.
- v1.2 paket 4: dasbor admin ditulis ulang dengan kelas Tailwind; grafik batang jadi komponen `rr-plot` di `app.css`; kelas `dsh-` yang tidak terpakai dihapus dari `admin.css`.
- v1.2: semua aksi penting (terima/tolak bayar, konfirmasi, serah terima, lunasi, pengembalian, batal, hapus, ubah status unit, simpan pengaturan) memakai kotak konfirmasi `x-confirm-dialog`. Aksi berbahaya: tombol merah dan fokus awal di "Batal".
- v1.2 paket 5: halaman Pesanan admin ditulis ulang dengan Tailwind (tabel jadi kartu di bawah 1024 px, menu ⋯, dialog cek bukti bayar dan catat kembali); kelas `ot-` yang tidak terpakai dihapus.
- v1.2 paket 6: halaman Kendaraan admin (daftar, tambah, ubah) ditulis ulang dengan Tailwind; kelas `fl-` dan sisa kelas lama yang tidak terpakai dihapus.
- v1.2 paket 7: Laporan dan Pengaturan admin ditulis ulang dengan Tailwind (tampilan cetak dijaga dengan varian `print:`). Semua halaman admin selesai; `admin.css` tinggal 6 KB (dari 45 KB) berisi gaya form masuk/daftar dan komponen kecil bersama.
- v1.3 landing final (arah A + B, arah C tidak dipakai):
  - A: header dan blok pembuka biru tua `#005B8C`, judul `clamp(2.4rem, 6vw, 4.6rem)`, kolase 4 foto statis di kanan (foto pelanggan berizin, dilengkapi foto armada; kolom kanan diturunkan) dengan kartu kecil logo + nama usaha yang mengambang, dan kartu kontak toska muda di bawah judul (tanpa slide); kotak "Cek unit kosong" (mulai sewa, layanan, jumlah orang) mengambang di batas bawah dan benar-benar mengecek jadwal semua unit; kartu "Paling sering disewa bulan ini" 1,4 : 1 : 1 dari data pesanan 30 hari (bila belum ada pesanan: "Mulai dari yang paling hemat").
  - B: judul "Harga sewa tertulis jelas" + papan harga. Revisi: papan gelap dan aksen kuning terlalu mencolok, diganti permukaan putih bergaris tipis dengan satu aksen brand (tab aktif biru tua, baris terlaris toska muda + label "terlaris"); angka Plus Jakarta tabular, bukan mono. Kolom durasi hanya yang ada di layanan itu. "Lihat semua" ⇄ "Tampilkan lebih sedikit"; saat terbuka tabel digulir di dalam kotak setinggi ±9 baris dengan judul kolom menempel. Cara sewa empat kolom bergaris.
  - Grid armada lama tidak dipakai lagi di landing. Ring fokus putih di permukaan biru. Hasil cek unit kosong: 6 kartu lalu tombol buka/tutup; bidang foto kartu putih (foto armada berlatar putih).
- v1.3 kolase: dua kolom flex bertingkat dengan tinggi foto berbeda; hover/fokus melebarkan foto (flex-grow) dan meredupkan foto lain; kartu logo dipindah ke ruang atas kolom kanan sehingga tidak menutupi keterangan foto; bingkai tipis bergeser sebagai elemen latar.
- v1.3 pembuka: kartu kontak dihapus, diganti tombol "Cek unit kosong" (putih) dan "Chat WhatsApp" (garis putih). Di belakang kolase ada lapisan latar flat statis: lingkaran biru brand `#0077B6`, dua cincin garis, kotak toska transparan, pola titik, dan garis rute putus-putus; semua bentuk kecil dan utuh di dalam area kolase, tanpa ikon. Chip angka dan pita layanan dihapus (terlihat seperti template). Tagline di kartu logo boleh dua baris.
- v1.3 beranda pelanggan (`/beranda`): tujuan setelah masuk. Urutan: sapaan + kalimat status, Perlu tindakan (batas bayar dengan hitung mundur, bukti ditolak, sisa tagihan, telat kembali), Sedang berjalan, Cara pesan (khusus pelanggan baru), Cek unit kosong (partial yang sama dengan landing), Pernah Anda sewa, ringkasan satu baris. Halaman Akun (`/akun`): data diri dan ganti password. Teks "langkah berikutnya" dari `Order::langkahBerikutnya()`, dipakai juga di Pesanan saya.
- v1.3 header: pita putih mengambang (radius 16px, garis tipis, bayangan lembut karena benar-benar melayang di atas isi) untuk semua pengguna; bagian luar header transparan. Di landing blok biru memanjang ke belakang header (`.rr-under-header`). Varian header biru dihapus.
- v1.3 cek unit kosong tanpa memuat ulang: form dan reset diambil dengan fetch, area `data-swap` (form + hasil) ditukar, URL diperbarui lewat history (tombol Kembali jalan), status dibacakan lewat live region, tombol jadi "Mengecek…" selama memuat. Tanpa JavaScript tetap berfungsi sebagai form biasa.
- v1.3 ulasan: bintang amber `#D97706` (3,2:1, ditambah teks nilai untuk pembaca layar), input bintang memakai radio asli (Tab + panah). Landing: "Kata pelanggan setelah sewa" hanya bila ada ulasan asli berkomentar; nama disingkat. Admin bisa menyembunyikan ulasan.
- v1.3 halaman publik: Tentang, Kontak, dan detail armada memakai pembuka biru tua yang sama dengan landing (`.rr-under-header`, breadcrumb versi gelap); detail armada menampilkan nilai rata-rata dan ulasan, serta `AggregateRating` di JSON-LD.
- v1.3 S&K: pembuka biru tua dengan tiga angka dari Pengaturan; garis progres baca di atas layar; garis daftar isi bergeser ke bab yang sedang dibaca (`aria-current`); bab muncul saat di-scroll; tombol Cetak / simpan PDF dan Tanya lewat WhatsApp di bawah daftar isi. Semua gerak mati bila reduced motion.
- v1.3 bersih-bersih (gaya ponytail): dihapus view `admin/vehicle`, salinan `resources/views/rayarent.css`, `resources/js/app.js` (kosong) beserta input Vite-nya, skrip `hapus-file-lama.ps1`; salinan kedua skrip scroll halus dan skrip filter armada di `landing-motion`; `Order::nextCode`, `Order::AKTIF` (pakai `VehicleUnit::AKTIF`), relasi `Payment::verifier` dan `Vehicle::reviews` yang tidak dipanggil; prop `level` di `section-head`; CSS `.nav`, `.card`, `.two-col`, `.rr-sticky`, `.page-sub`, `.spec*`, `.pkg`, `.media-*`, `[data-fleet]`.
