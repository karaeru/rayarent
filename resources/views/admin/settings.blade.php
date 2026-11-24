@extends('app')
@section('title', 'Pengaturan · Admin '.config('rayarent.nama'))
@section('content')
<div class="page-head"><div><h2>Pengaturan</h2><p class="page-sub">Aturan pembayaran dan denda.</p></div></div>
<form method="POST" action="{{ route('admin.pengaturan.simpan') }}" class="card" style="max-width:520px"><div class="card-body">
    @csrf @method('PUT')
    <div class="field">
        <label for="dp">Persentase DP (%)</label>
        <input id="dp" type="number" name="dp_percent" min="1" max="99" value="{{ old('dp_percent', $dpPercent) }}" required>
        @error('dp_percent') <p class="err">{{ $message }}</p> @enderror
    </div>
    <div class="field">
        <label for="late">Denda keterlambatan (Rp per jam)</label>
        <input id="late" type="number" name="late_fee_per_hour" min="0" step="1000" value="{{ old('late_fee_per_hour', $lateFee) }}" required>
        @error('late_fee_per_hour') <p class="err">{{ $message }}</p> @enderror
    </div>
    <div class="field">
        <label for="batas">Batas waktu unggah bukti bayar (jam)</label>
        <input id="batas" type="number" name="batas_bayar_jam" min="0" max="72" value="{{ old('batas_bayar_jam', $batasBayar) }}" required>
        <p class="hint">Pesanan tanpa bukti bayar melewati batas ini dibatalkan otomatis dan unitnya kosong lagi. Isi 0 untuk mematikan.</p>
        @error('batas_bayar_jam') <p class="err">{{ $message }}</p> @enderror
    </div>
    <p class="hint mb-2">Berlaku untuk pesanan dan pengembalian berikutnya. Pesanan yang sudah ada tidak berubah.</p>
    <button class="btn">Simpan pengaturan</button>
</div></form>
@endsection
