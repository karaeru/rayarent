@extends('app')
@section('title', config('rayarent.nama').' · '.config('rayarent.tagline'))
@push('head')
    <meta name="description" content="Sewa mobil dan minibus {{ config('rayarent.nama') }}: lepas kunci atau dengan driver, paket 12 dan 24 jam. Lihat harga dan cek unit kosong langsung di website.">
    <link rel="canonical" href="{{ route('home') }}">
@endpush
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $waLink = $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode('Halo '.config('rayarent.nama').', saya mau tanya sewa kendaraan.') : null;
    $sorotKeys = $sorot ? $sorot->group->pluck('id')->all() : [];
@endphp
@section('content')

{{-- ============ PEMBUKA · blok biru tua, judul besar, kartu kontak terang, kolase 4 foto (tanpa slide) ============ --}}
@php
@endphp
<section class="rr-bleed rr-on-dark rr-under-header grid items-center gap-x-12 gap-y-10 pb-28 text-white [--bleed:var(--color-brand-strong)] lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:pb-32">
    <div class="min-w-0" data-reveal>
        {{-- dua kalimat, dua bobot: pertanyaan tebal, jawaban lebih ringan --}}
        <h1 class="m-0 max-w-[14ch] text-[clamp(2.5rem,5.6vw,4.5rem)] leading-[1] tracking-[-0.045em] text-white">
            <span class="block font-extrabold">Mau ke mana hari ini?</span>
            <span class="mt-1 block font-medium text-white/90">Mobilnya sudah siap.</span>
        </h1>
        <p class="mb-0 mt-7 max-w-[42ch] text-[1.15rem] leading-[1.7] text-white/80">
            <b class="font-semibold text-white">{{ $totalUnit }} unit</b>, setir sendiri atau pakai driver, per <b class="font-semibold text-white">12 atau 24 jam</b>.
            Pilih tanggal dan lihat unit yang masih kosong sebelum membayar <b class="font-semibold text-white">DP {{ $dpPercent }}%</b>.
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#cek-unit" class="btn btn-lg bg-white text-brand-strong hover:bg-brand-soft hover:text-brand-strong">Cek unit kosong</a>
            @if ($waLink)
                <a href="{{ $waLink }}" rel="noopener" class="btn btn-lg border-white/60 bg-transparent text-white hover:bg-white/10 hover:text-white">Chat WhatsApp</a>
            @endif
        </div>
    </div>

    {{-- kolase 4 foto: dua kolom bertingkat. Kursor di satu foto: foto itu membesar, foto lain mengecil dan sedikit redup --}}
    <div class="relative min-w-0 py-6 lg:pl-6" data-reveal>
        {{--
            Lapisan latar di belakang kolase (gaya flat: bentuk sederhana, warna solid dari palet brand, tanpa gradasi/bayangan, statis).
            Garis putus-putus = rute perjalanan. Semua bentuk berada utuh di dalam wadah (tidak terpotong).
        --}}
        <div class="pointer-events-none absolute -inset-3 overflow-hidden sm:-inset-5 xl:-inset-10" aria-hidden="true">
            {{-- lingkaran besar biru brand di kanan atas --}}
            <span class="absolute right-2 top-2 size-44 rounded-full bg-brand sm:size-56"></span>
            {{-- cincin konsentris di kiri bawah --}}
            <span class="absolute bottom-2 left-2 size-40 rounded-full border border-white/15"></span>
            <span class="absolute bottom-10 left-10 size-24 rounded-full border border-white/15"></span>
            {{-- kotak toska muda tipis di kanan bawah --}}
            <span class="absolute bottom-8 right-8 size-16 rotate-12 rounded-2xl bg-brand-soft/15"></span>
            {{-- pola titik di kiri atas --}}
            <svg class="absolute left-2 top-2 h-28 w-40 text-white/25">
                <defs><pattern id="rr-dots" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1.6" fill="currentColor"/></pattern></defs>
                <rect width="100%" height="100%" fill="url(#rr-dots)"/>
            </svg>
            {{-- rute putus-putus dari kiri bawah ke kanan atas, dengan titik tujuan --}}
            <svg class="absolute inset-0 h-full w-full" viewBox="0 0 600 600" preserveAspectRatio="none" fill="none">
                <path d="M30 560 C 140 470, 90 330, 230 300 S 420 330, 470 180 S 540 70, 575 40" stroke="rgb(255 255 255 / .35)" stroke-width="2" stroke-dasharray="2 12" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
            </svg>
        </div>

        <div class="rr-collage relative">
            @php $tumbuh = [1.15, 0.85, 0.9, 1.1]; @endphp
            @foreach ([[0, 1], [2, 3]] as $c => $idx)
                <div class="rr-collage__col">
                    @if ($c === 1)
                        {{-- logo + nama usaha: teks boleh dua baris, tidak dipotong --}}
                        <div class="flex flex-none items-center gap-3 rounded-2xl bg-white px-3.5 py-3 text-ink">
                            <span class="grid size-10 flex-none place-items-center rounded-full bg-brand-soft">
                                @if ($logoUrl) <img src="{{ $logoUrl }}" alt="" width="26" height="26" class="size-[26px]"> @else <span class="font-extrabold text-brand-strong">R</span> @endif
                            </span>
                            <span class="min-w-0 leading-tight">
                                <b class="block">{{ config('rayarent.nama') }}</b>
                                <span class="block text-xs leading-snug text-muted">{{ config('rayarent.tagline') }}</span>
                            </span>
                        </div>
                    @endif

                    @foreach ($idx as $n)
                        @php $f = $kolase[$n] ?? null; @endphp
                        @if ($f)
                            <figure class="rr-collage__ph m-0 bg-white" style="--g: {{ $tumbuh[$n] }}">
                                @if ($f['url']) <a href="{{ $f['url'] }}" class="block h-full" aria-label="{{ $f['title'] }}"> @endif
                                <img src="{{ $f['src'] }}" alt="{{ $f['title'] }}" width="800" height="800" @if ($n === 0) fetchpriority="high" @else loading="lazy" @endif
                                     @class(['h-full w-full', 'object-cover' => $f['type'] === 'photo', 'object-contain p-3' => $f['type'] === 'car'])>
                                @if ($f['url']) </a> @endif
                                <figcaption class="pointer-events-none absolute bottom-2 left-2 max-w-[calc(100%-1rem)] truncate rounded-lg bg-white/95 px-2.5 py-1 text-xs font-semibold text-ink" title="{{ $f['title'] }}">{{ $f['title'] }}</figcaption>
                            </figure>
                        @endif
                    @endforeach

                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- kotak cek unit kosong: mengambang di batas bawah blok biru --}}
