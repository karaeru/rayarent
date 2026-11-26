<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('unit.vehicle', 'package', 'payments')
            ->latest()->get();

        return view('index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        // pelanggan hanya boleh melihat pesanannya sendiri
        abort_unless($order->user_id === $request->user()->id, 404);

        // saat hitung mundur habis halaman dimuat ulang; batalkan yang kedaluwarsa supaya statusnya langsung benar
        rescue(fn () => Order::cancelExpired(), 0);
        $order->refresh()->load('unit.vehicle', 'package', 'payments', 'review');

        return view('show', compact('order'));
    }

    public function pay(Request $request, Order $order)
    {
        // pelanggan hanya boleh bayar pesanannya sendiri
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_if(in_array($order->status, ['dibatalkan', 'selesai'], true), 403, 'Pesanan ini tidak menerima pembayaran.');
        abort_if($order->due_amount <= 0, 403, 'Pesanan ini sudah lunas.');

        $order->load('payments');
        if ($order->hasPendingPayment()) {
            return back()->with('error', 'Masih ada bukti pembayaran yang menunggu verifikasi admin.');
        }

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:' . $order->due_amount],
            'proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'bank'   => ['nullable', 'in:' . collect(config('rayarent.rekening'))->pluck('bank')->implode(',')],
        ], [
            'amount.max' => 'Jumlah transfer tidak boleh melebihi sisa tagihan (Rp' . number_format($order->due_amount, 0, ',', '.') . ').',
        ]);

        // disk 'local' = privat (storage/app/private). Dibuka lewat route 'bukti', bukan URL publik.
        $path = $request->file('proof')->store('proofs', 'local');

        Payment::create([
            'order_id'   => $order->id,
            'amount'     => $data['amount'],
            'bank'       => $data['bank'] ?? null,
            'proof_path' => $path,
            'status'     => 'menunggu',
        ]);

        return redirect()->route('pelanggan.pesanan.show', $order)
            ->with('ok', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->status === 'menunggu_konfirmasi', 403, 'Pesanan tidak dapat dibatalkan.');

        $order->load('payments');
        if ($order->paid_amount > 0 || $order->hasPendingPayment()) {
            return back()->with('error', 'Pesanan sudah ada pembayaran. Hubungi admin untuk pembatalan dan pengembalian dana.');
        }

        $order->update(['status' => 'dibatalkan']);

        return redirect()->route('pelanggan.pesanan')
            ->with('ok', 'Pesanan ' . $order->code . ' berhasil dibatalkan.');
    }

    /** Bukti bayar hanya boleh dilihat pemilik pesanan dan admin. */
    public function proof(Request $request, Payment $payment)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $payment->order->user_id === $user->id, 404);
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path);
    }

    /** Invoice siap cetak / simpan PDF. Pemilik pesanan atau admin. */
    public function invoice(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $order->user_id === $user->id, 404);
        abort_if($order->status === 'dibatalkan', 404);

        $order->load('user', 'unit.vehicle', 'package', 'payments');

        return view('invoice', compact('order'));
    }
}
