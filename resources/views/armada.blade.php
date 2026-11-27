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
    $bisaPesan = ! auth()->check() || auth()->user()->isPelanggan();
    $catatan = ['lepas_kunci' => 'Anda menyetir sendiri.', 'driver' => 'Termasuk driver, BBM ditanggung penyewa.', 'driver_bbm' => 'Termasuk driver dan BBM.'];
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
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => 'Sewa '.$vehicle->name,
        'description' => $desc,
        'image' => $vehicle->photo_url,
        'brand' => ['@type' => 'Organization', 'name' => $nama],
        'aggregateRating' => $nilai['jumlah'] ? ['@type' => 'AggregateRating', 'ratingValue' => $nilai['rata'], 'reviewCount' => $nilai['jumlah'], 'bestRating' => 5] : null,
        'offers' => [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'IDR',
            'lowPrice' => $vehicle->packages->min('price'),
            'highPrice' => $vehicle->packages->max('price'),
            'offerCount' => $vehicle->packages->count(),
            'availability' => $unitAktif > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ],
    ], fn ($v) => $v !== null), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
@section('content')

{{-- pembuka biru tua seperti landing: foto besar di kartu putih, info dan tombol di samping --}}
<section class="rr-bleed rr-on-dark rr-under-header text-white [--bleed:var(--color-brand-strong)] [--rr-tr:#2b45a8] [--rr-bl:#00a3c4] grid items-start gap-8 pb-14 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)] lg:gap-12 lg:pb-16">
    <div class="min-w-0 lg:col-span-2"><x-crumbs dark :items="[['Beranda', route('home')], ['Armada', route('home').'#armada'], [$vehicle->name]]" /></div>
    <div class="-mt-2 aspect-[16/10] min-w-0 overflow-hidden rounded-3xl bg-white" data-reveal>
        @if ($vehicle->photo_url)
            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}" width="1600" height="1000" fetchpriority="high" class="h-full w-full object-contain p-[5%]">
        @else
            <span class="flex h-full items-center justify-center text-brand-strong opacity-60 [&>svg]:w-1/2"><x-car-icon :type="$vehicle->icon" /></span>
        @endif
    </div>

    <div class="min-w-0" data-reveal>
        <h1 class="m-0 text-[clamp(2rem,4vw,3rem)] font-extrabold leading-tight tracking-[-0.03em] text-white">{{ $vehicle->name }}</h1>
        <p class="mb-0 mt-2 text-lg text-white/85">
            {{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }} · {{ $layanan->implode(' · ') }}
        </p>
        @if ($nilai['jumlah'])
            <a href="#ulasan" class="mt-3 inline-flex items-center gap-2 text-white hover:no-underline"><x-stars :value="$nilai['rata']" /> <b class="tabular-nums">{{ number_format($nilai['rata'], 1, ',', '') }}</b> <span class="text-white/80 underline decoration-white/40 underline-offset-2">{{ $nilai['jumlah'] }} ulasan</span></a>
        @endif

        <dl class="m-0 mt-6 grid grid-cols-3 gap-px overflow-hidden rounded-2xl bg-white/20">
            <div class="bg-brand-strong px-4 py-3"><dt class="text-xs font-semibold text-white/75">Kapasitas</dt><dd class="m-0 mt-0.5 text-lg font-bold">{{ $vehicle->seat_label }} <small class="font-medium text-white/75">kursi</small></dd></div>
            <div class="bg-brand-strong px-4 py-3"><dt class="text-xs font-semibold text-white/75">Transmisi</dt><dd class="m-0 mt-0.5 text-lg font-bold">{{ $vehicle->transmission ?? 'Sesuai unit' }}</dd></div>
            <div class="bg-brand-strong px-4 py-3"><dt class="text-xs font-semibold text-white/75">Unit siap jalan</dt><dd class="m-0 mt-0.5 text-lg font-bold tabular-nums">{{ $unitAktif }}</dd></div>
        </dl>

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-white/20 pt-6" data-buy>
            <p class="m-0 mr-auto leading-tight">
                <span class="block text-xs font-semibold text-white/75">Mulai dari</span>
                <span class="text-3xl font-extrabold tabular-nums">{{ $rp($vehicle->min_price) }}</span>
            </p>
            @if ($bisaPesan) <a class="btn btn-lg bg-white text-brand-strong hover:bg-brand-soft hover:text-brand-strong" href="{{ route('pelanggan.pesan', $vehicle) }}">Cek jadwal &amp; pesan</a> @endif
            @if ($waLink) <a class="btn btn-lg border-white/60 bg-transparent text-white hover:bg-white/10 hover:text-white" href="{{ $waLink }}" rel="noopener">WhatsApp</a> @endif
        </div>
        @guest <p class="mb-0 mt-2 text-sm text-white/75">Anda akan diminta masuk atau daftar dulu, lalu langsung ke form pesanan.</p> @endguest
    </div>