@include('partials.cek-unit-form', ['aksi' => route('home').'#armada', 'kelas' => 'relative z-10 -mt-16 shadow-xl'])

{{-- ============ PALING SERING DISEWA / HASIL CEK (arah A) ============ --}}
<section id="armada" class="scroll-mt-24 py-14" data-swap="cek-hasil">
    @if ($cek)
        @php $daftar = $cek['hasil']; @endphp
        <div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-reveal>
            <div>
                <h2 class="m-0 text-[clamp(1.5rem,2.6vw,2rem)] font-extrabold tracking-tight" data-arrive>
                    {{ $daftar->count() ? $daftar->count().' kendaraan punya unit kosong' : 'Belum ada unit kosong' }}
                </h2>
                <p class="mb-0 mt-1.5 text-ink-2">
                    Mulai {{ $cek['mulai']->translatedFormat('l, d M Y · H.i') }}{{ $cek['layanan'] ? ' · '.$services[$cek['layanan']] : '' }}{{ $cek['orang'] ? ' · minimal '.$cek['orang'].' kursi' : '' }}.
                    Dicek untuk paket terpendek tiap kendaraan.
                </p>
            </div>
            {{-- Reset memakai form GET, bukan link: klik link #… dicegat skrip scroll halus, sedangkan form selalu memuat ulang halaman tanpa ?mulai=… --}}
            <form method="GET" action="{{ route('home') }}#cek-unit" data-cek-ajax>
                <button class="inline-flex min-h-11 items-center border-0 bg-transparent px-0 font-semibold text-brand-strong underline underline-offset-2 hover:text-ink">Reset pencarian</button>
            </form>
        </div>
        @if ($daftar->isEmpty())
            <div class="rr-card px-5 py-10 text-center text-ink-2"><strong class="mb-1 block text-ink">Semua unit sudah terpesan di jam itu</strong>Coba jam lain, layanan lain, atau tanya kami lewat WhatsApp.</div>
        @endif
    @else
        @php $daftar = $populer->map(fn ($m) => (object) ['model' => $m, 'kosong' => null, 'pilihan' => null]); @endphp
        <div class="mb-6" data-reveal>
            <h2 class="m-0 text-[clamp(1.5rem,2.6vw,2rem)] font-extrabold tracking-tight" data-arrive>{{ $populerAsli ? 'Paling sering disewa bulan ini' : 'Mulai dari yang paling hemat' }}</h2>
        </div>
    @endif

    @include('partials.kartu-kendaraan', ['daftar' => $daftar, 'cek' => $cek])
    <p class="mb-0 mt-5 text-ink-2">Semua {{ $models->count() }} model ada di <a href="#harga" class="font-semibold text-brand-strong underline underline-offset-2">daftar harga</a> di bawah.</p>
</section>

{{-- ============ HARGA · arah B: judul besar + papan harga (permukaan terang, satu warna aksen) ============ --}}
@php
    // satu tab per layanan; di tiap tab, satu baris per kendaraan dengan paket yang ada saja
    $tabs = collect($services)->map(fn ($label, $key) => [
        'key' => $key,
        'label' => $label,
        'rows' => $vehicles
            ->map(fn ($v) => ['v' => $v, 'pkgs' => $v->packages->where('service', $key)->sortBy('duration_hours')])
            ->filter(fn ($r) => $r['pkgs']->isNotEmpty())
            ->sortBy(fn ($r) => $r['pkgs']->min('price'))
            ->values(),
        // kolom durasi hanya yang benar-benar ada di layanan ini (Dengan Driver tidak punya 12 jam)
        'jam' => $vehicles->flatMap(fn ($v) => $v->packages->where('service', $key)->pluck('duration_hours'))->unique()->sort()->values(),
    ])->filter(fn ($t) => $t['rows']->isNotEmpty())->values();
    $tampil = 6;
@endphp
<section id="harga" class="rr-bleed grid items-start gap-x-12 gap-y-8 py-16 [--bleed:#fff] lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)]">
    <div class="min-w-0" data-reveal>
        <h2 class="m-0 text-[clamp(2rem,4.4vw,3.6rem)] font-extrabold leading-[1.04] tracking-[-0.035em]" data-arrive>Harga sewa tertulis jelas, tanpa tanya-tanya dulu.</h2>
        <p class="mb-6 mt-5 max-w-[40ch] text-lg leading-relaxed text-ink-2">Semua paket 12 dan 24 jam tampil di sini, sama dengan yang ditagihkan. Pilih kendaraan, cek unit kosong, bayar DP {{ $dpPercent }}%.</p>
        <div class="flex flex-wrap gap-3">
            <a href="#cek-unit" class="btn btn-lg">Pesan sekarang</a>
            @if ($waLink) <a href="{{ $waLink }}" class="btn btn-lg btn-outline" rel="noopener">Tanya via WhatsApp</a> @endif
        </div>
        <dl class="m-0 mt-8 flex flex-wrap gap-x-8 gap-y-3 border-t border-line pt-5">
            <div><dt class="text-sm text-muted">Armada</dt><dd class="m-0 text-xl font-extrabold tabular-nums">{{ $totalUnit }} unit</dd></div>
            <div><dt class="text-sm text-muted">Model</dt><dd class="m-0 text-xl font-extrabold tabular-nums">{{ $models->count() }}</dd></div>
            <div><dt class="text-sm text-muted">Kapasitas</dt><dd class="m-0 text-xl font-extrabold tabular-nums">s.d. {{ $kursiTerbanyak }}</dd></div>
        </dl>
    </div>

    <div class="min-w-0 overflow-hidden rounded-2xl border border-line bg-white" data-pricetabs data-reveal>
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-canvas px-4 py-3 sm:px-5">
            <p class="m-0 font-bold">Daftar harga per paket</p>
            @if ($tabs->count() > 1)
                <div class="flex max-w-full gap-0.5 overflow-x-auto rounded-lg border border-line bg-white p-1" role="tablist" aria-label="Jenis layanan">
                    @foreach ($tabs as $i => $t)
                        <button type="button" role="tab" id="tab-{{ $t['key'] }}" aria-controls="pane-{{ $t['key'] }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-tab="{{ $t['key'] }}"
                                class="min-h-9 flex-none whitespace-nowrap rounded-md border-0 bg-transparent px-3 text-sm font-semibold text-ink-2 transition-colors hover:bg-canvas aria-selected:bg-brand-strong aria-selected:text-white">{{ $t['label'] }}</button>
                    @endforeach
                </div>
            @endif
        </div>

        @foreach ($tabs as $i => $t)
            <div role="tabpanel" id="pane-{{ $t['key'] }}" aria-labelledby="tab-{{ $t['key'] }}" data-pane="{{ $t['key'] }}" class="group" @if ($i) hidden @endif>
                <h3 class="m-0 px-5 pt-3 text-xs font-bold uppercase tracking-wider text-muted" data-pane-title>{{ $t['label'] }}</h3>
                {{-- saat "Lihat semua": tabel tetap setinggi ±9 baris dan digulir di dalam kotak, judul kolom menempel --}}
                <div class="px-4 group-[.is-open]:max-h-[29rem] group-[.is-open]:overflow-y-auto sm:px-5" data-scroll-box tabindex="-1">
                    <table class="w-full border-collapse">
                        <thead class="sticky top-0 z-[1] bg-white">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-muted [&>th]:border-b [&>th]:border-line [&>th]:px-1.5 [&>th]:py-2.5">
                                <th>Kendaraan</th><th>Kursi</th>
                                @foreach ($t['jam'] as $jam) <th class="text-right">{{ $jam }} jam</th> @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($t['rows'] as $n => $r)
                                @php $per = $r['pkgs']->keyBy('duration_hours'); $hi = $populerAsli && in_array($r['v']->id, $sorotKeys, true); @endphp
                                <tr @class(['transition-colors hover:bg-canvas [&>td]:border-b [&>td]:border-line/70 [&>td]:px-1.5 [&>td]:py-3', 'bg-brand-soft hover:bg-brand-soft' => $hi, 'is-extra' => $n >= $tampil])>
                                    <td class="text-[0.95rem]">
                                        <a href="{{ route('armada', $r['v']->slug) }}" class="font-semibold text-ink hover:text-brand-strong hover:underline">{{ $r['v']->name }}</a>
                                        @if ($hi) <span class="ml-1.5 whitespace-nowrap text-xs font-semibold text-brand-strong">terlaris</span> @endif
                                    </td>
                                    <td class="text-sm text-muted">{{ $r['v']->seat_label }}</td>
                                    @foreach ($t['jam'] as $jam)
                                        <td class="whitespace-nowrap text-right font-bold tabular-nums">{!! isset($per[$jam]) ? e($rp($per[$jam]->price)) : '<span class="font-normal text-muted" aria-label="tidak tersedia">–</span>' !!}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line px-5 py-2 text-sm text-muted">
                    <span><span class="group-[.is-open]:hidden">{{ min($tampil, $t['rows']->count()) }} dari </span>{{ $t['rows']->count() }} kendaraan · 3 hari = 3 × paket 24 jam</span>
                    @if ($t['rows']->count() > $tampil)
                        <button type="button" class="min-h-11 border-0 bg-transparent px-1 font-semibold text-brand-strong underline underline-offset-2" aria-expanded="false" data-more
                                data-label-open="Lihat semua {{ $t['rows']->count() }} kendaraan" data-label-close="Tampilkan lebih sedikit">Lihat semua {{ $t['rows']->count() }} kendaraan</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <p class="m-0 text-sm text-muted lg:col-start-2">Paket driver belum termasuk BBM, tol, parkir, dan makan driver kecuali tertulis "Driver + BBM".</p>
</section>

{{-- ============ CARA SEWA · arah B: empat langkah bergaris ============ --}}
<section id="cara-sewa" class="pb-14 pt-4">
    <h2 class="sr-only">Cara sewa</h2>
    <ol class="rr-steps m-0 grid list-none border-t border-line p-0 pt-7 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        @foreach ([
            ['Buat akun', 'Nama, email, dan nomor HP aktif.'],
            ['Pilih paket & tanggal', 'Hanya unit yang kosong yang bisa dipilih.'],
            ['Transfer DP '.$dpPercent.'%', 'Unggah bukti, admin mengonfirmasi.'],
            ['Ambil kendaraan', 'Lunasi sisanya saat serah terima.'],
        ] as $i => [$judul, $isi])
            <li class="min-w-0 py-3 pr-5 sm:[&:nth-child(even)]:border-l sm:[&:nth-child(even)]:border-line sm:[&:nth-child(even)]:pl-5 lg:[&+li]:border-l lg:[&+li]:border-line lg:[&+li]:pl-5">
                <span class="font-mono text-sm font-semibold text-brand-strong">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="m-0 mt-1.5 text-base font-bold">{{ $judul }}</h3>
                <p class="mb-0 mt-1 text-sm leading-relaxed text-muted">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ============ ULASAN PELANGGAN: hanya ulasan asli dari pesanan selesai ============ --}}
