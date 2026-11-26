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
    $kategori = \App\Models\Vehicle::CATEGORIES;
@endphp
@section('content')

{{-- ============ PEMBUKA (ENERGY 3: judul besar, satu titik fokus) ============ --}}
<section class="grid items-center gap-8 pb-14 pt-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)] lg:gap-12 lg:pt-6">
    <div class="order-2 lg:order-1" data-reveal>
        <h1 class="m-0 max-w-[14ch] text-4xl font-extrabold leading-[1.08] tracking-tight text-ink sm:text-5xl">Mau ke mana hari ini? Mobilnya sudah siap.</h1>
        <p class="mb-0 mt-5 max-w-[48ch] text-lg leading-relaxed text-ink-2">
            Sewa per 12 atau 24 jam, setir sendiri atau pakai driver.
            Pilih tanggal, lihat unit yang kosong, dan bayar DP {{ $dpPercent }}% saja di awal.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="#armada" class="btn btn-lg">Pilih kendaraan</a>
            @if ($waLink)
                <a href="{{ $waLink }}" class="btn btn-lg btn-outline" rel="noopener">Tanya via WhatsApp</a>
            @else
                <a href="#harga" class="btn btn-lg btn-outline">Lihat harga</a>
            @endif
        </div>
        <dl class="m-0 mt-9 flex flex-wrap gap-x-10 gap-y-3 border-t border-line pt-5">
            <div><dt class="text-sm text-muted">Armada</dt><dd class="m-0 text-xl font-extrabold tabular-nums">{{ $totalUnit }} unit</dd></div>
            <div><dt class="text-sm text-muted">Mulai dari</dt><dd class="m-0 text-xl font-extrabold tabular-nums">{{ $rp($hargaTermurah) }}</dd></div>
            <div><dt class="text-sm text-muted">Kapasitas</dt><dd class="m-0 text-xl font-extrabold tabular-nums">s.d. {{ $kursiTerbanyak }} kursi</dd></div>
        </dl>
    </div>

    {{-- slide: logo · foto pelanggan (izin tertulis) atau foto armada · kontak --}}
    <div class="order-1 flex flex-col gap-3.5 lg:order-2" data-stage aria-roledescription="carousel" aria-label="Tentang {{ config('rayarent.nama') }}">
        <div class="relative aspect-[16/11] overflow-hidden rounded-3xl bg-brand-soft lg:aspect-[5/4]">
            @foreach ($showcase as $i => $s)
                <div @class(['rr-slide flex flex-col', 'is-active' => $i === 0]) data-slide data-type="{{ $s['type'] }}"
                     role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} dari {{ $showcase->count() }}: {{ $s['label'] }}"
                     @if ($i) inert @endif>
                    @switch($s['type'])
                        @case('logo')
                            <div class="flex h-full flex-col items-center justify-center gap-5 text-center">
                                <div class="grid aspect-square w-[min(240px,52%)] place-items-center rounded-full bg-white" data-slide-media>
                                    @if ($s['src'])
                                        <img src="{{ $s['src'] }}" alt="" width="260" height="260" fetchpriority="high" class="h-auto w-[62%]">
                                    @else
                                        <span class="text-6xl font-extrabold text-brand-strong">R</span>
                                    @endif
                                </div>
                                <p class="m-0" data-slide-cap>
                                    <span class="block text-2xl font-extrabold tracking-wide text-ink">{{ config('rayarent.nama') }}</span>
                                    <span class="text-ink-2">{{ config('rayarent.tagline') }}</span>
                                </p>
                            </div>
                            @break

                        @case('photo')
                            <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}" width="1200" height="960" loading="lazy" data-slide-media
                                 class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-x-0 bottom-0 bg-ink/75 px-6 py-5 text-white" data-slide-cap>
                                <span class="mb-1.5 inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide">Pelanggan kami</span>
                                <p class="m-0 max-w-[26ch] text-xl font-bold leading-snug">{{ $s['title'] }}</p>
                            </div>
                            @break

                        @case('car')
                            <a href="{{ $s['url'] }}" tabindex="-1" class="flex h-[78%] items-center" data-slide-media>
                                <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}" width="1200" height="960" loading="lazy" class="h-full w-full object-contain p-[4%]">
                            </a>
                            <a href="{{ $s['url'] }}" class="ml-[6%] w-fit rounded-xl bg-white px-4 py-2.5 leading-snug text-ink hover:no-underline" data-slide-cap>
                                <span class="block font-bold">{{ $s['title'] }}</span>
                                <span class="text-sm text-muted">{{ $s['sub'] }}</span>
                            </a>
                            @break

                        @case('contact')
                            @php
                                // alamat pendek untuk slide: buang plus code Google dan ", Indonesia"
                                $alamatPendek = trim(preg_replace(['/^[2-9CFGHJMPQRVWX]{4,8}\+[2-9CFGHJMPQRVWX]{2,3},?\s*/i', '/,?\s*Indonesia$/i'], '', (string) config('rayarent.alamat')));
                                $ig = ltrim((string) config('rayarent.instagram'), '@');
                            @endphp
                            <div class="flex h-full flex-col justify-center bg-brand-strong px-6 py-6 text-white sm:px-9">
                                <span class="mb-2.5 w-fit rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide" data-slide-cap>Hubungi kami</span>
                                <h2 class="m-0 mb-4 max-w-[18ch] text-2xl font-bold leading-tight text-white" data-slide-cap>Siap jalan? Tanya dulu juga boleh.</h2>
                                <dl class="m-0 mb-5 grid grid-cols-2 gap-x-5 gap-y-3 border-y border-white/25 py-3.5" data-slide-cap>
                                    @if ($wa) <div class="min-w-0"><dt class="text-xs font-semibold uppercase tracking-wide text-white/80">WhatsApp</dt><dd class="m-0 truncate font-semibold"><a href="{{ $waLink }}" rel="noopener" class="text-white underline decoration-white/50 underline-offset-2">+{{ $wa }}</a></dd></div> @endif
                                    @if (config('rayarent.jam_operasional')) <div class="min-w-0"><dt class="text-xs font-semibold uppercase tracking-wide text-white/80">Jam buka</dt><dd class="m-0 truncate font-semibold">{{ config('rayarent.jam_operasional') }}</dd></div> @endif
                                    @if ($alamatPendek) <div class="col-span-2 hidden min-w-0 sm:block"><dt class="text-xs font-semibold uppercase tracking-wide text-white/80">Alamat</dt><dd class="m-0 line-clamp-2 font-semibold">{{ $alamatPendek }}</dd></div> @endif
                                    @if ($ig) <div class="hidden min-w-0 sm:block"><dt class="text-xs font-semibold uppercase tracking-wide text-white/80">Instagram</dt><dd class="m-0 truncate font-semibold"><a href="https://instagram.com/{{ $ig }}" rel="noopener" class="text-white underline decoration-white/50 underline-offset-2">{{ '@'.$ig }}</a></dd></div> @endif
                                    @if (config('rayarent.email')) <div class="hidden min-w-0 sm:block"><dt class="text-xs font-semibold uppercase tracking-wide text-white/80">Email</dt><dd class="m-0 truncate font-semibold"><a href="mailto:{{ config('rayarent.email') }}" class="text-white underline decoration-white/50 underline-offset-2">{{ config('rayarent.email') }}</a></dd></div> @endif
                                </dl>
                                <div class="flex flex-wrap gap-2.5" data-slide-cap>
                                    @if ($waLink) <a class="btn bg-white text-brand-strong hover:bg-brand-soft hover:text-brand-strong" href="{{ $waLink }}" rel="noopener">Chat WhatsApp</a> @endif
                                    <a class="btn border-white/60 bg-transparent text-white hover:bg-white/10 hover:text-white" href="{{ route('kontak') }}">Lokasi &amp; peta</a>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>
        <div class="flex justify-center gap-2" role="group" aria-label="Pilih slide">
            @foreach ($showcase as $i => $s)
                <button type="button" data-dot="{{ $i }}" aria-label="Slide {{ $i + 1 }}: {{ $s['label'] }}" @class(['rr-dot', 'is-active' => $i === 0]) @if ($i === 0) aria-current="true" @endif><span></span></button>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ ARMADA (latar halaman, kartu) ============ --}}
