<?php

namespace App\Http\Controllers;

use App\Models\RentalPackage;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleUnit;

/** Halaman depan publik: armada, daftar harga, cara sewa. Tanpa login. */
class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user) {
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'pelanggan.katalog');
        }

        $vehicles = Vehicle::where('is_active', true)
            ->with('packages')
            ->withCount(['units' => fn ($q) => $q->where('status', '!=', 'perawatan')])
            ->get()
            ->filter(fn ($v) => $v->packages->isNotEmpty())
            ->sortBy(fn ($v) => $v->min_price)
            ->values();

        // Panggung di pembuka: 1 logo · 2-5 foto pelanggan · 6 kontak.
        // Foto pelanggan dari public/images/pelanggan/ (urut nama file, maks. 4).
        // Bila folder itu kosong, diisi foto kendaraan supaya panggung tidak sepi.
        $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
        $fotoPelanggan = collect(glob(public_path('images/pelanggan/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}'), GLOB_BRACE) ?: [])
            ->sort()->take(4)->values()
            ->map(fn ($f) => ['type' => 'photo', 'label' => 'Foto pelanggan',
                'src' => asset('images/pelanggan/' . rawurlencode(basename($f))) . '?v=' . filemtime($f),
                'title' => $this->captionFromFile($f)]);
        if ($fotoPelanggan->isEmpty()) {
            $fotoPelanggan = $vehicles->filter(fn ($v) => $v->photo)
                ->unique(fn ($v) => Vehicle::baseName($v->name))
                ->sortByDesc('capacity')->take(4)->values()
                ->map(fn ($v) => ['type' => 'car', 'label' => $v->name, 'src' => $v->photo_url, 'title' => $v->name,
                    'sub' => $v->seat_label . ' kursi · mulai Rp' . number_format($v->min_price, 0, ',', '.'),
                    'url' => route('armada', $v->slug)]);
        }
        $showcase = collect([['type' => 'logo', 'label' => 'Logo', 'src' => $logo ? asset('images/logo.' . $logo) : null]])
            ->concat($fotoPelanggan)
            ->push(['type' => 'contact', 'label' => 'Kontak']);

        return view('home', [
            'showcase' => $showcase,
            'vehicles' => $vehicles,
            'services' => RentalPackage::SERVICES,
            'totalUnit' => $vehicles->sum('units_count'),
            'hargaTermurah' => (int) $vehicles->min(fn ($v) => $v->min_price),
            'kursiTerbanyak' => (int) $vehicles->max('capacity'),
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
        ]);
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
