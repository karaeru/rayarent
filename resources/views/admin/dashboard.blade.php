@extends('app')
@section('title', 'Dashboard Admin · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $ringkas = function (int $n): string {          // label sumbu: Rp1,5 jt / Rp750 rb
        if ($n >= 1_000_000) return 'Rp'.rtrim(rtrim(number_format($n / 1_000_000, 1, ',', '.'), '0'), ',').' jt';
        if ($n >= 1_000) return 'Rp'.number_format($n / 1_000, 0, ',', '.').' rb';
        return 'Rp'.$n;
    };
    $persen = fn ($kini, $lalu) => $lalu > 0 ? (int) round(($kini - $lalu) / $lalu * 100) : null;

    // KPI
    $deltaBulan = $persen($stats['pendapatan_bulan'], $stats['pendapatan_bulan_lalu']);
    $unitTotal = (int) $statusUnit->sum();
    $unitJalan = (int) ($statusUnit['disewa'] ?? 0);
    $utilisasi = $unitTotal ? (int) round($unitJalan / $unitTotal * 100) : 0;
    $perluCek = $stats['menunggu_bayar'] + $stats['menunggu_konfirmasi_lunas'];
    $jumlahTugas = $telat->count() + $pembayaranMenunggu->count() + $serahTerima->count() + $kembaliHariIni->count() + $stats['menunggu_konfirmasi_lunas'];

    // grafik: skala "rapi" (1 / 2 / 2,5 / 5 × 10^n) supaya garis bantu bernilai bulat
    $g = collect($grafik);
    $total30 = (int) $g->sum('total');
    $delta30 = $persen($total30, $total30Lalu);
    $hariAda = $g->where('total', '>', 0)->count();
    $maks = max(1, (int) $g->max('total'));
    $kasar = $maks / 4;
    $pangkat = 10 ** floor(log10(max(1, $kasar)));
    $step = (int) (collect([1, 2, 2.5, 5, 10])->first(fn ($m) => $m * $pangkat >= $kasar) * $pangkat);
    $atas = $step * 4;
    $puncak = $g->sortByDesc('total')->first();
@endphp
@section('content')

<x-page-head title="Dashboard" :sub="now()->translatedFormat('l, d F Y')">
    <a class="btn btn-outline btn-sm" href="{{ route('admin.laporan') }}">Laporan</a>
    <a class="btn btn-sm" href="{{ route('admin.pesanan') }}">Kelola pesanan</a>
</x-page-head>

{{-- ============ KPI: satu strip, dipisah garis ============ --}}
<section class="rr-card mb-4 grid overflow-hidden sm:grid-cols-2 xl:grid-cols-4 [&>*]:border-line [&>*+*]:border-t sm:[&>*:nth-child(2)]:border-t-0 sm:[&>*:nth-child(even)]:border-l xl:[&>*+*]:border-t-0 xl:[&>*+*]:border-l" aria-label="Ringkasan">
    <div class="flex flex-col gap-1 px-5 py-4 text-ink">
        <span class="text-sm font-medium text-muted">Pendapatan {{ now()->translatedFormat('F') }}</span>
        <span class="text-2xl font-bold tracking-tight tabular-nums">{{ $rp($stats['pendapatan_bulan']) }}</span>
        <span class="text-xs text-muted">
            @if ($deltaBulan !== null)
                <span @class(['font-semibold tabular-nums', 'text-ok' => $deltaBulan >= 0, 'text-bad' => $deltaBulan < 0])>{{ $deltaBulan >= 0 ? '+' : '−' }}{{ abs($deltaBulan) }}%</span>
                vs {{ now()->subMonthNoOverflow()->translatedFormat('F') }}
            @else
                Belum ada pembanding
            @endif
        </span>
    </div>
    <a class="flex flex-col gap-1 px-5 py-4 text-ink transition-colors hover:bg-canvas hover:text-ink hover:no-underline" href="{{ route('admin.pesanan', ['status' => 'sedang_disewa']) }}">
        <span class="text-sm font-medium text-muted">Sedang disewa</span>
        <span class="text-2xl font-bold tracking-tight tabular-nums">{{ $stats['aktif'] }}</span>
        <span class="text-xs text-muted">{{ $unitJalan }}/{{ $unitTotal }} unit jalan · utilisasi {{ $utilisasi }}%</span>
    </a>
    <a class="flex flex-col gap-1 px-5 py-4 text-ink transition-colors hover:bg-canvas hover:text-ink hover:no-underline" href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}">
        <span class="flex items-center gap-1.5 text-sm font-medium text-muted">@if ($perluCek)<i class="inline-block size-2 rounded-full bg-warn" aria-hidden="true"></i>@endif Perlu dicek</span>
        <span class="text-2xl font-bold tracking-tight tabular-nums">{{ $perluCek }}</span>
        <span class="text-xs text-muted">{{ $stats['menunggu_bayar'] }} bukti bayar · {{ $stats['menunggu_konfirmasi_lunas'] }} siap konfirmasi</span>
    </a>
    <a class="flex flex-col gap-1 px-5 py-4 text-ink transition-colors hover:bg-canvas hover:text-ink hover:no-underline" href="{{ route('admin.pesanan', ['status' => 'sedang_disewa']) }}">
        <span class="flex items-center gap-1.5 text-sm font-medium text-muted">@if ($stats['telat'])<i class="inline-block size-2 rounded-full bg-bad" aria-hidden="true"></i>@endif Telat kembali</span>
        <span @class(['text-2xl font-bold tracking-tight tabular-nums', 'text-bad' => $stats['telat'] > 0])>{{ $stats['telat'] }}</span>
        <span class="text-xs text-muted">{{ $stats['telat'] ? 'Perlu dihubungi' : 'Semua tepat waktu' }}</span>
    </a>
