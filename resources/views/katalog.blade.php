@extends('app')
@section('title', 'Katalog · '.config('rayarent.nama'))
@section('content')
<div class="page-head">
    <div>
        <h2>Katalog kendaraan</h2>
        <p class="page-sub">{{ $vehicles->count() }} kendaraan{{ request()->hasAny(['search','service','capacity']) ? ' sesuai pencarian' : ' tersedia untuk disewa' }}</p>
    </div>
</div>

<form method="GET" class="card filters">
    <div class="grow"><input name="search" value="{{ request('search') }}" placeholder="Cari nama kendaraan…" aria-label="Cari nama kendaraan"></div>
    <select name="service" onchange="this.form.submit()" aria-label="Jenis layanan">
        <option value="">Semua layanan</option>
        @foreach ($services as $k => $label)
            <option value="{{ $k }}" @selected(request('service') === $k)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="capacity" onchange="this.form.submit()" aria-label="Kapasitas">
        <option value="">Semua kapasitas</option>
        @foreach ([4, 7, 14] as $c)
            <option value="{{ $c }}" @selected((int) request('capacity') === $c)>Min. {{ $c }} kursi</option>
        @endforeach
    </select>
    <button class="btn">Cari</button>
    @if (request()->hasAny(['search','service','capacity']))
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline">Reset</a>
    @endif
</form>

@if ($vehicles->isEmpty())
    <div class="card empty"><strong>Tidak ada kendaraan yang cocok</strong>Ubah kata kunci atau filter Anda.</div>
@else
<div class="vgrid">
    @foreach ($vehicles as $v)
        <article class="vcard">
            <a class="vcard__media" href="{{ route('pelanggan.kendaraan', $v) }}" tabindex="-1" aria-hidden="true">
                @if ($v->photo_url)
                    <img src="{{ $v->photo_url }}" alt="" loading="lazy">
                @else
                    <span class="media-placeholder">{{ $v->capacity >= 14 ? '🚐' : ($v->capacity <= 4 ? '🚗' : '🚙') }}</span>
                @endif
            </a>
            <div class="vcard__body">
                <a class="vcard__title" href="{{ route('pelanggan.kendaraan', $v) }}">{{ $v->name }}</a>
                <div class="vcard__chips">
                    <span class="chip">{{ $v->seat_label }} kursi</span>
                    @if ($v->transmission) <span class="chip">{{ $v->transmission }}</span> @endif
                    <span class="chip">{{ $v->units_count }} unit</span>
                </div>
                <p class="muted small" style="margin:0">{{ $v->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
                <div class="vcard__foot">
                    <div class="price"><small>Mulai dari</small>Rp{{ number_format($v->min_price, 0, ',', '.') }}</div>
                    <a class="btn btn-sm" href="{{ route('pelanggan.pesan', $v) }}">Pesan</a>
                </div>
            </div>
        </article>
    @endforeach
</div>
@endif
@endsection
