<?php

namespace App\Support;

use App\Models\Order;
use App\Models\RentalPackage;
use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Data armada yang dipakai bersama oleh landing (HomeController) dan Beranda pelanggan:
 * kendaraan aktif, pengelompokan model (Matic & Manual jadi satu), dan cek unit kosong.
 */
class Armada
{
    /** Kendaraan aktif yang punya paket, urut dari harga termurah. Unit perawatan tidak dihitung. */
    public static function kendaraanAktif(): Collection
    {
        return Vehicle::where('is_active', true)
            ->with(['packages', 'units' => fn ($q) => $q->where('status', '!=', 'perawatan')->orderBy('plate_number')])
            ->withCount(['units' => fn ($q) => $q->where('status', '!=', 'perawatan')])
            ->get()
            ->filter(fn ($v) => $v->packages->isNotEmpty())
            ->sortBy(fn ($v) => $v->min_price)
            ->values();
    }

    /** Matic & Manual dari kendaraan yang sama digabung jadi satu model. */
    public static function model(Collection $vehicles): Collection
    {
        return $vehicles->groupBy(fn ($v) => Vehicle::baseName($v->name))
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
    }

    /**
     * Cek unit kosong: ?mulai=2026-11-27T08:00&layanan=lepas_kunci&orang=5
     * Hasil null bila belum dicari. Tiap kendaraan dicek untuk paket terpendek layanan itu (12 atau 24 jam).
     */
    public static function cekUnitKosong(Request $request, Collection $models): ?array
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
}
