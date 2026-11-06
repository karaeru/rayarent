@extends('app')
@section('title', $vehicle->name.' · RAYARENT')
@section('content')
<style>
    .hero{height:160px;border-radius:12px;background:linear-gradient(120deg,#1d4e89,#14181f);display:flex;align-items:center;justify-content:center;font-size:80px;margin-bottom:20px}
    .spec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin:16px 0}
    .spec{background:var(--sf);border:1px solid var(--ln);border-radius:10px;padding:12px;text-align:center}
    .spec .val{font-size:18px;font-weight:700}
    .spec .lbl{font-size:12px;color:var(--mu);margin-top:2px}
    .pkg-row{background:var(--sf);border:1px solid var(--ln);border-radius:10px;padding:14px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px}
    a.btn{text-decoration:none;display:inline-block}
</style>

<p><a href="{{ route('pelanggan.katalog') }}" style="color:var(--mu)">← Kembali ke katalog</a></p>

<div class="hero">{{ $vehicle->capacity >= 14 ? '🚐' : ($vehicle->capacity <= 4 ? '🚗' : '🚙') }}</div>

<h2 style="margin:0">{{ $vehicle->name }}</h2>
<p style="color:var(--mu);margin:4px 0 16px">{{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }}</p>

<div class="spec-grid">
    <div class="spec">
        <div class="val">{{ $vehicle->seat_label }}</div>
        <div class="lbl">Kapasitas</div>
    </div>
    @if ($vehicle->transmission)
    <div class="spec">
        <div class="val">{{ $vehicle->transmission }}</div>
        <div class="lbl">Transmisi</div>
    </div>
    @endif
    <div class="spec">
        <div class="val">{{ $vehicle->units->count() }}</div>
        <div class="lbl">Unit Tersedia</div>
    </div>
    <div class="spec">
        <div class="val">Rp{{ number_format($vehicle->packages->min('price'), 0, ',', '.') }}</div>
        <div class="lbl">Mulai dari</div>
    </div>
</div>

<h3 style="margin:20px 0 10px">Paket Layanan</h3>
@foreach ($vehicle->packages->groupBy('service') as $service => $pkgs)
<h4 style="margin:12px 0 6px;color:var(--mu);font-size:13px;text-transform:uppercase;letter-spacing:.5px">{{ $pkgs->first()->service_label }}</h4>
@foreach ($pkgs as $p)
<div class="pkg-row">
    <div>
        <b>{{ $p->duration_hours }} jam / periode</b>
        <div style="font-size:13px;color:var(--mu)">{{ $p->service_label }}</div>
    </div>
    <div style="text-align:right">
        <div style="font-size:18px;font-weight:700">Rp{{ number_format($p->price, 0, ',', '.') }}</div>
        <div style="font-size:12px;color:var(--mu)">per periode</div>
    </div>
</div>
@endforeach
@endforeach

<div style="margin-top:20px">
    <a class="btn y" href="{{ route('pelanggan.pesan', $vehicle) }}" style="padding:12px 24px;font-size:16px">
        Pesan Sekarang →
    </a>
</div>

@endsection
