<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\VehicleUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // pendapatan diterima per hari, 30 hari terakhir (hari tanpa pemasukan = 0)
        $perHari = Payment::where('status', 'diterima')
            ->where('verified_at', '>=', $today->copy()->subDays(29))
            ->selectRaw('DATE(verified_at) as hari, SUM(amount) as total')
            ->groupByRaw('DATE(verified_at)')
            ->pluck('total', 'hari');

        $grafik = collect(range(29, 0))->map(function ($mundur) use ($today, $perHari) {
            $hari = $today->copy()->subDays($mundur);

            return ['label' => $hari->translatedFormat('j M'), 'total' => (int) ($perHari[$hari->toDateString()] ?? 0)];
        })->all();

        $telat = Order::with('user', 'unit.vehicle')
            ->where('status', 'sedang_disewa')->whereNull('returned_at')->where('end_at', '<', now())
            ->orderBy('end_at')->get();

        $stats = [
            'total_pesanan'    => Order::count(),
            'menunggu'         => Order::where('status', 'menunggu_konfirmasi')->count(),
            'aktif'            => Order::where('status', 'sedang_disewa')->count(),
            'menunggu_bayar'   => Payment::where('status', 'menunggu')->count(),
            'telat'            => $telat->count(),
            'pendapatan_bulan' => (int) Payment::where('status', 'diterima')
                                      ->whereYear('verified_at', now()->year)
                                      ->whereMonth('verified_at', now()->month)
                                      ->sum('amount'),
        ];

        $statusPesanan = Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $statusUnit = VehicleUnit::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $terlaris = DB::table('orders')
            ->join('vehicle_units', 'orders.vehicle_unit_id', '=', 'vehicle_units.id')
            ->join('vehicles', 'vehicle_units.vehicle_id', '=', 'vehicles.id')
            ->where('orders.status', '!=', 'dibatalkan')
            ->selectRaw('vehicles.name, COUNT(*) as total')
            ->groupBy('vehicles.id', 'vehicles.name')
            ->orderByDesc('total')->limit(5)->get();

        $serahTerima = Order::with('user', 'unit.vehicle')
            ->where('status', 'dikonfirmasi')->whereBetween('start_at', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
            ->orderBy('start_at')->get();

        $pembayaranMenunggu = Payment::with('order.user', 'order.unit.vehicle')
            ->where('status', 'menunggu')->latest()->get();

        return view('admin.dashboard', compact(
            'stats', 'grafik', 'telat', 'statusPesanan', 'statusUnit', 'terlaris', 'serahTerima', 'pembayaranMenunggu'
        ));
    }

    public function orders(Request $request)
    {
        $orders = Order::with('user', 'unit.vehicle', 'package', 'payments')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            // kondisi pencarian dikelompokkan supaya filter status tetap berlaku
            ->when($request->filled('search'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // jumlah per status untuk tab di atas tabel (ikut kata kunci pencarian)
        $counts = Order::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            }))
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $perluTindakan = Payment::where('status', 'menunggu')->count();

        return view('admin.orders', compact('orders', 'counts', 'perluTindakan'));
    }

    public function confirm(Order $order): RedirectResponse
    {
        return $this->run(fn () => $order->confirm(), 'Pesanan ' . $order->code . ' dikonfirmasi.');
    }

    public function startRental(Order $order): RedirectResponse
    {
        return $this->run(fn () => $order->startRental(), 'Serah terima ' . $order->code . ' dicatat.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        return $this->run(fn () => $order->cancelByAdmin(), 'Pesanan ' . $order->code . ' dibatalkan.');
    }

    public function recordReturn(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'returned_at'      => ['required', 'date', 'before_or_equal:now'],
            'return_condition' => ['nullable', 'string', 'max:200'],
            'damage_fee'       => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ], [
            'returned_at.before_or_equal' => 'Waktu pengembalian tidak boleh di masa depan.',
        ]);

        return $this->run(
            fn () => $order->recordReturn(Carbon::parse($data['returned_at']), $data['return_condition'] ?? null, (int) ($data['damage_fee'] ?? 0)),
            'Pengembalian ' . $order->code . ' dicatat.'
        );
    }

    public function cashPayment(Request $request, Order $order): RedirectResponse
    {
        return $this->run(fn () => $order->recordCashPayment($request->user()->id), 'Pelunasan tunai ' . $order->code . ' dicatat.');
    }

    public function verifyPayment(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'in:diterima,ditolak']]);

        return $this->run(
            fn () => $payment->order->verifyPayment($payment->id, $data['action'] === 'diterima', $request->user()->id),
            $data['action'] === 'diterima' ? 'Pembayaran berhasil diverifikasi.' : 'Pembayaran ditolak.'
        );
    }

    /* ---------- laporan ---------- */

    public function report(Request $request)
    {
        $filters = $this->reportFilters($request);
        $base = $this->reportQuery($filters);

        $ringkasan = (clone $base)->where('status', '!=', 'dibatalkan')
            ->selectRaw('COUNT(*) as jumlah, COALESCE(SUM(total_price + late_fee + damage_fee), 0) as tagihan')->first();
        $diterima = (int) Payment::where('status', 'diterima')
            ->whereIn('order_id', (clone $base)->where('status', '!=', 'dibatalkan')->select('orders.id'))->sum('amount');

        $orders = (clone $base)->with('user', 'unit.vehicle', 'package', 'payments')
            ->latest('start_at')->paginate(25)->withQueryString();

        return view('admin.report', [
            'orders' => $orders,
            'filters' => $filters,
            'jumlah' => (int) $ringkasan->jumlah,
            'tagihan' => (int) $ringkasan->tagihan,
            'diterima' => $diterima,
        ]);
    }

    public function exportReport(Request $request)
    {
        $filters = $this->reportFilters($request);
        $query = $this->reportQuery($filters)->with('user', 'unit.vehicle', 'package', 'payments')->latest('start_at');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['Kode', 'Pelanggan', 'Telepon', 'Kendaraan', 'Plat', 'Paket', 'Mulai', 'Selesai', 'Status', 'Pembayaran', 'Total tagihan', 'Diterima', 'Sisa']);

            $query->chunk(500, function ($orders) use ($out) {
                foreach ($orders as $o) {
                    fputcsv($out, array_map([$this, 'csvCell'], [
                        $o->code, $o->user->name, $o->user->phone, $o->unit->vehicle->name, $o->unit->plate_number,
                        $o->package->service_label . ' ' . $o->package->duration_hours . ' jam x' . $o->quantity,
                        $o->start_at->format('Y-m-d H:i'), $o->end_at->format('Y-m-d H:i'),
                        $o->status, $o->payment_status, $o->grand_total, $o->paid_amount, $o->due_amount,
                    ]));
                }
            });
            fclose($out);
        }, 'laporan-rayarent-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportFilters(Request $request): array
    {
        return $request->validate([
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'status' => ['nullable', 'in:menunggu_konfirmasi,dikonfirmasi,sedang_disewa,selesai,dibatalkan'],
        ]);
    }

    /** Filter berdasarkan tanggal mulai sewa. */
    private function reportQuery(array $filters): Builder
    {
        return Order::query()
            ->when(! empty($filters['dari']), fn ($q) => $q->whereDate('start_at', '>=', $filters['dari']))
            ->when(! empty($filters['sampai']), fn ($q) => $q->whereDate('start_at', '<=', $filters['sampai']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']));
    }

    /** Nama pelanggan berasal dari input pengguna: netralkan awalan rumus (=, +, -, @) agar aman dibuka di Excel. */
    private function csvCell(mixed $value): mixed
    {
        return is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }

    /** Jalankan aksi; pelanggaran aturan ditampilkan sebagai pesan error, bukan halaman 500. */
    private function run(callable $action, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('ok', $success);
    }
}