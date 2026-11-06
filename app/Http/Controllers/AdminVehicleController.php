<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RentalPackage;
use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminVehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('units', 'packages')->orderBy('name')->get();

        return view('admin.vehicles', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicle-form', [
            'vehicle' => new Vehicle(['is_active' => true]),
            'services' => RentalPackage::SERVICES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $vehicle = Vehicle::create($this->vehicleData($request));

        return redirect()->route('admin.kendaraan.ubah', $vehicle)
            ->with('ok', 'Kendaraan dibuat. Tambahkan paket harga dan unit di bawah.');
    }

    public function edit(Vehicle $vehicle)
    {
        $vehicle->load('packages')->load(['units' => fn ($q) => $q->withCount('orders')->orderBy('plate_number')]);

        return view('admin.vehicle-form', [
            'vehicle' => $vehicle,
            'services' => RentalPackage::SERVICES,
            'usedPackageIds' => Order::whereIn('rental_package_id', $vehicle->packages->pluck('id'))
                ->pluck('rental_package_id')->unique()->all(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->vehicleData($request, $vehicle));

        return back()->with('ok', 'Data kendaraan disimpan.');
    }

    /* ---------- paket harga ---------- */

    public function storePackage(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->validate([
            'service' => ['required', Rule::in(array_keys(RentalPackage::SERVICES))],
            'duration_hours' => ['required', 'in:12,24'],
            'price' => ['required', 'integer', 'min:10000', 'max:100000000'],
        ]);

        if ($vehicle->packages()->where('service', $data['service'])->where('duration_hours', $data['duration_hours'])->exists()) {
            return back()->with('error', 'Paket dengan layanan dan durasi itu sudah ada. Ubah harganya saja.');
        }

        $vehicle->packages()->create($data);

        return back()->with('ok', 'Paket ditambahkan.');
    }

    /** Harga baru hanya berlaku untuk pesanan berikutnya; pesanan lama menyimpan total sendiri. */
    public function updatePackage(Request $request, RentalPackage $package): RedirectResponse
    {
        $data = $request->validate(['price' => ['required', 'integer', 'min:10000', 'max:100000000']]);
        $package->update($data);

        return back()->with('ok', 'Harga paket diperbarui.');
    }

    public function destroyPackage(RentalPackage $package): RedirectResponse
    {
        if (Order::where('rental_package_id', $package->id)->exists()) {
            return back()->with('error', 'Paket ini sudah pernah dipesan dan tidak bisa dihapus. Ubah harganya atau biarkan.');
        }
        $package->delete();

        return back()->with('ok', 'Paket dihapus.');
    }

    /* ---------- unit ---------- */

    public function storeUnit(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->merge(['plate_number' => strtoupper(preg_replace('/\s+/', ' ', trim((string) $request->plate_number)))]);

        $data = $request->validate([
            'plate_number' => ['required', 'string', 'max:15', 'regex:/^[A-Z]{1,2} ?\d{1,4} ?[A-Z]{0,3}$/', 'unique:vehicle_units,plate_number'],
        ], [
            'plate_number.unique' => 'Plat nomor itu sudah terdaftar.',
            'plate_number.regex' => 'Format plat tidak valid. Contoh: AD 1234 RA',
        ]);

        $vehicle->units()->create(['plate_number' => $data['plate_number'], 'status' => 'tersedia']);

        return back()->with('ok', 'Unit ' . $data['plate_number'] . ' ditambahkan.');
    }

    /** Status "disewa" diatur otomatis oleh alur pesanan, admin hanya mengatur tersedia/perawatan. */
    public function updateUnitStatus(Request $request, VehicleUnit $unit): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:tersedia,perawatan']]);

        if ($unit->status === 'disewa') {
            return back()->with('error', 'Unit sedang disewa. Catat pengembaliannya dulu.');
        }
        if ($data['status'] === 'perawatan' && $this->hasUpcomingOrders($unit)) {
            return back()->with('error', 'Unit ini punya pesanan aktif atau yang akan datang. Selesaikan atau batalkan pesanannya dulu.');
        }

        $unit->update($data);

        return back()->with('ok', 'Status unit ' . $unit->plate_number . ' diperbarui.');
    }

    public function destroyUnit(VehicleUnit $unit): RedirectResponse
    {
        if ($unit->orders()->exists()) {
            return back()->with('error', 'Unit ini punya riwayat pesanan dan tidak bisa dihapus. Ubah status ke perawatan jika tidak dipakai lagi.');
        }
        $unit->delete();

        return back()->with('ok', 'Unit dihapus.');
    }

    private function hasUpcomingOrders(VehicleUnit $unit): bool
    {
        return $unit->orders()->whereIn('status', VehicleUnit::AKTIF)->where('end_at', '>', now())->exists();
    }

    private function vehicleData(Request $request, ?Vehicle $vehicle = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('vehicles', 'name')->ignore($vehicle?->id)],
            'seat_label' => ['required', 'regex:/^\d{1,2}(-\d{1,2})?$/'],
            'capacity' => ['required', 'integer', 'min:1', 'max:60'],
            'transmission' => ['nullable', 'in:Matic,Manual'],
        ], [
            'seat_label.regex' => 'Isi angka atau rentang, contoh: 7 atau 3-4.',
            'name.unique' => 'Nama kendaraan sudah ada.',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['transmission'] = $data['transmission'] ?: null;

        return $data;
    }
}
