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

<div class="dsh-head">
    <div>
        <h2>Dashboard</h2>
        <p class="page-sub">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="row">
        <a class="btn btn-outline btn-sm" href="{{ route('admin.laporan') }}">Laporan</a>
        <a class="btn btn-sm" href="{{ route('admin.pesanan') }}">Kelola pesanan</a>
    </div>
</div>

{{-- ============ KPI: satu strip, dipisah garis ============ --}}
<section class="dsh-kpis" aria-label="Ringkasan">
    <div class="dsh-kpi">
        <span class="dsh-kpi__label">Pendapatan {{ now()->translatedFormat('F') }}</span>
        <span class="dsh-kpi__value">{{ $rp($stats['pendapatan_bulan']) }}</span>
        <span class="dsh-kpi__meta">
            @if ($deltaBulan !== null)
                <span @class(['dsh-delta', 'is-up' => $deltaBulan >= 0, 'is-down' => $deltaBulan < 0])>{{ $deltaBulan >= 0 ? '+' : '−' }}{{ abs($deltaBulan) }}%</span>
                vs {{ now()->subMonthNoOverflow()->translatedFormat('F') }}
            @else
                Belum ada pembanding
            @endif
        </span>
    </div>
    <a class="dsh-kpi" href="{{ route('admin.pesanan', ['status' => 'sedang_disewa']) }}">
        <span class="dsh-kpi__label">Sedang disewa</span>
        <span class="dsh-kpi__value">{{ $stats['aktif'] }}</span>
        <span class="dsh-kpi__meta">{{ $unitJalan }}/{{ $unitTotal }} unit jalan · utilisasi {{ $utilisasi }}%</span>
    </a>
    <a class="dsh-kpi" href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}">
        <span class="dsh-kpi__label">@if ($perluCek)<i class="dsh-ind is-warn"></i>@endif Perlu dicek</span>
        <span class="dsh-kpi__value">{{ $perluCek }}</span>
        <span class="dsh-kpi__meta">{{ $stats['menunggu_bayar'] }} bukti bayar · {{ $stats['menunggu_konfirmasi_lunas'] }} siap konfirmasi</span>
    </a>
    <a class="dsh-kpi" href="{{ route('admin.pesanan', ['status' => 'sedang_disewa']) }}">
        <span class="dsh-kpi__label">@if ($stats['telat'])<i class="dsh-ind is-danger"></i>@endif Telat kembali</span>
        <span @class(['dsh-kpi__value', 'is-danger' => $stats['telat'] > 0])>{{ $stats['telat'] }}</span>
        <span class="dsh-kpi__meta">{{ $stats['telat'] ? 'Perlu dihubungi' : 'Semua tepat waktu' }}</span>
    </a>
</section>

