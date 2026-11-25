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
    /*
     * Rekening tujuan transfer (tampil di halaman pesanan, invoice, dan Syarat & Ketentuan).
     * DATA DUMMY untuk prototype. Ganti nomor dan nama pemilik dengan rekening asli sebelum dipakai.
     * 'warna' = warna label bank di tampilan.
     */
    'rekening' => [
        ['bank' => 'BCA', 'nomor' => '8730452196', 'nama' => 'Ragil Raya Trans', 'warna' => '#005eb8'],
        ['bank' => 'BNI', 'nomor' => '1298374650', 'nama' => 'Ragil Raya Trans', 'warna' => '#f37021'],
        ['bank' => 'Mandiri', 'nomor' => '1370019283746', 'nama' => 'Ragil Raya Trans', 'warna' => '#003d79'],
    ],
    // Lokasi di peta. Isi salah satu: koordinat "-7.5666,110.8166" (paling akurat) atau biarkan kosong agar memakai alamat.
    'maps' => env('RAYARENT_MAPS'),
    'instagram' => env('RAYARENT_INSTAGRAM'),     // contoh: rayarent.id (tanpa @)
    'tahun_berdiri' => env('RAYARENT_SEJAK'),     // contoh: 2019
    'jam_operasional' => env('RAYARENT_JAM'),     // contoh: Setiap hari, 07.00-21.00
    // Versi Syarat & Ketentuan. Ganti tanggalnya setiap kali isi resources/views/syarat.blade.php diubah,
    // supaya tercatat versi mana yang disetujui tiap pesanan.
    'sk_versi' => env('RAYARENT_SK_VERSI', '2026-11-25'),
];