@php $awal = 8; @endphp
<section id="armada" class="py-14">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-reveal>
        <div>
            <h2 class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>Armada</h2>
            <p class="mb-0 mt-1.5 text-muted">{{ $models->count() }} model, {{ $totalUnit }} unit siap jalan. Diurutkan dari harga termurah.</p>
        </div>
    </div>

    @if ($models->isEmpty())
        <div class="rounded-xl border border-line bg-white px-5 py-10 text-center text-muted"><strong class="mb-1 block text-ink">Daftar armada sedang diperbarui</strong>Silakan hubungi kami untuk info ketersediaan.</div>
    @else
    <div data-fleet>
        @if ($categories->count() > 1)
            <div class="mb-5 flex flex-wrap gap-2" role="group" aria-label="Saring kategori" data-reveal>
                <button type="button" class="group is-active inline-flex items-center gap-2 rounded-full border border-line-strong bg-white px-4 py-2 text-sm font-semibold text-ink-2 transition hover:border-brand-strong aria-pressed:border-brand-strong aria-pressed:bg-brand-strong aria-pressed:text-white" aria-pressed="true" data-filter="semua">
                    Semua <span class="text-xs tabular-nums text-muted group-aria-pressed:text-white/85">{{ $models->count() }}</span>
                </button>
                @foreach ($categories as $cat)
                    <button type="button" class="group inline-flex items-center gap-2 rounded-full border border-line-strong bg-white px-4 py-2 text-sm font-semibold text-ink-2 transition hover:border-brand-strong aria-pressed:border-brand-strong aria-pressed:bg-brand-strong aria-pressed:text-white" aria-pressed="false" data-filter="{{ $cat['key'] }}">
                        {{ $cat['label'] }} <span class="text-xs tabular-nums text-muted group-aria-pressed:text-white/85">{{ $cat['count'] }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($models as $n => $m)
                <x-vehicle-card
                    :href="route('armada', $m->main->slug)" :name="$m->name" :photo="$m->photo" :icon="$m->icon"
                    :category="$kategori[$m->category]" :cat="$m->category" :extra="$n >= $awal"
                    :chips="array_filter([$m->seat.' kursi', $m->units.' unit', $m->variants->isEmpty() ? $m->main->transmission : null])"
                    :variants="$m->variants" :services="$m->services->implode(' · ')" :price="$m->min" />
            @endforeach
        </div>

        @if ($models->count() > $awal)
            <div class="mt-6 flex justify-center">
                <button type="button" class="btn btn-outline" data-fleet-more>Lihat semua {{ $models->count() }} model</button>
            </div>
        @endif
    </div>
    @endif
</section>

{{-- ============ HARGA (pita putih, padat) ============ --}}
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
    ])->filter(fn ($t) => $t['rows']->isNotEmpty())->values();
    $tampil = 8;