@if ($ulasan->isNotEmpty())
<section id="ulasan" class="pb-14 pt-2" aria-labelledby="judul-ulasan">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-reveal>
        <h2 id="judul-ulasan" class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>Kata pelanggan setelah sewa</h2>
        <p class="m-0 flex items-center gap-2 text-ink-2"><x-stars :value="$nilai['rata']" /> <b class="tabular-nums text-ink">{{ number_format($nilai['rata'], 1, ',', '') }}</b> dari {{ $nilai['jumlah'] }} ulasan</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($ulasan as $u)
            <figure class="rr-card m-0 flex flex-col gap-3 p-5" data-reveal>
                <x-stars :value="$u->rating" />
                <blockquote class="m-0 flex-1 leading-relaxed text-ink">{{ $u->comment }}</blockquote>
                <figcaption class="border-t border-line pt-3 text-sm text-muted"><b class="text-ink">{{ $u->public_name }}</b> · {{ $u->vehicle?->name }} · {{ $u->created_at->translatedFormat('M Y') }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

{{-- ============ KETENTUAN (pita toska muda, ringkas) ============ --}}
<section id="ketentuan" class="rr-bleed py-14 [--bleed:var(--color-brand-soft)]" data-reveal>
    <div class="mb-6 flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
        <h2 class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>Yang perlu diketahui</h2>
        <a href="{{ route('syarat') }}" class="font-semibold text-brand-strong underline underline-offset-2">Baca Syarat &amp; Ketentuan lengkap</a>
    </div>
    <dl class="m-0 grid gap-x-10 md:grid-cols-2">
        @foreach ([
            ['Uang muka', $dpPercent.'% dari total sewa. Anda juga bisa langsung bayar lunas.'],
            ['Keterlambatan', $rp($lateFee).' per jam, dihitung dibulatkan ke atas sejak jam selesai sewa.'],
            ['Kerusakan', 'Kondisi kendaraan dicek saat dikembalikan. Biaya kerusakan, jika ada, ditambahkan ke tagihan.'],
            ['Pembatalan', 'Bisa dibatalkan sendiri dari akun selama belum ada pembayaran. Jika sudah transfer, hubungi admin.'],
        ] as [$dt, $dd])
            <div class="grid gap-1 border-t border-brand-strong/20 py-3.5 sm:grid-cols-[140px_1fr] sm:gap-4">
                <dt class="font-bold text-ink">{{ $dt }}</dt>
                <dd class="m-0 text-ink-2">{{ $dd }}</dd>
            </div>
        @endforeach
    </dl>
</section>

@include('partials.landing-motion')
@include('partials.cek-unit-ajax')
@endsection
