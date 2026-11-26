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
