@extends('app')
@section('title', 'Katalog · RAYARENT')
@section('content')
<style>
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;margin-top:16px}
    .kc{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:16px}
    .kc .car{height:80px;border-radius:8px;background:linear-gradient(120deg,#1d4e89,#14181f);display:flex;align-items:center;justify-content:center;font-size:44px;margin-bottom:10px}
    .mu{color:var(--mu);font-size:13px}
    .row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    select{padding:8px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit}
    a.btn{text-decoration:none;display:inline-block}
</style>
<h2>Katalog kendaraan</h2>
<form method="GET" class="row" style="margin-top:12px">
    <input name="search" value="{{ request('search') }}" placeholder="Cari nama kendaraan…"
           style="padding:8px 10px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit;flex:1;min-width:160px">
    <select name="service" onchange="this.form.submit()">
        <option value="">Semua layanan</option>
        @foreach ($services as $k => $label)
            <option value="{{ $k }}" @selected(request('service') === $k)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="capacity" onchange="this.form.submit()">
        <option value="">Semua kapasitas</option>
        @foreach ([4, 7, 14] as $c)
            <option value="{{ $c }}" @selected((int) request('capacity') === $c)>Min. {{ $c }} kursi</option>
        @endforeach
    </select>
    <button class="btn">Cari</button>
    @if (request()->hasAny(['search','service','capacity']))
        <a href="{{ route('pelanggan.katalog') }}" class="btn" style="background:var(--sf);color:var(--tx);border:1px solid var(--ln)">Reset</a>
    @endif
</form>

<div class="grid">
@forelse ($vehicles as $v)
    <div class="kc">
        <a href="{{ route('pelanggan.kendaraan', $v) }}" style="text-decoration:none;color:inherit">
            <div class="car">{{ $v->capacity >= 14 ? '🚐' : ($v->capacity <= 4 ? '🚗' : '🚙') }}</div>
            <h3 style="margin:0">{{ $v->name }}</h3>
            <p class="mu">{{ $v->seat_label }} kursi{{ $v->transmission ? ' · '.$v->transmission : '' }} · {{ $v->units_count }} unit</p>
            <p class="mu">{{ $v->packages->pluck('service_label')->unique()->implode(', ') }}</p>
        </a>
        <div class="row" style="justify-content:space-between;margin-top:8px">
            <b>mulai Rp{{ number_format($v->min_price, 0, ',', '.') }}</b>
            <a class="btn y" href="{{ route('pelanggan.pesan', $v) }}">Pesan</a>
        </div>
    </div>
@empty
    <p class="mu">Tidak ada kendaraan yang cocok. Ubah filter.</p>
@endforelse
</div>
@endsection
