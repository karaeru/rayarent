@extends('app')
@section('title', 'Beranda · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $user = auth()->user();
    $namaDepan = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->first();
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $sub = match (true) {
        $tindakan->isNotEmpty() => $tindakan->count().' pesanan perlu tindakan Anda.',
        $berjalan->isNotEmpty() => $berjalan->count().' pesanan sedang berjalan.',
        $adaPesanan => 'Tidak ada pesanan yang sedang berjalan.',
        default => 'Belum ada pesanan. Cek unit kosong di bawah untuk mulai.',
    };
    $thumb = 'flex aspect-[14/9] w-20 flex-none items-center justify-center overflow-hidden rounded-lg border border-line bg-white text-brand [&>svg]:w-4/5 [&>svg]:opacity-50';
@endphp
@section('content')

<x-page-head :title="'Halo, '.$namaDepan" :sub="$sub">
    <a class="btn btn-outline btn-sm" href="{{ route('pelanggan.pesanan') }}">Pesanan saya</a>
    <a class="btn btn-sm" href="{{ route('pelanggan.katalog') }}">Lihat katalog</a>
</x-page-head>

{{-- ============ PERLU TINDAKAN ============ --}}
@if ($tindakan->isNotEmpty())
<section class="mb-8" aria-labelledby="judul-tindakan">
    <h2 id="judul-tindakan" class="m-0 mb-3 text-lg font-bold">Perlu tindakan</h2>
    <div class="flex flex-col gap-3">
        @foreach ($tindakan as $o)
            @php [$nada, $teks] = $o->langkah; $telat = $nada === 'danger'; @endphp
            <article @class(['rr-card flex flex-wrap items-center gap-x-4 gap-y-3 p-4 sm:flex-nowrap', 'shadow-[inset_4px_0_0_var(--color-warn)]' => ! $telat, 'shadow-[inset_4px_0_0_var(--color-bad)]' => $telat])>
                <span class="{{ $thumb }}">
                    @if ($o->unit->vehicle->photo_url) <img src="{{ $o->unit->vehicle->photo_url }}" alt="" width="168" height="108" class="h-full w-full object-contain p-1"> @else <x-car-icon :type="$o->unit->vehicle->icon" /> @endif
                </span>
                <div class="min-w-0 flex-1">
                    <p class="m-0 flex flex-wrap items-center gap-2"><b>{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span> <span class="text-sm text-muted">{{ $o->code }}</span></p>
                    <p @class(['m-0 mt-1 text-sm font-semibold', 'text-warn' => ! $telat, 'text-bad' => $telat])>{{ $teks }}</p>
                    @if ($o->pay_deadline)
                        <p class="m-0 mt-0.5 text-sm text-muted">Sisa waktu <b class="tabular-nums text-ink" data-countdown="{{ $o->pay_deadline->toIso8601String() }}">{{ gmdate('H:i:s', max(0, (int) now()->diffInSeconds($o->pay_deadline, false))) }}</b>, lewat dari itu pesanan batal otomatis.</p>
                    @elseif ($o->due_amount > 0 && ! $telat)
                        <p class="m-0 mt-0.5 text-sm text-muted">Total {{ $rp($o->grand_total) }} · sudah dibayar {{ $rp($o->paid_amount) }}</p>
                    @endif
                </div>
                <div class="flex w-full flex-none flex-wrap gap-2 sm:w-auto">
                    @if ($telat)
                        @if ($wa) <a class="btn btn-sm bg-wa text-white hover:bg-[#0b6535] hover:text-white" target="_blank" rel="noopener" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo '.config('rayarent.nama').', saya pelanggan pesanan '.$o->code.'. Kendaraan sedang dalam perjalanan kembali.') }}">Kabari admin</a> @endif
                        <a class="btn btn-outline btn-sm" href="{{ route('pelanggan.pesanan.show', $o) }}">Lihat pesanan</a>
                    @else
                        <a class="btn btn-sm" href="{{ route('pelanggan.pesanan.show', $o) }}#form-bayar">{{ $o->paid_amount > 0 ? 'Lunasi sekarang' : 'Bayar sekarang' }}</a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- ============ SEDANG BERJALAN ============ --}}