</section>

<div class="mb-4 grid items-start gap-4 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
    {{-- ============ GRAFIK ============ --}}
    <section class="rr-card overflow-hidden">
        <header class="mb-4 flex items-start justify-between gap-3 px-5 pt-5">
            <div>
                <h2 class="m-0 flex items-center gap-2 text-base font-semibold">Pendapatan diterima</h2>
                <p class="m-0 mt-0.5 text-sm text-muted">30 hari terakhir</p>
            </div>
            <div class="text-right leading-tight">
                <span class="block text-xl font-bold tracking-tight tabular-nums">{{ $rp($total30) }}</span>
                @if ($delta30 !== null)
                    <small class="text-xs text-muted"><b @class(['font-semibold tabular-nums', 'text-ok' => $delta30 >= 0, 'text-bad' => $delta30 < 0])>{{ $delta30 >= 0 ? '+' : '−' }}{{ abs($delta30) }}%</b> vs 30 hari sebelumnya</small>
                @endif
            </div>
        </header>

        <div class="rr-plot mx-5 mt-2" data-plot>
            {{-- garis bantu + label sumbu (HTML, jadi ukuran huruf tidak ikut melar) --}}
            <div class="rr-plot__grid" aria-hidden="true">
                @for ($t = 4; $t >= 0; $t--)
                    <div class="rr-plot__line" style="bottom:{{ $t * 25 }}%"><span>{{ $t ? $ringkas($step * $t) : 'Rp0' }}</span></div>
                @endfor
            </div>

            <ol class="rr-plot__bars" data-bars aria-label="Pendapatan per hari">
                @foreach ($g as $i => $d)
                    <li @class(['is-today' => $loop->last, 'is-zero' => ! $d['total']])
                        data-day="{{ $d['hari'] }}" data-value="{{ $rp($d['total']) }}" tabindex="-1">
                        <span class="rr-plot__bar" style="height:{{ $d['total'] ? max(1.5, $d['total'] / $atas * 100) : 0 }}%"></span>
                        <span class="sr-only">{{ $d['hari'] }}: {{ $rp($d['total']) }}</span>
                    </li>
                @endforeach
            </ol>

            <div class="rr-plot__x" aria-hidden="true">
                @foreach ($g as $i => $d)
                    <span>{{ ($i % 7 === 1 || $loop->last) ? ($loop->last ? 'Hari ini' : $d['label']) : '' }}</span>
                @endforeach
            </div>
            <div class="pointer-events-none absolute -top-1.5 z-10 flex -translate-x-1/2 -translate-y-full flex-col whitespace-nowrap rounded-md border border-line bg-white px-2.5 py-1.5 text-xs leading-snug shadow-md" data-tip role="status" hidden></div>
        </div>

        <footer class="mt-4 grid divide-y divide-line border-t border-line sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <div class="flex flex-col gap-0.5 px-5 py-3"><span class="text-xs text-muted">Hari dengan pemasukan</span><b class="text-sm font-semibold tabular-nums">{{ $hariAda }} dari 30</b></div>
            <div class="flex flex-col gap-0.5 px-5 py-3"><span class="text-xs text-muted">Rata-rata per hari aktif</span><b class="text-sm font-semibold tabular-nums">{{ $rp($hariAda ? intdiv($total30, $hariAda) : 0) }}</b></div>
            <div class="flex flex-col gap-0.5 px-5 py-3"><span class="text-xs text-muted">Tertinggi</span><b class="text-sm font-semibold tabular-nums">{{ $puncak && $puncak['total'] ? $rp($puncak['total']).' · '.$puncak['label'] : '–' }}</b></div>
        </footer>
    </section>

    {{-- ============ ARMADA & STATUS ============ --}}
    <div class="flex flex-col gap-4">
        <section class="rr-card p-5">
            <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 flex items-center gap-2 text-base font-semibold">Armada</h2><a class="text-sm font-semibold text-brand-strong underline-offset-2 hover:underline" href="{{ route('admin.kendaraan') }}">Kelola</a></header>
            <dl class="m-0 mb-4 grid grid-cols-3 divide-x divide-line">
                <div class="pr-3"><dt class="text-xs text-muted">Tersedia</dt><dd class="m-0 mt-0.5 text-xl font-bold tabular-nums">{{ (int) ($statusUnit['tersedia'] ?? 0) }}</dd></div>
                <div class="px-3"><dt class="text-xs text-muted">Disewa</dt><dd class="m-0 mt-0.5 text-xl font-bold tabular-nums">{{ $unitJalan }}</dd></div>
                <div class="pl-3"><dt class="text-xs text-muted">Perawatan</dt><dd class="m-0 mt-0.5 text-xl font-bold tabular-nums">{{ (int) ($statusUnit['perawatan'] ?? 0) }}</dd></div>
            </dl>
            <span class="block h-1.5 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-brand-strong" style="width:{{ $utilisasi }}%"></i></span>
            <span class="mt-1.5 block text-xs text-muted">Utilisasi {{ $utilisasi }}% dari {{ $unitTotal }} unit</span>
        </section>

        <section class="rr-card p-5">
            <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 flex items-center gap-2 text-base font-semibold">Status pesanan</h2><span class="text-xs text-muted">{{ $stats['total_pesanan'] }} total</span></header>
            @php
                $label = ['menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
                $semua = max(1, $statusPesanan->sum());
            @endphp
            <ul class="m-0 list-none divide-y divide-line/70 p-0">
                @foreach ($label as $k => $l)
                    @php $n = (int) ($statusPesanan[$k] ?? 0); @endphp
                    <li>
                        <a href="{{ route('admin.pesanan', ['status' => $k]) }}" class="group grid min-h-9 grid-cols-[110px_1fr_32px] items-center gap-3 py-1.5 text-sm text-ink-2 hover:text-ink hover:no-underline">
                            <span class="truncate">{{ $l }}</span>
                            <span class="block h-1.5 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-muted/60 group-hover:bg-brand-strong" style="width:{{ $n / $semua * 100 }}%"></i></span>
                            <span class="text-right font-semibold tabular-nums text-ink">{{ $n }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>

<div class="grid items-start gap-4 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
    {{-- ============ PERLU DITINDAK ============ --}}
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3">
            <h2 class="m-0 flex items-center gap-2 text-base font-semibold">Perlu ditindak @if ($jumlahTugas)<span class="rounded-full border border-line bg-canvas px-2 text-xs font-semibold leading-relaxed text-ink-2">{{ $jumlahTugas }}</span>@endif</h2>
            <a class="text-sm font-semibold text-brand-strong underline-offset-2 hover:underline" href="{{ route('admin.pesanan') }}">Semua pesanan</a>
        </header>

        @if (! $jumlahTugas)
            <p class="m-0 text-sm text-muted">Tidak ada yang perlu ditindak. Bukti bayar baru, serah terima, dan kendaraan telat akan muncul di sini.</p>
        @endif

        <div>
            @if ($telat->isNotEmpty())
                <h3 class="mb-0.5 mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted first:mt-0"><i class="inline-block size-2 rounded-full bg-bad" aria-hidden="true"></i>Kendaraan telat kembali</h3>
                @foreach ($telat as $o)
                    <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2 border-b border-line/70 py-2.5 last:border-b-0 sm:flex-nowrap">
                        <div class="min-w-0 flex-1 text-sm">
                            <b class="font-semibold">{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span class="mt-0.5 block truncate text-xs text-muted">{{ $o->code }} · {{ $o->user->name }} · jadwal {{ $o->end_at->translatedFormat('d M H:i') }}</span>
                        </div>
                        <span class="flex-none text-xs font-semibold tabular-nums text-bad">{{ $o->end_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]) }}</span>
                        @if ($o->user->phone)
                            <a class="btn btn-outline btn-sm" target="_blank" rel="noopener"
                               href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $o->user->phone)) }}?text={{ rawurlencode('Halo '.$o->user->name.', pesanan '.$o->code.' sudah melewati jadwal kembali. Mohon kabari posisi kendaraan.') }}">Hubungi</a>
                        @endif
                    </div>
                @endforeach
            @endif

            @if ($pembayaranMenunggu->isNotEmpty())
                <h3 class="mb-0.5 mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted first:mt-0"><i class="inline-block size-2 rounded-full bg-warn" aria-hidden="true"></i>Bukti bayar masuk</h3>
                @foreach ($pembayaranMenunggu as $p)
                    <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2 border-b border-line/70 py-2.5 last:border-b-0 sm:flex-nowrap">
                        <div class="min-w-0 flex-1 text-sm">
                            <b class="font-semibold tabular-nums">{{ $rp($p->amount) }}</b>@if ($p->bank) <span class="text-xs text-muted">ke {{ $p->bank }}</span>@endif
                            <span class="mt-0.5 block truncate text-xs text-muted">{{ $p->order->code }} · {{ $p->order->user->name }} · {{ $p->order->unit->vehicle->name }}</span>
                        </div>
                        <span class="flex-none text-xs tabular-nums text-muted">{{ $p->created_at->diffForHumans(null, true) }}</span>
                        <div class="flex w-full flex-none items-center gap-1.5 sm:w-auto">
                            @if ($p->proof_path) <a class="mr-1 text-sm font-semibold text-brand-strong underline underline-offset-2" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti</a> @endif
                            <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" class="inline" data-confirm-title="Tolak pembayaran ini?" data-confirm="{{ $rp($p->amount) }} dari {{ $p->order->user->name }} untuk {{ $p->order->code }} ditandai ditolak. Pelanggan perlu mengunggah bukti baru." data-confirm-ok="Ya, tolak" data-confirm-tone="danger">@csrf @method('PATCH')
                                <input type="hidden" name="action" value="ditolak"><button class="btn btn-outline btn-sm">Tolak</button></form>
                            <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" class="inline" data-confirm-title="Terima pembayaran ini?" data-confirm="{{ $rp($p->amount) }}{{ $p->bank ? ' ke '.$p->bank : '' }} dari {{ $p->order->user->name }} untuk {{ $p->order->code }} dicatat sebagai diterima. Pastikan dana sudah masuk ke rekening." data-confirm-ok="Ya, terima" data-confirm-tone="ok">@csrf @method('PATCH')
                                <input type="hidden" name="action" value="diterima"><button class="btn btn-sm">Terima</button></form>
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($stats['menunggu_konfirmasi_lunas'])
                <h3 class="mb-0.5 mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted first:mt-0"><i class="inline-block size-2 rounded-full bg-muted" aria-hidden="true"></i>Siap dikonfirmasi</h3>
                <a class="flex flex-wrap items-center gap-x-3.5 gap-y-2 border-b border-line/70 py-2.5 last:border-b-0 sm:flex-nowrap group text-ink hover:text-ink hover:no-underline" href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}">
                    <div class="min-w-0 flex-1 text-sm">
                        <b class="font-semibold">{{ $stats['menunggu_konfirmasi_lunas'] }} pesanan sudah dibayar</b>
                        <span class="mt-0.5 block truncate text-xs text-muted">Pembayaran diterima, tinggal dikonfirmasi</span>
                    </div>
                    <span class="flex-none text-xs font-semibold text-muted group-hover:text-brand-strong">Buka</span>
                </a>
            @endif

            @if ($kembaliHariIni->isNotEmpty())
                <h3 class="mb-0.5 mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted first:mt-0"><i class="inline-block size-2 rounded-full bg-muted" aria-hidden="true"></i>Dijadwalkan kembali hari ini</h3>
                @foreach ($kembaliHariIni as $o)
                    <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2 border-b border-line/70 py-2.5 last:border-b-0 sm:flex-nowrap">
                        <span class="w-14 flex-none text-sm font-semibold tabular-nums">{{ $o->end_at->format('H:i') }}</span>
                        <div class="min-w-0 flex-1 text-sm">
                            <b class="font-semibold">{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span class="mt-0.5 block truncate text-xs text-muted">{{ $o->code }} · {{ $o->user->name }}</span>
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($serahTerima->isNotEmpty())
                <h3 class="mb-0.5 mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted first:mt-0"><i class="inline-block size-2 rounded-full bg-muted" aria-hidden="true"></i>Serah terima 2 hari ke depan</h3>
                @foreach ($serahTerima as $o)
                    <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2 border-b border-line/70 py-2.5 last:border-b-0 sm:flex-nowrap">
                        <span class="w-14 flex-none text-sm font-semibold leading-tight tabular-nums">
                            {{ $o->start_at->isToday() ? 'Hari ini' : ($o->start_at->isTomorrow() ? 'Besok' : $o->start_at->translatedFormat('D')) }}
                            <small class="block font-normal text-muted">{{ $o->start_at->format('H:i') }}</small>
                        </span>
                        <div class="min-w-0 flex-1 text-sm">
                            <b class="font-semibold">{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span class="mt-0.5 block truncate text-xs text-muted">{{ $o->code }} · {{ $o->user->name }} · {{ $o->pickup_location ?: 'lokasi belum diisi' }}</span>
                        </div>
                        @if ($o->start_at->isToday())
                            <form method="POST" action="{{ route('admin.pesanan.mulai', $o) }}" class="inline" data-confirm-title="Serah terima {{ $o->code }}?" data-confirm="{{ $o->unit->vehicle->name }} ({{ $o->unit->plate_number }}) diserahkan ke {{ $o->user->name }}. Status pesanan menjadi Sedang disewa." data-confirm-ok="Ya, serahkan" data-confirm-tone="brand">@csrf @method('PATCH')
                                <button class="btn btn-outline btn-sm">Serah terima</button></form>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- ============ TERLARIS ============ --}}
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 flex items-center gap-2 text-base font-semibold">Kendaraan terlaris</h2><span class="text-xs text-muted">jumlah pesanan</span></header>
        @if ($terlaris->isEmpty())
            <p class="m-0 text-sm text-muted">Belum ada data.</p>
        @else
            @php $atasT = max(1, $terlaris->first()->total); @endphp
            <ol class="m-0 list-none divide-y divide-line/70 p-0">
                @foreach ($terlaris as $i => $t)
                    <li class="grid grid-cols-[18px_minmax(0,120px)_1fr_28px] items-center gap-3 py-1.5 text-sm text-ink-2">
                        <span class="text-xs tabular-nums text-muted">{{ $i + 1 }}</span>
                        <span class="truncate" title="{{ $t->name }}">{{ $t->name }}</span>
                        <span class="block h-1.5 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-brand-strong" style="width:{{ $t->total / $atasT * 100 }}%"></i></span>
                        <span class="text-right font-semibold tabular-nums text-ink">{{ $t->total }}</span>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
