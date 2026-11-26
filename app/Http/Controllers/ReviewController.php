<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

/** Pelanggan memberi ulasan untuk pesanannya sendiri yang sudah selesai (sekali per pesanan). */
class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->status === 'selesai', 403, 'Ulasan bisa diberikan setelah sewa selesai.');
        if ($order->review()->exists()) {
            return back()->with('error', 'Pesanan ini sudah diulas.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'rating.required' => 'Pilih bintang 1 sampai 5.',
            'comment.max' => 'Komentar maksimal 500 karakter.',
        ]);

        Review::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'vehicle_id' => $order->unit->vehicle_id,
            'rating' => $data['rating'],
            'comment' => trim((string) ($data['comment'] ?? '')) ?: null,
        ]);

        return redirect()->route('pelanggan.pesanan.show', $order)->with('ok', 'Terima kasih, ulasan Anda tersimpan.');
    }
}
