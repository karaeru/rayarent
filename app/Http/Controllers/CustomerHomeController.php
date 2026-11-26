<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RentalPackage;
use App\Models\Setting;
use App\Support\Armada;
use Illuminate\Http\Request;

/**
 * Beranda pelanggan setelah masuk: apa yang perlu dilakukan sekarang, sewa yang sedang berjalan,
 * cek unit kosong, kendaraan yang pernah disewa, dan ringkasan akun. Semua dari data pelanggan itu sendiri.
 */
class CustomerHomeController extends Controller
{
    public function index(Request $request)
    {
        // pesanan yang lewat batas bayar dibatalkan dulu supaya statusnya langsung benar
        rescue(fn () => Order::cancelExpired(), 0, false);

        $orders = $request->user()->orders()
            ->with('unit.vehicle.packages', 'package', 'payments')
            ->latest()->get();

        $aktif = $orders->whereIn('status', Order::AKTIF)
            ->map(fn ($o) => tap($o, fn ($o) => $o->langkah = $o->langkahBerikutnya()));
        // perlu tindakan: bayar, unggah ulang, lunasi, atau kembalikan kendaraan yang lewat jadwal
        $tindakan = $aktif->filter(fn ($o) => in_array($o->langkah[0], ['warn', 'danger'], true))
            ->sortBy(fn ($o) => $o->pay_deadline ?? $o->end_at)->values();
        // sedang berjalan: menunggu verifikasi/konfirmasi, siap diambil, sedang disewa
        $berjalan = $aktif->reject(fn ($o) => in_array($o->langkah[0], ['warn', 'danger'], true))
            ->sortBy('start_at')->values();

        // sewa lagi: kendaraan berbeda dari riwayat (tanpa yang batal), hanya yang masih disewakan
        $pernah = $orders->where('status', '!=', 'dibatalkan')
            ->map(fn ($o) => $o->unit?->vehicle ? ['vehicle' => $o->unit->vehicle, 'terakhir' => $o->start_at] : null)
            ->filter()
            ->unique(fn ($x) => $x['vehicle']->id)
            ->filter(fn ($x) => $x['vehicle']->is_active && $x['vehicle']->packages->isNotEmpty())
            ->take(3)->values();

        $tidakBatal = $orders->where('status', '!=', 'dibatalkan');
        $ringkas = [
            'selesai' => $orders->where('status', 'selesai')->count(),
            'dibayar' => (int) $tidakBatal->sum(fn ($o) => $o->paid_amount),
            'sisa' => (int) $tidakBatal->sum(fn ($o) => $o->due_amount),
        ];

        // cek unit kosong (sama dengan landing)
        $vehicles = Armada::kendaraanAktif();
        $cek = Armada::cekUnitKosong($request, Armada::model($vehicles));

        return view('beranda', [
            'tindakan' => $tindakan,
            'berjalan' => $berjalan,
            'pernah' => $pernah,
            'ringkas' => $ringkas,
            'adaPesanan' => $orders->isNotEmpty(),
            'cek' => $cek,
            'services' => RentalPackage::SERVICES,
            'kursiTerbanyak' => (int) $vehicles->max('capacity'),
            'dpPercent' => (int) Setting::get('dp_percent', 30),
        ]);
    }
}