</div>

<script>
(() => {
    const plot = document.querySelector('[data-plot]');
    if (!plot) return;
    const tip = plot.querySelector('[data-tip]');
    const bars = [...plot.querySelectorAll('[data-bars] li')];
    const show = (li) => {
        bars.forEach((b) => b.classList.toggle('is-active', b === li));
        tip.innerHTML = `<span class="text-muted">${li.dataset.day}</span><b class="tabular-nums">${li.dataset.value}</b>`;
        tip.hidden = false;
        const p = plot.getBoundingClientRect(), r = li.getBoundingClientRect();
        const x = r.left - p.left + r.width / 2;
        tip.style.left = Math.max(tip.offsetWidth / 2, Math.min(p.width - tip.offsetWidth / 2, x)) + 'px';
    };
    const hide = () => { tip.hidden = true; bars.forEach((b) => b.classList.remove('is-active')); };
    bars.forEach((li) => li.addEventListener('pointerenter', () => show(li)));
    plot.addEventListener('pointerleave', hide);
    // keyboard: panah kiri/kanan di dalam grafik
    const list = plot.querySelector('[data-bars]');
    list.tabIndex = 0;
    let k = bars.length - 1;
    list.addEventListener('focus', () => show(bars[k]));
    list.addEventListener('blur', hide);
    list.addEventListener('keydown', (e) => {
        if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
        e.preventDefault();
        k = Math.max(0, Math.min(bars.length - 1, k + (e.key === 'ArrowRight' ? 1 : -1)));
        show(bars[k]);
    });
})();
</script>
@endsection
