@extends('app')
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $nama = config('rayarent.nama');
    $kota = config('rayarent.kota');
    $layanan = $vehicle->packages->pluck('service_label')->unique()->values();
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $waLink = $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode('Halo '.$nama.', saya mau tanya sewa '.$vehicle->name.'.') : null;
    $desc = 'Sewa '.$vehicle->name.($kota ? ' di '.$kota : '').' mulai '.$rp($vehicle->min_price).'. '
        .$vehicle->seat_label.' kursi'.($vehicle->transmission ? ', '.$vehicle->transmission : '').'. '
        .$layanan->implode(', ').'. Cek unit kosong dan pesan online.';
@endphp
@section('wa_text', 'Halo '.config('rayarent.nama').', saya mau tanya sewa '.$vehicle->name.'.')
@section('title', 'Sewa '.$vehicle->name.($kota ? ' '.$kota : '').' · '.$nama)
@push('head')
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ route('armada', $vehicle->slug) }}">
    <meta property="og:type" content="product">
    <meta property="og:title" content="Sewa {{ $vehicle->name }} · {{ $nama }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ route('armada', $vehicle->slug) }}">
    @if ($vehicle->photo_url) <meta property="og:image" content="{{ $vehicle->photo_url }}"> @endif
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => 'Sewa '.$vehicle->name,
        'description' => $desc,
        'image' => $vehicle->photo_url,
        'brand' => ['@type' => 'Organization', 'name' => $nama],
        'offers' => [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'IDR',
            'lowPrice' => $vehicle->packages->min('price'),
            'highPrice' => $vehicle->packages->max('price'),
            'offerCount' => $vehicle->packages->count(),
            'availability' => $unitAktif > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Beranda</a>
    <span aria-hidden="true">/</span>
    <a href="{{ route('home') }}#armada">Armada</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $vehicle->name }}</span>
</nav>

<div class="ar-top">
    <div class="ar-media" data-hero>
        @if ($vehicle->photo_url)
            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}" width="1600" height="1200">
        @else
            <span class="media-placeholder"><x-car-icon :type="$vehicle->icon" /></span>
        @endif
    </div>

    <div class="ar-info">
        <h1>{{ $vehicle->name }}</h1>
        <p class="lp-lead" style="margin-top:8px">
            {{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }} · {{ $layanan->implode(' · ') }}
        </p>

        <dl class="ar-specs">
            <div><dt>Kapasitas</dt><dd>{{ $vehicle->seat_label }} <small>kursi</small></dd></div>
            <div><dt>Transmisi</dt><dd>{{ $vehicle->transmission ?? 'Sesuai unit' }}</dd></div>
            <div><dt>Unit siap jalan</dt><dd>{{ $unitAktif }}</dd></div>
        </dl>

        <div class="ar-buy">
            <div class="price"><small>Mulai dari</small>{{ $rp($vehicle->min_price) }}</div>
            @auth
                @if (auth()->user()->isPelanggan())
                    <a class="btn btn-lg" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan sekarang</a>
                @endif
            @else
                <a class="btn btn-lg" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan sekarang</a>
            @endauth
            @if ($waLink) <a class="btn btn-lg btn-outline" href="{{ $waLink }}" rel="noopener">WhatsApp</a> @endif
        </div>
        @guest <p class="small muted" style="margin:8px 0 0">Anda akan diminta masuk atau daftar dulu, lalu langsung ke form pesanan.</p> @endguest
    </div>
</div>

<section class="lp-section">
    <div class="lp-head" data-reveal>
        <h2>Paket &amp; harga</h2>
        <p class="muted">Sewa lebih lama dihitung kelipatan paket. Contoh: 3 hari = 3 × paket 24 jam.</p>
    </div>
    <div class="ar-pkgs">
        @foreach ($vehicle->packages as $p)
            <div class="ar-pkg" data-reveal>
                <span class="ar-pkg__svc">{{ $p->service_label }}</span>
                <span class="ar-pkg__dur">{{ $p->duration_hours }} jam</span>
                <b class="ar-pkg__price">{{ $rp($p->price) }}</b>
                <span class="small muted">
                    @switch($p->service)
                        @case('lepas_kunci') Anda menyetir sendiri. @break
                        @case('driver') Termasuk driver, BBM ditanggung penyewa. @break
                        @case('driver_bbm') Termasuk driver dan BBM. @break
                    @endswitch
                </span>
            </div>
        @endforeach
    </div>
</section>

<section class="lp-section lp-terms" data-reveal>
    <div class="lp-head">
        <h2>Ketentuan singkat</h2>
        <a href="{{ route('syarat') }}">Syarat &amp; Ketentuan lengkap →</a>
    </div>
    <dl>
        <div><dt>Uang muka</dt><dd>{{ $dpPercent }}% dari total sewa, atau langsung lunas.</dd></div>
        <div><dt>Keterlambatan</dt><dd>{{ $rp($lateFee) }} per jam, dibulatkan ke atas.</dd></div>
        <div><dt>Ketersediaan</dt><dd>{{ $unitAktif }} unit. Unit kosong dicek otomatis saat Anda memilih tanggal.</dd></div>
        <div><dt>Pembatalan</dt><dd>Gratis selama belum ada pembayaran.</dd></div>
    </dl>
</section>

@if ($lainnya->isNotEmpty())
<section class="lp-section">
    <div class="lp-head" data-reveal><h2>Kendaraan serupa</h2></div>
    <div class="vgrid">
        @foreach ($lainnya as $v)
            <article class="vcard" data-reveal data-tilt>
                <a class="vcard__media" href="{{ route('armada', $v->slug) }}" tabindex="-1" aria-hidden="true">
                    @if ($v->photo_url)
                        <img src="{{ $v->photo_url }}" alt="" loading="lazy">
                    @else
                        <span class="media-placeholder"><x-car-icon :type="$v->icon" /></span>
                    @endif
                </a>
                <div class="vcard__body">
                    <h3><a class="vcard__title" href="{{ route('armada', $v->slug) }}">{{ $v->name }}</a></h3>
                    <div class="vcard__chips">
                        <span class="chip">{{ $v->seat_label }} kursi</span>
                        @if ($v->transmission) <span class="chip">{{ $v->transmission }}</span> @endif
                    </div>
                    <div class="vcard__foot">
                        <div class="price"><small>Mulai dari</small>{{ $rp($v->min_price) }}</div>
                        <a class="btn btn-sm btn-outline" href="{{ route('armada', $v->slug) }}">Detail</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- tombol pesan menempel di bawah layar HP --}}
<div class="ar-sticky" data-sticky-buy>
    <div class="price"><small>Mulai dari</small>{{ $rp($vehicle->min_price) }}</div>
    <a class="btn" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan</a>
</div>

@include('partials.landing-motion')
@endsection