@if ($berjalan->isNotEmpty())
<section class="mb-8" aria-labelledby="judul-berjalan">
    <h2 id="judul-berjalan" class="m-0 mb-3 text-lg font-bold">Sedang berjalan</h2>
    <div class="grid gap-3 md:grid-cols-2">
        @foreach ($berjalan as $o)
            @php [$nada, $teks] = $o->langkah; @endphp
            <a href="{{ route('pelanggan.pesanan.show', $o) }}" class="rr-card group flex items-center gap-4 p-4 text-ink transition-colors hover:border-line-strong hover:no-underline">
                <span class="{{ $thumb }}">
                    @if ($o->unit->vehicle->photo_url) <img src="{{ $o->unit->vehicle->photo_url }}" alt="" width="168" height="108" loading="lazy" class="h-full w-full object-contain p-1"> @else <x-car-icon :type="$o->unit->vehicle->icon" /> @endif
                </span>
                <span class="min-w-0 flex-1">
                    <span class="flex flex-wrap items-center gap-2"><b>{{ $o->unit->vehicle->name }}</b> <x-order-status :status="$o->status" /></span>
                    <span @class(['mt-1 block text-sm font-semibold', 'text-ok' => $nada === 'ok', 'text-brand-strong' => $nada === 'info'])>{{ $teks }}</span>
                    <span class="mt-0.5 block text-sm tabular-nums text-muted">{{ $o->start_at->translatedFormat('d M H:i') }} → {{ $o->end_at->translatedFormat('d M H:i') }}@if ($o->status === 'dikonfirmasi' && $o->pickup_location) · {{ $o->pickup_location }}@endif</span>
                </span>
                <span class="hidden text-xl text-muted transition-transform group-hover:translate-x-0.5 sm:block" aria-hidden="true">›</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ============ PELANGGAN BARU: cara pesan ============ --}}
@unless ($adaPesanan)
<section class="mb-8" aria-labelledby="judul-cara">
    <h2 id="judul-cara" class="m-0 mb-3 text-lg font-bold">Cara pesan</h2>
    <ol class="rr-card m-0 grid list-none divide-y divide-line p-0 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
        @foreach ([
            ['Pilih tanggal & kendaraan', 'Cek unit kosong di bawah, lalu klik "Pesan untuk jam ini".'],
            ['Transfer DP '.$dpPercent.'%', 'Unggah bukti transfer di halaman pesanan. Admin mengecek dan mengonfirmasi.'],
            ['Ambil kendaraan', 'Lunasi sisa tagihan saat serah terima. Bawa KTP dan SIM asli.'],
        ] as $i => [$judul, $isi])
            <li class="p-4">
                <span class="text-sm font-bold tabular-nums text-brand-strong">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="m-0 mt-1 text-base font-bold">{{ $judul }}</h3>
                <p class="mb-0 mt-1 text-sm leading-relaxed text-ink-2">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>
@endunless

{{-- ============ CEK UNIT KOSONG ============ --}}
<section class="mb-8" aria-labelledby="judul-cek">
    <h2 id="judul-cek" class="m-0 mb-3 text-lg font-bold">{{ $adaPesanan ? 'Sewa lagi? Cek unit kosong' : 'Cek unit kosong' }}</h2>
    @include('partials.cek-unit-form', ['aksi' => route('pelanggan.beranda').'#hasil-cek'])

    {{-- area hasil selalu ada supaya bisa ditukar tanpa memuat ulang halaman --}}
    <div data-swap="cek-hasil" class="scroll-mt-24">
        @if ($cek)
            @php $daftar = $cek['hasil']; @endphp
            <div id="hasil-cek" class="scroll-mt-24 pt-6">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
                    <div>
                        <h3 class="m-0 text-xl font-extrabold tracking-tight">{{ $daftar->count() ? $daftar->count().' kendaraan punya unit kosong' : 'Belum ada unit kosong' }}</h3>
                        <p class="mb-0 mt-1 text-sm text-ink-2">
                            Mulai {{ $cek['mulai']->translatedFormat('l, d M Y · H.i') }}{{ $cek['layanan'] ? ' · '.$services[$cek['layanan']] : '' }}{{ $cek['orang'] ? ' · minimal '.$cek['orang'].' kursi' : '' }}.
                            Dicek untuk paket terpendek tiap kendaraan.
                        </p>
                    </div>
                    <form method="GET" action="{{ route('pelanggan.beranda') }}#cek-unit" data-cek-ajax>
                        <button class="inline-flex min-h-11 items-center border-0 bg-transparent px-0 font-semibold text-brand-strong underline underline-offset-2 hover:text-ink">Reset pencarian</button>
                    </form>
                </div>
                @if ($daftar->isEmpty())
                    <div class="rr-card px-5 py-10 text-center text-ink-2"><strong class="mb-1 block text-ink">Semua unit sudah terpesan di jam itu</strong>Coba jam lain, layanan lain, atau lihat <a href="{{ route('pelanggan.katalog') }}" class="font-semibold text-brand-strong underline underline-offset-2">katalog</a>.</div>
                @endif
                @include('partials.kartu-kendaraan', ['daftar' => $daftar, 'cek' => $cek])
            </div>
        @endif
        </div>
