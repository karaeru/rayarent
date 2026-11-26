@extends('app')
@section('title', 'Katalog · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $f = array_filter(request()->only(['search', 'service', 'capacity']), fn ($v) => $v !== null && $v !== '');
    $link = fn (array $ubah) => route('pelanggan.katalog', array_filter(array_merge($f, $ubah), fn ($v) => $v !== null && $v !== ''));
    $kapasitas = ['' => 'Semua', 4 => '4+ kursi', 7 => '7+ kursi', 14 => '14+ kursi'];
@endphp
@section('content')

<div class="dsh-head">
    <div>
        <h1>Pilih kendaraan</h1>
        <p class="page-sub">{{ $vehicles->count() }} kendaraan{{ $f ? ' sesuai pencarian' : ' siap disewa' }} · ketersediaan unit dicek saat Anda memilih tanggal</p>
    </div>
    <a class="btn btn-outline btn-sm" href="{{ route('pelanggan.pesanan') }}">Pesanan saya</a>
</div>

<div class="cu-toolbar">
    <nav class="ot-tabs" aria-label="Jenis layanan">
        <a href="{{ $link(['service' => null]) }}" @class(['is-active' => empty($f['service'])])>Semua layanan</a>
        @foreach ($services as $k => $label)
            <a href="{{ $link(['service' => $k]) }}" @class(['is-active' => ($f['service'] ?? '') === $k])>{{ $label }}</a>
        @endforeach
    </nav>
    <div class="cu-toolbar__right">
        <div class="rp-presets" role="group" aria-label="Kapasitas">
            @foreach ($kapasitas as $k => $label)
                <a href="{{ $link(['capacity' => $k ?: null]) }}" @class(['is-active' => (string) ($f['capacity'] ?? '') === (string) $k])>{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" class="ot-search" role="search">
            @foreach (['service', 'capacity'] as $k) @if (! empty($f[$k])) <input type="hidden" name="{{ $k }}" value="{{ $f[$k] }}"> @endif @endforeach
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input name="search" value="{{ $f['search'] ?? '' }}" placeholder="Cari nama kendaraan" aria-label="Cari nama kendaraan">
            @if (! empty($f['search'])) <a href="{{ $link(['search' => null]) }}" class="ot-search__clear" aria-label="Hapus pencarian">×</a> @endif
        </form>
    </div>
</div>

@if ($f)
    <p class="cu-active">
        Filter aktif:
        @if (! empty($f['service'])) <span>{{ $services[$f['service']] ?? $f['service'] }}</span> @endif
        @if (! empty($f['capacity'])) <span>min. {{ $f['capacity'] }} kursi</span> @endif
        @if (! empty($f['search'])) <span>"{{ $f['search'] }}"</span> @endif
        <a href="{{ route('pelanggan.katalog') }}">Hapus semua</a>
    </p>
@endif

@if ($vehicles->isEmpty())
    <div class="dsh-card empty"><strong>Tidak ada kendaraan yang cocok</strong>Ubah kata kunci atau filter Anda.</div>
@else
<div class="vgrid">
    @foreach ($vehicles as $v)
        <article class="vcard">
            <a class="vcard__media" href="{{ route('pelanggan.kendaraan', $v) }}" tabindex="-1" aria-hidden="true">
                @if ($v->photo_url)
                    <img src="{{ $v->photo_url }}" alt="" width="800" height="500" loading="lazy">
                @else
                    <span class="media-placeholder"><x-car-icon :type="$v->icon" /></span>
                @endif
                <span class="vcard__cat">{{ \App\Models\Vehicle::CATEGORIES[$v->category] }}</span>
            </a>
            <div class="vcard__body">
                <h3><a class="vcard__title" href="{{ route('pelanggan.kendaraan', $v) }}">{{ $v->name }}</a></h3>
                <div class="vcard__chips">
                    <span class="chip">{{ $v->seat_label }} kursi</span>
                    @if ($v->transmission) <span class="chip">{{ $v->transmission }}</span> @endif
                    <span class="chip">{{ $v->units_count }} unit</span>
                </div>
                <p class="muted small" style="margin:0">{{ $v->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
                <div class="vcard__foot">
                    <div class="price"><small>Mulai dari</small>{{ $rp($v->min_price) }}</div>
                    <div class="row" style="gap:6px">
                        <a class="btn btn-sm btn-outline" href="{{ route('pelanggan.kendaraan', $v) }}">Detail</a>
                        <a class="btn btn-sm" href="{{ route('pelanggan.pesan', $v) }}">Pesan</a>
                    </div>
                </div>
            </div>
        </article>
    @endforeach
</div>
@endif
@endsection
