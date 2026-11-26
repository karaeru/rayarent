@extends('app')
@section('title', $vehicle->name.' · '.config('rayarent.nama'))
@section('content')
<p class="mb-2"><a href="{{ route('pelanggan.katalog') }}">← Kembali ke katalog</a></p>

<div class="two-col">
    <div>
        <div class="media-hero">
            @if ($vehicle->photo_url)
                <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}" width="1600" height="1200">
            @else
                <span class="media-placeholder" ><x-car-icon :type="$vehicle->icon" /></span>
            @endif
        </div>

        <h1>{{ $vehicle->name }}</h1>
        <p class="page-sub">{{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }}</p>

        <div class="specs">
            <div class="spec"><div class="spec__val">{{ $vehicle->seat_label }}</div><div class="spec__lbl">Kapasitas</div></div>
            @if ($vehicle->transmission)
                <div class="spec"><div class="spec__val">{{ $vehicle->transmission }}</div><div class="spec__lbl">Transmisi</div></div>
            @endif
            <div class="spec"><div class="spec__val">{{ $vehicle->units->where('status', '!=', 'perawatan')->count() }}</div><div class="spec__lbl">Unit Aktif</div></div>
        </div>

        <h3 class="mt-3">Paket layanan</h3>
        @foreach ($vehicle->packages->groupBy('service') as $service => $pkgs)
            <div class="group-title">{{ $pkgs->first()->service_label }}</div>
            @foreach ($pkgs as $p)
                <div class="pkg">
                    <div>
                        <b>{{ $p->duration_hours }} jam / periode</b>
                        <div class="muted small">{{ $p->service_label }}</div>
                    </div>
                    <div class="text-right">
                        <div class="price">Rp{{ number_format($p->price, 0, ',', '.') }}</div>
                        <div class="muted small">per periode</div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    <aside class="card sticky"><div class="card-body">
        <div class="price"><small>Mulai dari</small>Rp{{ number_format($vehicle->packages->min('price'), 0, ',', '.') }}</div>
        <p class="muted small mt-1">Pilih paket, tanggal, dan unit di langkah berikutnya. Ketersediaan dicek otomatis.</p>
        <a class="btn btn-lg btn-block" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan sekarang</a>
    </div></aside>
</div>
@endsection