</section>

{{-- ============ SEWA LAGI ============ --}}
@if ($pernah->isNotEmpty())
<section class="mb-8" aria-labelledby="judul-lagi">
    <h2 id="judul-lagi" class="m-0 mb-3 text-lg font-bold">Pernah Anda sewa</h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($pernah as ['vehicle' => $v, 'terakhir' => $kapan])
            <article class="rr-card flex items-center gap-3 p-3">
                <span class="{{ $thumb }}">
                    @if ($v->photo_url) <img src="{{ $v->photo_url }}" alt="" width="168" height="108" loading="lazy" class="h-full w-full object-contain p-1"> @else <x-car-icon :type="$v->icon" /> @endif
                </span>
                <span class="min-w-0 flex-1 leading-snug">
                    <b class="block truncate">{{ $v->name }}</b>
                    <span class="block text-xs text-muted">Terakhir {{ $kapan->translatedFormat('d M Y') }} · mulai {{ $rp($v->min_price) }}</span>
                </span>
                <a href="{{ route('pelanggan.pesan', $v) }}" class="btn btn-outline btn-sm flex-none" aria-label="Pesan lagi {{ $v->name }}">Pesan lagi</a>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- ============ RINGKASAN AKUN: satu baris, bukan deretan kartu angka ============ --}}
@if ($adaPesanan)
    <p class="m-0 border-t border-line pt-5 text-sm text-ink-2">
        {{ $ringkas['selesai'] }} sewa selesai · total dibayar <b class="tabular-nums text-ink">{{ $rp($ringkas['dibayar']) }}</b>@if ($ringkas['sisa']) · sisa tagihan <b class="tabular-nums text-warn">{{ $rp($ringkas['sisa']) }}</b>@endif
        · <a href="{{ route('pelanggan.pesanan') }}" class="font-semibold text-brand-strong underline underline-offset-2">Semua pesanan</a>
        · <a href="{{ route('pelanggan.akun') }}" class="font-semibold text-brand-strong underline underline-offset-2">Ubah data akun</a>
    </p>
@endif

<script>
(() => {
    // hitung mundur batas bayar; saat habis, muat ulang supaya status pesanan ikut berubah
    const els = [...document.querySelectorAll('[data-countdown]')];
    if (!els.length) return;
    const pad = (n) => String(n).padStart(2, '0');
    const tick = () => {
        let habis = false;
        els.forEach((el) => {
            const s = Math.max(0, Math.round((new Date(el.dataset.countdown) - Date.now()) / 1000));
            el.textContent = `${pad(Math.floor(s / 3600))}:${pad(Math.floor(s % 3600 / 60))}:${pad(s % 60)}`;
            if (s === 0) habis = true;
        });
        if (habis) { setTimeout(() => location.reload(), 2000); return; }
        setTimeout(tick, 1000 - (Date.now() % 1000) + 5);
    };
    tick();
})();
</script>
@include('partials.landing-motion')
@include('partials.cek-unit-ajax')
@endsection