<div class="dsh-row">
    {{-- ============ GRAFIK ============ --}}
    <section class="dsh-card dsh-chart">
        <header class="dsh-card__head">
            <div>
                <h3>Pendapatan diterima</h3>
                <p>30 hari terakhir</p>
            </div>
            <div class="dsh-chart__total">
                <span>{{ $rp($total30) }}</span>
                @if ($delta30 !== null)
                    <small><b @class(['dsh-delta', 'is-up' => $delta30 >= 0, 'is-down' => $delta30 < 0])>{{ $delta30 >= 0 ? '+' : '−' }}{{ abs($delta30) }}%</b> vs 30 hari sebelumnya</small>
                @endif
            </div>
        </header>

        <div class="dsh-plot" data-plot>
            {{-- garis bantu + label sumbu (HTML, jadi ukuran huruf tidak ikut melar) --}}
            <div class="dsh-plot__grid" aria-hidden="true">
                @for ($t = 4; $t >= 0; $t--)
                    <div class="dsh-plot__line" style="bottom:{{ $t * 25 }}%"><span>{{ $t ? $ringkas($step * $t) : 'Rp0' }}</span></div>
                @endfor
            </div>

            <ol class="dsh-plot__bars" aria-label="Pendapatan per hari">
                @foreach ($g as $i => $d)
                    <li @class(['is-weekend' => $d['akhir_pekan'], 'is-today' => $loop->last, 'is-zero' => ! $d['total']])
                        data-day="{{ $d['hari'] }}" data-value="{{ $rp($d['total']) }}" tabindex="-1">
                        <span class="dsh-bar" style="height:{{ $d['total'] ? max(1.5, $d['total'] / $atas * 100) : 0 }}%"></span>
                        <span class="sr-only">{{ $d['hari'] }}: {{ $rp($d['total']) }}</span>
                    </li>
                @endforeach
            </ol>

            <div class="dsh-plot__x" aria-hidden="true">
                @foreach ($g as $i => $d)
                    <span>{{ ($i % 7 === 1 || $loop->last) ? ($loop->last ? 'Hari ini' : $d['label']) : '' }}</span>
                @endforeach
            </div>
            <div class="dsh-tip" role="status" hidden></div>
        </div>

        <footer class="dsh-chart__foot">
            <div><span>Hari dengan pemasukan</span><b>{{ $hariAda }} dari 30</b></div>
            <div><span>Rata-rata per hari aktif</span><b>{{ $rp($hariAda ? intdiv($total30, $hariAda) : 0) }}</b></div>
            <div><span>Tertinggi</span><b>{{ $puncak && $puncak['total'] ? $rp($puncak['total']).' · '.$puncak['label'] : '–' }}</b></div>
        </footer>
    </section>

    {{-- ============ ARMADA & STATUS ============ --}}
    <div class="dsh-col">
        <section class="dsh-card">
            <header class="dsh-card__head"><h3>Armada</h3><a href="{{ route('admin.kendaraan') }}">Kelola</a></header>
            <dl class="dsh-fleet">
                <div><dt>Tersedia</dt><dd>{{ (int) ($statusUnit['tersedia'] ?? 0) }}</dd></div>
                <div><dt>Disewa</dt><dd>{{ $unitJalan }}</dd></div>
                <div><dt>Perawatan</dt><dd>{{ (int) ($statusUnit['perawatan'] ?? 0) }}</dd></div>
            </dl>
            <div class="dsh-util">
                <div class="dsh-util__track"><i style="width:{{ $utilisasi }}%"></i></div>
                <span>Utilisasi {{ $utilisasi }}% dari {{ $unitTotal }} unit</span>
            </div>
        </section>

        <section class="dsh-card">
            <header class="dsh-card__head"><h3>Status pesanan</h3><span class="dsh-muted">{{ $stats['total_pesanan'] }} total</span></header>
            @php
                $label = ['menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
                $semua = max(1, $statusPesanan->sum());
            @endphp
            <ul class="dsh-list">
                @foreach ($label as $k => $l)
                    @php $n = (int) ($statusPesanan[$k] ?? 0); @endphp
                    <li>
                        <a href="{{ route('admin.pesanan', ['status' => $k]) }}">
                            <span class="dsh-list__name">{{ $l }}</span>
                            <span class="dsh-list__track"><i style="width:{{ $n / $semua * 100 }}%"></i></span>
                            <span class="dsh-list__num">{{ $n }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>

<div class="dsh-row">
    {{-- ============ PERLU DITINDAK ============ --}}
    <section class="dsh-card">
        <header class="dsh-card__head">
            <h3>Perlu ditindak @if ($jumlahTugas)<span class="dsh-count">{{ $jumlahTugas }}</span>@endif</h3>
            <a href="{{ route('admin.pesanan') }}">Semua pesanan</a>
        </header>

        @if (! $jumlahTugas)
            <p class="dsh-empty">Tidak ada yang perlu ditindak. Bukti bayar baru, serah terima, dan kendaraan telat akan muncul di sini.</p>
        @endif

        <div class="dsh-tasks">
            @if ($telat->isNotEmpty())
                <h4><i class="dsh-ind is-danger"></i>Kendaraan telat kembali</h4>
                @foreach ($telat as $o)
                    <div class="dsh-task">
                        <div class="dsh-task__main">
                            <b>{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span>{{ $o->code }} · {{ $o->user->name }} · jadwal {{ $o->end_at->translatedFormat('d M H:i') }}</span>
                        </div>
                        <span class="dsh-task__meta is-danger">{{ $o->end_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]) }}</span>
                        @if ($o->user->phone)
                            <a class="btn btn-outline btn-sm" target="_blank" rel="noopener"
                               href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $o->user->phone)) }}?text={{ rawurlencode('Halo '.$o->user->name.', pesanan '.$o->code.' sudah melewati jadwal kembali. Mohon kabari posisi kendaraan.') }}">Hubungi</a>
                        @endif
                    </div>
                @endforeach
            @endif

            @if ($pembayaranMenunggu->isNotEmpty())
                <h4><i class="dsh-ind is-warn"></i>Bukti bayar masuk</h4>
                @foreach ($pembayaranMenunggu as $p)
                    <div class="dsh-task">
                        <div class="dsh-task__main">
                            <b>{{ $rp($p->amount) }}</b>@if ($p->bank) <span class="dsh-muted">ke {{ $p->bank }}</span>@endif
                            <span>{{ $p->order->code }} · {{ $p->order->user->name }} · {{ $p->order->unit->vehicle->name }}</span>
                        </div>
                        <span class="dsh-task__meta">{{ $p->created_at->diffForHumans(null, true) }}</span>
                        <div class="dsh-task__actions">
                            @if ($p->proof_path) <a class="dsh-link" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti</a> @endif
                            <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" onsubmit="return confirm('Tolak pembayaran ini?')">@csrf @method('PATCH')
                                <input type="hidden" name="action" value="ditolak"><button class="btn btn-outline btn-sm">Tolak</button></form>
                            <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">@csrf @method('PATCH')
                                <input type="hidden" name="action" value="diterima"><button class="btn btn-sm">Terima</button></form>
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($stats['menunggu_konfirmasi_lunas'])
                <h4><i class="dsh-ind"></i>Siap dikonfirmasi</h4>
                <a class="dsh-task dsh-task--link" href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}">
                    <div class="dsh-task__main">
                        <b>{{ $stats['menunggu_konfirmasi_lunas'] }} pesanan sudah dibayar</b>
                        <span>Pembayaran diterima, tinggal dikonfirmasi</span>
                    </div>
                    <span class="dsh-task__meta">Buka →</span>
                </a>
            @endif

            @if ($kembaliHariIni->isNotEmpty())
                <h4><i class="dsh-ind"></i>Dijadwalkan kembali hari ini</h4>
                @foreach ($kembaliHariIni as $o)
                    <div class="dsh-task">
                        <span class="dsh-task__time">{{ $o->end_at->format('H:i') }}</span>
                        <div class="dsh-task__main">
                            <b>{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span>{{ $o->code }} · {{ $o->user->name }}</span>
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($serahTerima->isNotEmpty())
                <h4><i class="dsh-ind"></i>Serah terima 2 hari ke depan</h4>
                @foreach ($serahTerima as $o)
                    <div class="dsh-task">
                        <span class="dsh-task__time">
                            {{ $o->start_at->isToday() ? 'Hari ini' : ($o->start_at->isTomorrow() ? 'Besok' : $o->start_at->translatedFormat('D')) }}
                            <small>{{ $o->start_at->format('H:i') }}</small>
                        </span>
                        <div class="dsh-task__main">
                            <b>{{ $o->unit->vehicle->name }}</b> <span class="plate">{{ $o->unit->plate_number }}</span>
                            <span>{{ $o->code }} · {{ $o->user->name }} · {{ $o->pickup_location ?: 'lokasi belum diisi' }}</span>
                        </div>
                        @if ($o->start_at->isToday())
                            <form method="POST" action="{{ route('admin.pesanan.mulai', $o) }}">@csrf @method('PATCH')
                                <button class="btn btn-outline btn-sm">Serah terima</button></form>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- ============ TERLARIS ============ --}}
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Kendaraan terlaris</h3><span class="dsh-muted">jumlah pesanan</span></header>
        @if ($terlaris->isEmpty())
            <p class="dsh-empty">Belum ada data.</p>
        @else
            @php $atasT = max(1, $terlaris->first()->total); @endphp
            <ol class="dsh-list dsh-list--rank">
                @foreach ($terlaris as $i => $t)
                    <li>
                        <span class="dsh-list__rank">{{ $i + 1 }}</span>
                        <span class="dsh-list__name" title="{{ $t->name }}">{{ $t->name }}</span>
                        <span class="dsh-list__track"><i style="width:{{ $t->total / $atasT * 100 }}%"></i></span>
                        <span class="dsh-list__num">{{ $t->total }}</span>
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
    const tip = plot.querySelector('.dsh-tip');
    const bars = [...plot.querySelectorAll('.dsh-plot__bars li')];
    const show = (li) => {
        bars.forEach((b) => b.classList.toggle('is-active', b === li));
        tip.innerHTML = `<span>${li.dataset.day}</span><b>${li.dataset.value}</b>`;
        tip.hidden = false;
        const p = plot.getBoundingClientRect(), r = li.getBoundingClientRect();
        const x = r.left - p.left + r.width / 2;
        tip.style.left = Math.max(tip.offsetWidth / 2, Math.min(p.width - tip.offsetWidth / 2, x)) + 'px';
    };
    const hide = () => { tip.hidden = true; bars.forEach((b) => b.classList.remove('is-active')); };
    bars.forEach((li) => li.addEventListener('pointerenter', () => show(li)));
    plot.addEventListener('pointerleave', hide);
    // keyboard: panah kiri/kanan di dalam grafik
    const list = plot.querySelector('.dsh-plot__bars');
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