</section>

<section class="py-14">
    <x-section-head title="Paket & harga" sub="Sewa lebih lama dihitung kelipatan paket. Contoh: 3 hari = 3 × paket 24 jam." />
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($vehicle->packages as $p)
            <div class="flex flex-col gap-1 rounded-xl border border-line bg-white p-5" data-reveal>
                <span class="text-sm font-bold text-brand-strong">{{ $p->service_label }}</span>
                <span class="text-sm text-muted">{{ $p->duration_hours }} jam</span>
                <b class="my-1 text-2xl font-extrabold tabular-nums">{{ $rp($p->price) }}</b>
                <span class="text-sm text-ink-2">{{ $catatan[$p->service] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</section>

<section class="rr-bleed py-12 [--bleed:var(--color-brand-soft)]" data-reveal>
    <x-section-head title="Ketentuan singkat" :link="route('syarat')" link-text="Syarat & Ketentuan lengkap" />
    <dl class="m-0 grid gap-x-10 md:grid-cols-2">
        @foreach ([
            ['Uang muka', $dpPercent.'% dari total sewa, atau langsung lunas.'],
            ['Keterlambatan', $rp($lateFee).' per jam, dibulatkan ke atas.'],
            ['Ketersediaan', $unitAktif.' unit. Unit kosong dicek otomatis saat Anda memilih tanggal.'],
            ['Pembatalan', 'Gratis selama belum ada pembayaran.'],
        ] as [$dt, $dd])
            <div class="grid gap-1 border-t border-brand-strong/20 py-3.5 sm:grid-cols-[140px_1fr] sm:gap-4">
                <dt class="font-bold">{{ $dt }}</dt><dd class="m-0 text-ink-2">{{ $dd }}</dd>
            </div>
        @endforeach
    </dl>
</section>

@if ($ulasan->isNotEmpty())
<section id="ulasan" class="scroll-mt-24 py-14" aria-labelledby="judul-ulasan">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-reveal>
        <h2 id="judul-ulasan" class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>Ulasan penyewa</h2>
        <p class="m-0 flex items-center gap-2 text-ink-2"><x-stars :value="$nilai['rata']" /> <b class="tabular-nums text-ink">{{ number_format($nilai['rata'], 1, ',', '') }}</b> dari {{ $nilai['jumlah'] }} ulasan</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($ulasan as $u)
            <figure class="rr-card m-0 flex flex-col gap-3 p-5" data-reveal>
                <x-stars :value="$u->rating" />
                <blockquote class="m-0 flex-1 leading-relaxed text-ink">{{ $u->comment }}</blockquote>
                <figcaption class="border-t border-line pt-3 text-sm text-muted"><b class="text-ink">{{ $u->public_name }}</b> · {{ $u->created_at->translatedFormat('M Y') }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

@if ($lainnya->isNotEmpty())
<section class="py-14">
    <x-section-head title="Kendaraan serupa" />
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($lainnya as $v)
            <x-vehicle-card :href="route('armada', $v->slug)" :name="$v->name" :photo="$v->photo_url" :icon="$v->icon"
                :chips="array_filter([$v->seat_label.' kursi', $v->transmission])" :price="$v->min_price" />
        @endforeach
    </div>
</section>
@endif

@if ($bisaPesan)
{{-- tombol pesan menempel di bawah layar HP, muncul setelah tombol utama lewat --}}
<div class="fixed inset-x-0 bottom-0 z-40 flex items-center justify-between gap-3 border-t border-line bg-white px-4 py-3 md:hidden" data-sticky-buy>
    <p class="m-0 leading-tight"><span class="block text-xs text-muted">Mulai dari</span><span class="text-lg font-extrabold tabular-nums">{{ $rp($vehicle->min_price) }}</span></p>
    <a class="btn" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan</a>
</div>
@endif

@include('partials.landing-motion')
@endsection
