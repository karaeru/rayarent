@extends('app')
@section('title', 'Pengaturan · Admin RAYARENT')
@section('content')
<h2 style="margin:0 0 12px">Pengaturan</h2>
<form method="POST" action="{{ route('admin.pengaturan.simpan') }}" class="card">
    @csrf @method('PUT')
    <label for="dp">Persentase DP (%)</label>
    <input id="dp" type="number" name="dp_percent" min="1" max="99" value="{{ old('dp_percent', $dpPercent) }}" required>
    @error('dp_percent') <p class="err">{{ $message }}</p> @enderror

    <label for="late">Denda keterlambatan (Rp per jam)</label>
    <input id="late" type="number" name="late_fee_per_hour" min="0" step="1000" value="{{ old('late_fee_per_hour', $lateFee) }}" required>
    @error('late_fee_per_hour') <p class="err">{{ $message }}</p> @enderror

    <p style="color:var(--mu);font-size:13px">Berlaku untuk pesanan dan pengembalian berikutnya. Pesanan yang sudah ada tidak berubah.</p>
    <p><button class="btn y">Simpan</button></p>
</form>
@endsection