@endphp
<section id="harga" class="rr-bleed py-14 [--bleed:#fff]">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-reveal>
        <h2 class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>Daftar harga</h2>
        <p class="m-0 text-muted">Harga per paket. 3 hari = 3 × paket 24 jam.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-line bg-white" data-pricetabs data-reveal>
        @if ($tabs->count() > 1)
            <div class="flex gap-1 overflow-x-auto border-b border-line bg-canvas p-1.5" role="tablist" aria-label="Jenis layanan">
                @foreach ($tabs as $i => $t)
                    <button type="button" role="tab" id="tab-{{ $t['key'] }}" aria-controls="pane-{{ $t['key'] }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-tab="{{ $t['key'] }}"
                            class="group flex flex-none items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold text-ink-2 transition hover:bg-white aria-selected:bg-white aria-selected:text-brand-strong aria-selected:shadow-sm">
                        {{ $t['label'] }} <span class="text-xs tabular-nums text-muted group-aria-selected:text-brand-strong">{{ $t['rows']->count() }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        @foreach ($tabs as $i => $t)
            <div role="tabpanel" id="pane-{{ $t['key'] }}" aria-labelledby="tab-{{ $t['key'] }}" data-pane="{{ $t['key'] }}" @if ($i) hidden @endif>
                <h3 class="m-0 px-5 pt-4 text-xs font-bold uppercase tracking-wider text-muted" data-pane-title>{{ $t['label'] }}</h3>
                <ol class="m-0 list-none p-0 py-1">
                    @foreach ($t['rows'] as $n => $r)
                        <li @class(['grid grid-cols-[minmax(0,1fr)_auto] items-baseline gap-x-4 gap-y-0.5 px-4 py-2.5 transition-colors hover:bg-brand-soft sm:grid-cols-[minmax(0,1fr)_96px_auto] sm:px-5 [&+li]:border-t [&+li]:border-dashed [&+li]:border-line', 'is-extra' => $n >= $tampil])>
                            <a class="truncate font-semibold text-ink hover:text-brand-strong hover:no-underline" href="{{ route('armada', $r['v']->slug) }}">{{ $r['v']->name }}</a>
                            <span class="whitespace-nowrap text-sm text-muted">{{ $r['v']->seat_label }} kursi</span>
                            <span class="col-span-2 flex flex-wrap gap-x-5 font-bold tabular-nums sm:col-span-1 sm:justify-end">
                                @foreach ($r['pkgs'] as $p)
                                    <span class="sm:min-w-32 sm:text-right"><small class="mr-1 font-medium text-muted">{{ $p->duration_hours }} jam</small>{{ $rp($p->price) }}</span>
                                @endforeach
                            </span>
                        </li>
                    @endforeach
                </ol>
                @if ($t['rows']->count() > $tampil)
                    <button type="button" class="block w-full border-0 border-t border-line bg-transparent px-4 py-3 text-sm font-semibold text-brand-strong hover:bg-canvas" data-more>Lihat semua {{ $t['rows']->count() }} kendaraan</button>
                @endif
            </div>
        @endforeach
    </div>
    <p class="mb-0 mt-3 text-sm text-muted">Paket driver belum termasuk BBM, tol, parkir, dan makan driver kecuali tertulis "Driver + BBM".</p>
</section>

{{-- ============ CARA SEWA (lega, angka besar) ============ --}}
<section id="cara-sewa" class="py-16">
    <h2 class="m-0 mb-8 text-3xl font-extrabold tracking-tight" data-reveal data-arrive>Cara sewa</h2>
    <ol class="rr-steps m-0 grid list-none gap-0 p-0 pt-6 md:grid-cols-4" data-reveal>
        @foreach ([
            ['Buat akun', 'Cukup nama, email, dan nomor HP yang aktif. Kami menghubungi nomor ini untuk konfirmasi.'],
            ['Pilih paket & tanggal', 'Sistem menampilkan unit yang masih kosong di jam yang Anda pilih, jadi tidak ada pesanan dobel.'],
            ['Transfer DP '.$dpPercent.'%', 'Unggah foto bukti transfer di halaman pesanan. Admin mengecek lalu mengonfirmasi pesanan Anda.'],
            ['Ambil kendaraan', 'Lunasi sisa tagihan saat serah terima, tunai atau transfer. Kembalikan tepat waktu.'],
        ] as $i => [$judul, $isi])
            <li class="border-line py-4 md:pr-6 [&+li]:border-t md:[&+li]:border-l md:[&+li]:border-t-0 md:[&+li]:pl-6">
                <span class="block text-4xl font-extrabold tabular-nums text-brand">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="m-0 mt-3 text-lg font-bold">{{ $judul }}</h3>
                <p class="mb-0 mt-1.5 text-[0.95rem] leading-relaxed text-ink-2">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>

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

{{-- ============ PENUTUP (bidang brand solid, PB-8) ============ --}}
<section class="mt-16 flex flex-wrap items-center justify-between gap-6 rounded-2xl bg-brand-strong p-7 text-white sm:p-10" data-reveal>
    <div>
        <h2 class="m-0 text-3xl font-extrabold tracking-tight text-white">Sudah tahu mau sewa apa?</h2>
        <p class="mb-0 mt-2 text-white/90">Pesan sekarang dan cek unit yang kosong{{ config('rayarent.jam_operasional') ? '. '.config('rayarent.jam_operasional') : '' }}.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('register') }}" class="btn btn-lg bg-white text-brand-strong hover:bg-brand-soft hover:text-brand-strong">Daftar &amp; pesan</a>
        @if ($waLink) <a href="{{ $waLink }}" class="btn btn-lg border-white/60 bg-transparent text-white hover:bg-white/10 hover:text-white" rel="noopener">WhatsApp</a> @endif
    </div>
</section>
@include('partials.landing-motion')
@endsection
