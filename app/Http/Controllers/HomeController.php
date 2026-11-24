<?php

namespace App\Http\Controllers;

use App\Models\RentalPackage;
use App\Models\Setting;
use App\Models\Vehicle;

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

        return view('home', [
            'vehicles' => $vehicles,
            'services' => RentalPackage::SERVICES,
            'totalUnit' => $vehicles->sum('units_count'),
            'hargaTermurah' => (int) $vehicles->min(fn ($v) => $v->min_price),
            'kursiTerbanyak' => (int) $vehicles->max('capacity'),
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
        ]);
    }
}
