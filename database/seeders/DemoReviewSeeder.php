<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * Ulasan CONTOH untuk demo tugas: diberikan ke pesanan demo yang sudah selesai.
 * Jalankan: php artisan db:seed --class=DemoReviewSeeder
 * Jangan dipakai untuk data asli (testimoni karangan). Hapus dengan: php artisan tinker --execute="App\Models\Review::truncate();"
 */
class DemoReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('Seeder ulasan demo tidak boleh dijalankan di production.');
            return;
        }

        $contoh = [
            [5, 'Mobil bersih dan wangi, serah terima di stasiun tepat waktu.'],
            [5, 'Proses pesan gampang, unit kosong langsung kelihatan jadi tidak perlu tanya-tanya.'],
            [4, 'Driver ramah dan hafal jalan ke Gunungkidul. AC agak kurang dingin di siang hari.'],
            [5, 'Sewa untuk acara kantor tiga hari, tagihan sesuai yang tertulis di website.'],
            [4, 'Pengembalian dicek cepat. Semoga ke depan bisa bayar QRIS juga.'],
            [5, null],
        ];

        $orders = Order::where('status', 'selesai')->whereDoesntHave('review')->with('unit')->orderBy('id')->get();
        foreach ($orders as $i => $order) {
            [$rating, $comment] = $contoh[$i % count($contoh)];
            Review::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'vehicle_id' => $order->unit->vehicle_id,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => ($order->returned_at ?? $order->end_at)->copy()->addHours(3),
            ]);
        }

        $this->command?->info($orders->count() . ' ulasan demo dibuat.');
    }
}
