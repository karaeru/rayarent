<?php

namespace App\Http\Controllers;

use App\Models\RentalPackage;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->where('is_active', true)
            ->when($request->filled('service'), fn ($q) =>
                $q->whereHas('packages', fn ($p) => $p->where('service', $request->service)))
            ->when($request->filled('capacity'), fn ($q) =>
                $q->where('capacity', '>=', (int) $request->capacity))
            ->when($request->filled('search'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->search . '%'))
            ->with('packages')
            ->withCount('units')
            ->orderBy('name')
            ->get();

        return view('katalog', [
            'vehicles' => $vehicles,
            'services' => RentalPackage::SERVICES,
        ]);
    }

    public function show(Vehicle $vehicle)
    {
        abort_unless($vehicle->is_active, 404);

        $vehicle->load('packages', 'units');

        return view('vehicle', compact('vehicle'));
    }
}
