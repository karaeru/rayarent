<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'dpPercent' => (int) Setting::get('dp_percent', 30),
            'lateFee' => (int) Setting::get('late_fee_per_hour', 50000),
            'batasBayar' => (int) Setting::get('batas_bayar_jam', 2),
        ]);
    }

    /** Berlaku untuk pesanan dan pengembalian berikutnya; pesanan yang sudah ada tidak berubah. */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'dp_percent' => ['required', 'integer', 'min:1', 'max:99'],
            'late_fee_per_hour' => ['required', 'integer', 'min:0', 'max:10000000'],
            'batas_bayar_jam' => ['sometimes', 'required', 'integer', 'min:0', 'max:72'],
        ]);

        Setting::put('dp_percent', $data['dp_percent']);
        Setting::put('late_fee_per_hour', $data['late_fee_per_hour']);
        if (array_key_exists('batas_bayar_jam', $data)) {
            Setting::put('batas_bayar_jam', $data['batas_bayar_jam']);
        }

        return back()->with('ok', 'Pengaturan disimpan.');
    }
}
