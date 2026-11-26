@extends('app')
@section('title', $vehicle->name.' · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unitAktif = $vehicle->units->where('status', '!=', 'perawatan')->count();
    $keterangan = ['lepas_kunci' => 'Anda menyetir sendiri.', 'driver' => 'Termasuk driver, BBM ditanggung penyewa.', 'driver_bbm' => 'Termasuk driver dan BBM.'];
@endphp
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('pelanggan.katalog') }}">Katalog</a><span aria-hidden="true">/</span><span aria-current="page">{{ $vehicle->name }}</span>
</nav>

<div class="ar-top">
    <div class="ar-media">
        @if ($vehicle->photo_url)
            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}" width="1600" height="1000" fetchpriority="high">
        @else
            <span class="media-placeholder"><x-car-icon :type="$vehicle->icon" /></span>
        @endif
    </div>

    <div class="ar-info">
        <h1>{{ $vehicle->name }}</h1>
        <p class="lp-lead" style="margin-top:8px">{{ \App\Models\Vehicle::CATEGORIES[$vehicle->category] }} · {{ $vehicle->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
        <dl class="ar-specs">
            <div><dt>Kapasitas</dt><dd>{{ $vehicle->seat_label }} <small>kursi</small></dd></div>
            <div><dt>Transmisi</dt><dd>{{ $vehicle->transmission ?? 'Sesuai unit' }}</dd></div>
            <div><dt>Unit siap jalan</dt><dd>{{ $unitAktif }}</dd></div>
        </dl>
        <div class="ar-buy">
            <div class="price"><small>Mulai dari</small>{{ $rp($vehicle->packages->min('price')) }}</div>
            <a class="btn btn-lg" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan sekarang</a>
        </div>
        <p class="small muted" style="margin:8px 0 0">Pilih paket, tanggal, dan unit di langkah berikutnya. Ketersediaan dicek otomatis.</p>
    </div>
</div>

<section class="lp-section" style="padding-top:40px">
    <div class="lp-head">
        <h2>Pilih paket</h2>
        <p class="muted">Sewa lebih lama dihitung kelipatan paket. Contoh: 3 hari = 3 × paket 24 jam.</p>
    </div>
    <div class="ar-pkgs">
        @foreach ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
            <a class="ar-pkg cu-pkg" href="{{ route('pelanggan.pesan', [$vehicle, 'paket' => $p->id]) }}">
                <span class="ar-pkg__svc">{{ $p->service_label }}</span>
                <span class="ar-pkg__dur">{{ $p->duration_hours }} jam per periode</span>
                <b class="ar-pkg__price">{{ $rp($p->price) }}</b>
                <span class="small muted">{{ $keterangan[$p->service] ?? '' }}</span>
                <span class="cu-pkg__go">Pilih paket ini</span>
            </a>
        @endforeach
    </div>
</section>
@endsection
