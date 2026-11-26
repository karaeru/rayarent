<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RentalPackage;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Halaman depan publik: pembuka + cek unit kosong, kendaraan populer, papan harga, cara sewa. Tanpa login. */
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'pelanggan.katalog');
        }

        $vehicles = Vehicle::where('is_active', true)
            ->with(['packages', 'units' => fn ($q) => $q->where('status', '!=', 'perawatan')->orderBy('plate_number')])
            ->withCount(['units' => fn ($q) => $q->where('status', '!=', 'perawatan')])
            ->get()
            ->filter(fn ($v) => $v->packages->isNotEmpty())
            ->sortBy(fn ($v) => $v->min_price)
            ->values();

        // Kolase pembuka: 4 foto. Foto pelanggan berizin dari public/images/pelanggan/ (urut nama file,
        // nama file jadi keterangan); bila kurang dari 4, sisanya diisi foto armada dari kendaraan terbesar.
        $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
        $kolase = collect(glob(public_path('images/pelanggan/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}'), GLOB_BRACE) ?: [])
            ->sort()->take(4)->values()
            ->map(fn ($f) => ['type' => 'photo', 'src' => asset('images/pelanggan/' . rawurlencode(basename($f))) . '?v=' . filemtime($f),
                'title' => $this->captionFromFile($f), 'url' => null]);
        if ($kolase->count() < 4) {
            $kolase = $kolase->concat($vehicles->filter(fn ($v) => $v->photo)
                ->unique(fn ($v) => Vehicle::baseName($v->name))
                ->sortByDesc('capacity')->take(4 - $kolase->count())->values()
                ->map(fn ($v) => ['type' => 'car', 'src' => $v->photo_url, 'title' => $v->name, 'url' => route('armada', $v->slug)]))->values();
        }

        // Model: Matic & Manual dari kendaraan yang sama digabung jadi satu kartu
        $models = $vehicles->groupBy(fn ($v) => Vehicle::baseName($v->name))
            ->map(function ($group) {
                $group = $group->sortBy(fn ($v) => [$v->min_price, $v->transmission]);
                $utama = $group->first();
                $nama = trim(preg_replace('/\s+/', ' ', preg_replace('/\b(matic|manual)\b/i', '', $utama->name)));

                return (object) [
                    'key' => Vehicle::baseName($utama->name),
                    'name' => $nama ?: $utama->name,
                    'main' => $utama,
                    'group' => $group->values(),
                    'photo' => $group->first(fn ($v) => $v->photo)?->photo_url,
                    'icon' => $utama->icon,
                    'seat' => $utama->seat_label,
                    'capacity' => (int) $group->max('capacity'),
                    'units' => $group->sum('units_count'),
                    'min' => $group->min(fn ($v) => $v->min_price),
                    'transmisi' => $group->pluck('transmission')->filter()->unique()->implode(' & '),
                ];
            })
            ->sortBy('min')->values();

        // "Paling sering disewa bulan ini": jumlah pesanan 30 hari terakhir per model (tanpa yang dibatalkan).
        // Bila belum ada pesanan, tampil 3 model termurah dengan judul yang jujur.
        $hitung = Order::query()
            ->join('vehicle_units', 'vehicle_units.id', '=', 'orders.vehicle_unit_id')
            ->where('orders.status', '!=', 'dibatalkan')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->selectRaw('vehicle_units.vehicle_id, count(*) as total')
            ->groupBy('vehicle_units.vehicle_id')
            ->pluck('total', 'vehicle_id');
        $laris = $models->map(fn ($m) => tap($m, fn ($m) => $m->pesanan = (int) $m->group->sum(fn ($v) => $hitung[$v->id] ?? 0)))
            ->filter(fn ($m) => $m->pesanan > 0)
            ->sortByDesc('pesanan')->values();
        $adaData = $laris->isNotEmpty();
        $populer = ($adaData ? $laris : $models)->take(3)->values();
        $sorot = $populer->first();

        // Kotak "Cek unit kosong": cari unit yang tidak bentrok jadwal untuk paket terpendek tiap kendaraan
        $cek = $this->cekUnitKosong($request, $models);

        return view('home', [
            'models' => $models,
            'kolase' => $kolase,
            'logoUrl' => $logo ? asset('images/logo.' . $logo) : null,
            'populer' => $populer,
            'populerAsli' => $adaData,
            'sorot' => $sorot,
            'cek' => $cek,
            'vehicles' => $vehicles,
            'services' => RentalPackage::SERVICES,
            'totalUnit' => $vehicles->sum('units_count'),
            'kursiTerbanyak' => (int) $vehicles->max('capacity'),
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
        ]);
    }

    /**
     * Cek unit kosong dari pembuka landing: ?mulai=2026-11-27T08:00&layanan=lepas_kunci&orang=5
     * Hasil null bila belum dicari. Tiap kendaraan dicek untuk paket terpendek layanan itu (12 atau 24 jam).
     */
    private function cekUnitKosong(Request $request, $models): ?array
    {
        if (! $request->filled('mulai')) {
            return null;
        }
        $data = $request->validate([
            'mulai' => ['required', 'date', 'after:now'],
            'layanan' => ['nullable', Rule::in(array_keys(RentalPackage::SERVICES))],
            'orang' => ['nullable', 'integer', 'min:1', 'max:60'],
        ], ['mulai.after' => 'Waktu mulai sewa harus setelah sekarang.']);

        rescue(fn () => Order::cancelExpired(), 0, false);
        $mulai = Carbon::parse($data['mulai']);
        $layanan = $data['layanan'] ?? null;
        $orang = (int) ($data['orang'] ?? 0);

        $hasil = $models->map(function ($m) use ($mulai, $layanan, $orang) {
            if ($orang && $m->capacity < $orang) {
                return null;
            }
            $cocok = $m->group->map(function ($v) use ($mulai, $layanan) {
                $pkg = $v->packages->when($layanan, fn ($c) => $c->where('service', $layanan))->sortBy('duration_hours')->first();
                if (! $pkg) {
                    return null;
                }
                $kosong = VehicleUnit::where('vehicle_id', $v->id)
                    ->availableBetween($mulai, $mulai->copy()->addHours($pkg->duration_hours))->count();

                return $kosong ? ['vehicle' => $v, 'paket' => $pkg, 'kosong' => $kosong] : null;
            })->filter()->sortBy(fn ($x) => $x['paket']->price)->values();

            return $cocok->isEmpty() ? null : (object) [
                'model' => $m,
                'kosong' => $cocok->sum('kosong'),
                'pilihan' => $cocok->first(),
            ];
        })->filter()->values();

        return ['mulai' => $mulai, 'layanan' => $layanan, 'orang' => $orang, 'hasil' => $hasil];
    }

    /** Halaman detail kendaraan publik: /armada/innova-zenix */
    public function vehicle(string $slug)
    {
        $vehicle = Vehicle::findActiveBySlug($slug);
        abort_unless($vehicle && $vehicle->packages()->exists(), 404);

        $vehicle->load(['packages' => fn ($q) => $q->orderBy('price')]);
        $unitAktif = $vehicle->units()->where('status', '!=', 'perawatan')->count();

        // kendaraan lain dengan kapasitas mirip, untuk pembanding
        $lainnya = Vehicle::where('is_active', true)->where('id', '!=', $vehicle->id)
            ->whereHas('packages')->with('packages')
            ->get()
            ->sortBy(fn ($v) => abs($v->capacity - $vehicle->capacity) * 10_000_000 + $v->min_price)
            ->take(3)->values();

        return view('armada', [
            'vehicle' => $vehicle,
            'unitAktif' => $unitAktif,
            'lainnya' => $lainnya,
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
        ]);
    }

    public function about()
    {
        $vehicles = Vehicle::where('is_active', true)->whereHas('packages')->with('packages')->withCount('units')->get();

        return view('tentang', [
            'jenis' => $vehicles->count(),
            'totalUnit' => $vehicles->sum('units_count'),
            'kursiMin' => (int) $vehicles->min('capacity'),
            'kursiMax' => (int) $vehicles->max('capacity'),
            'layanan' => $vehicles->flatMap->packages->pluck('service_label')->unique()->values(),
        ] + $this->lokasi());
    }

    public function terms()
    {
        return view('syarat', [
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
            'batasBayar' => (int) Setting::get('batas_bayar_jam', 2),
        ]);
    }

    public function contact()
    {
        return view('kontak', [
            'vehicles' => Vehicle::where('is_active', true)->whereHas('packages')->orderBy('name')->pluck('name'),
        ] + $this->lokasi());
    }

    /** Data peta: koordinat dari RAYARENT_MAPS bila ada, kalau tidak pakai alamat. */
    private function lokasi(): array
    {
        $q = config('rayarent.maps') ?: config('rayarent.alamat');

        return [
            'mapEmbed' => $q ? 'https://www.google.com/maps?q='.rawurlencode($q).'&z=16&output=embed' : null,
            'mapLink' => $q ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($q) : null,
            'mapRoute' => $q ? 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($q) : null,
        ];
    }

    /**
     * Keterangan foto dari nama file: "02-rombongan-ke-dieng.jpg" -> "Rombongan ke dieng".
     * Nama acak dari kamera/WhatsApp (IMG_1234, WhatsApp Image ...) diganti teks umum.
     */
    private function captionFromFile(string $file): string
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        if (preg_match('/^(img|dsc|pxl|whatsapp|screenshot|photo|foto)[\s_-]|\d{5,}|[0-9a-f]{8}-/i', $name)) {
            return 'Perjalanan bersama ' . config('rayarent.nama');
        }
        $text = trim(preg_replace('/\s+/', ' ', str_replace(['-', '_'], ' ', preg_replace('/^\d+[\s._-]*/', '', $name))));

        return $text !== '' ? mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1) : 'Perjalanan bersama ' . config('rayarent.nama');
    }
}
