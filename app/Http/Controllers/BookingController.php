<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Vehicle $vehicle)
    {
        abort_unless($vehicle->is_active, 404);

        // dari kotak "Cek unit kosong" di landing: ?mulai=2026-11-27T08:00 mengisi jam mulai otomatis
        $mulai = rescue(fn () => request()->filled('mulai') ? Carbon::parse(request('mulai')) : null, null, false);

        return view('booking', [
            'vehicle' => $vehicle->load('packages'),
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'minStart' => now()->addHour()->format('Y-m-d\TH:i'),
            'mulaiAwal' => ($mulai && $mulai->isFuture() ? $mulai : now()->addDay()->setTime(8, 0))->format('Y-m-d\TH:i'),
        ]);
    }

    /** Dipanggil lewat fetch() dari form untuk menampilkan unit yang kosong. */
    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        [$pkg, $start, $end, $total] = $this->quote($request, $vehicle);
        // bebaskan unit dari pesanan yang tidak dibayar; bila gagal (mis. migration belum jalan) cek ketersediaan tetap lanjut
        rescue(fn () => Order::cancelExpired(), 0);

        $free = VehicleUnit::where('vehicle_id', $vehicle->id)
            ->availableBetween($start, $end)->pluck('id')->all();

        $units = $vehicle->units()->orderBy('plate_number')->get()->map(fn ($u) => [
            'id' => $u->id,
            'plate' => $u->plate_number,
            'ok' => in_array($u->id, $free),
            'why' => $u->status === 'perawatan' ? 'sedang perawatan' : 'sudah dipesan',
        ]);

        return response()->json([
            'end' => $end->translatedFormat('d M Y H:i'),
            'total' => $total,
            'dp' => (int) ceil($total * ((int) Setting::get('dp_percent', 30)) / 100),
            'units' => $units,
        ]);
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        [$pkg, $start, $end, $total] = $this->quote($request, $vehicle);

        $data = $request->validate([
            'vehicle_unit_id' => ['nullable', Rule::exists('vehicle_units', 'id')->where('vehicle_id', $vehicle->id)],
            'pickup_location' => ['nullable', 'string', 'max:200'],
            'pay_mode' => ['required', 'in:dp,lunas'],
            'setuju' => ['accepted'],
        ], [
            'setuju.accepted' => 'Centang persetujuan Syarat & Ketentuan sewa dulu.',
        ]);

        rescue(fn () => Order::cancelExpired(), 0);

        $order = DB::transaction(function () use ($vehicle, $pkg, $start, $end, $total, $data, $request) {
            // Kunci semua unit kendaraan ini supaya dua pelanggan tidak bisa
            // mengambil unit yang sama pada saat bersamaan.
            VehicleUnit::where('vehicle_id', $vehicle->id)->lockForUpdate()->get();

            $free = VehicleUnit::where('vehicle_id', $vehicle->id)->availableBetween($start, $end)->get();

            $unit = ! empty($data['vehicle_unit_id'])
                ? $free->firstWhere('id', (int) $data['vehicle_unit_id'])
                : $free->first();

            if (! $unit) {
                throw ValidationException::withMessages([
                    'vehicle_unit_id' => 'Unit tidak tersedia pada periode ini. Pilih unit atau tanggal lain.',
                ]);
            }

            $order = Order::create([
                'code' => 'TMP' . bin2hex(random_bytes(4)),
                'user_id' => $request->user()->id,
                'vehicle_unit_id' => $unit->id,
                'rental_package_id' => $pkg->id,
                'quantity' => (int) $request->quantity,
                'start_at' => $start,
                'end_at' => $end,
                'pickup_location' => $data['pickup_location'] ?? null,
                'total_price' => $total,
                'pay_mode' => $data['pay_mode'],
                'terms_accepted_at' => now(),
                'terms_version' => config('rayarent.sk_versi'),
                'status' => 'menunggu_konfirmasi',
            ]);
            $order->update(['code' => 'RR-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT)]);

            return $order;
        });

        return redirect()->route('pelanggan.pesanan.show', $order)
            ->with('ok', Order::paymentWindowHours()
                ? 'Pesanan dibuat. Unggah bukti transfer dalam ' . Order::paymentWindowHours() . ' jam, atau pesanan batal otomatis.'
                : 'Pesanan dibuat. Silakan transfer dan unggah bukti pembayaran di halaman ini.');
    }

    /** Validasi paket dan jadwal, lalu hitung waktu selesai dan total harga. */
    private function quote(Request $request, Vehicle $vehicle): array
    {
        $request->validate([
            'rental_package_id' => ['required', Rule::exists('rental_packages', 'id')->where('vehicle_id', $vehicle->id)],
            'start_at' => ['required', 'date', 'after:now'],
            'quantity' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'start_at.after' => 'Waktu mulai harus setelah sekarang.',
        ]);

        $pkg = $vehicle->packages()->findOrFail($request->rental_package_id);
        $start = Carbon::parse($request->start_at);
        $end = $start->copy()->addHours($pkg->duration_hours * (int) $request->quantity);

        return [$pkg, $start, $end, $pkg->price * (int) $request->quantity];
    }
}
