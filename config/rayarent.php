<?php

/*
 * Identitas dan kontak yang tampil di bagian bawah halaman.
 * Isi lewat .env; yang dikosongkan tidak ditampilkan.
 * Logo: simpan sebagai public/images/logo.svg (atau .png / .webp), akan terpakai otomatis.
 */
return [
    'nama' => env('RAYARENT_NAMA', 'RAYARENT'),
    'kota' => env('RAYARENT_KOTA'),                // contoh: Solo (dipakai di judul halaman armada untuk SEO)
    'tagline' => env('RAYARENT_TAGLINE', 'Sewa kendaraan mudah dan terpercaya'),
    'whatsapp' => env('RAYARENT_WHATSAPP'),        // contoh: 6281234567890
    'email' => env('RAYARENT_EMAIL'),
    'alamat' => env('RAYARENT_ALAMAT'),
    'rekening' => env('RAYARENT_REKENING'),        // contoh: BCA 1234567890 a.n. RAYARENT (tampil di halaman bayar)
    // Lokasi di peta. Isi salah satu: koordinat "-7.5666,110.8166" (paling akurat) atau biarkan kosong agar memakai alamat.
    'maps' => env('RAYARENT_MAPS'),
    'instagram' => env('RAYARENT_INSTAGRAM'),     // contoh: rayarent.id (tanpa @)
    'tahun_berdiri' => env('RAYARENT_SEJAK'),     // contoh: 2019
    'jam_operasional' => env('RAYARENT_JAM'),
    // Versi Syarat & Ketentuan. Ganti tanggalnya setiap kali isi resources/views/syarat.blade.php diubah,
    // supaya tercatat versi mana yang disetujui tiap pesanan.
    'sk_versi' => env('RAYARENT_SK_VERSI', '2026-11-25'),      // contoh: Setiap hari, 07.00-21.00
];
