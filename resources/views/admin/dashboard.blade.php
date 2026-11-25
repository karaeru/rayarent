@extends('app')
@section('title', 'Dashboard Admin · '.config('rayarent.nama'))
@section('content')
<div class="page-head">
    <div><h2>Dashboard</h2><p class="page-sub">Halo, {{ auth()->user()->name }}. Ringkasan operasional RAYARENT.</p></div>
    <a class="btn btn-outline" href="{{ route('admin.laporan') }}">Laporan transaksi</a>
</div>

<div class="stats">
    <div class="stat"><span class="stat__num">{{ $stats['total_pesanan'] }}</span><span class="stat__lbl">Total pesanan</span></div>
    <div class="stat"><span class="stat__num" style="color:var(--warn)">{{ $stats['menunggu'] }}</span><span class="stat__lbl">Menunggu konfirmasi</span></div>
    <div class="stat"><span class="stat__num" style="color:var(--success)">{{ $stats['aktif'] }}</span><span class="stat__lbl">Sedang disewa</span></div>
    <div class="stat"><span class="stat__num" style="color:var(--info)">{{ $stats['menunggu_bayar'] }}</span><span class="stat__lbl">Bukti bayar masuk</span></div>
    <div class="stat"><span class="stat__num" style="color:{{ $stats['telat'] ? 'var(--danger)' : 'inherit' }}">{{ $stats['telat'] }}</span><span class="stat__lbl">Telat kembali</span></div>
    <div class="stat"><span class="stat__num is-money">Rp{{ number_format($stats['pendapatan_bulan'], 0, ',', '.') }}</span><span class="stat__lbl">Diterima bulan ini</span></div>
</div>

@if ($telat->isNotEmpty())
<section class="panel panel--danger">
    <div class="panel__head"><h3 style="color:var(--danger)">Kendaraan telat kembali ({{ $telat->count() }})</h3></div>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Pesanan</th><th>Kendaraan</th><th>Pelanggan</th><th>Batas kembali</th><th>Telat</th></tr></thead>
        <tbody>
        @foreach ($telat as $o)
            <tr>
                <td><a href="{{ route('admin.pesanan', ['search' => $o->code]) }}"><b>{{ $o->code }}</b></a></td>
                <td>{{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span></td>
                <td>{{ $o->user->name }}<br><span class="muted small">{{ $o->user->phone ?? '-' }}</span></td>
                <td class="nowrap">{{ $o->end_at->translatedFormat('d M H:i') }}</td>
                <td><b>{{ $o->end_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</b></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</section>
@endif

@if ($pembayaranMenunggu->isNotEmpty())
<section class="panel">
    <div class="panel__head"><h3>Bukti bayar menunggu verifikasi ({{ $pembayaranMenunggu->count() }})</h3></div>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Kendaraan</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($pembayaranMenunggu as $p)
            <tr>
                <td><b>{{ $p->order->code }}</b><br><span class="muted small">{{ $p->created_at->translatedFormat('d M H:i') }}</span></td>
                <td>{{ $p->order->user->name }}</td>
                <td>{{ $p->order->unit->vehicle->name }}</td>
                <td><b>Rp{{ number_format($p->amount, 0, ',', '.') }}</b>@if ($p->bank)<br><span class="muted small">ke {{ $p->bank }}</span>@endif</td>
                <td>@if ($p->proof_path)<a href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat</a>@else <span class="muted">-</span>@endif</td>
                <td class="nowrap">
                    <form class="inline-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">
                        @csrf @method('PATCH')<input type="hidden" name="action" value="diterima">
                        <button class="btn btn-success btn-sm">Terima</button>
                    </form>
                    <form class="inline-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">
                        @csrf @method('PATCH')<input type="hidden" name="action" value="ditolak">
                        <button class="btn btn-danger-outline btn-sm" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</section>
@endif

@php
    $maks = max(1, collect($grafik)->max('total'));
    $total30 = collect($grafik)->sum('total');
@endphp
<section class="panel">
    <div class="panel__head">
        <h3>Pendapatan masuk per hari</h3>
        <span class="muted small">30 hari terakhir: <b style="color:var(--ink)">Rp{{ number_format($total30, 0, ',', '.') }}</b></span>
    </div>
    <p class="muted small">Pembayaran yang sudah diterima admin.</p>
    <svg class="chart" viewBox="0 0 600 150" width="100%" role="img" aria-label="Grafik pendapatan masuk per hari, 30 hari terakhir">
        @foreach ($grafik as $i => $g)
            @php $h = $g['total'] / $maks * 110; @endphp
            <rect x="{{ $i * 20 + 2 }}" y="{{ 122 - $h }}" width="15" height="{{ max($h, 1) }}" rx="2">
                <title>{{ $g['label'] }}: Rp{{ number_format($g['total'], 0, ',', '.') }}</title>
            </rect>
        @endforeach
        <text x="2" y="144" font-size="11">{{ $grafik[0]['label'] }}</text>
        <text x="300" y="144" font-size="11" text-anchor="middle">{{ $grafik[14]['label'] }}</text>
        <text x="598" y="144" font-size="11" text-anchor="end">{{ $grafik[29]['label'] }}</text>
    </svg>
</section>

<div class="panels">
    <section class="panel">
        <h3 class="card-title">Status pesanan</h3>
        @php
            $label = ['menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
            $mSt = max(1, $statusPesanan->max() ?? 1);
        @endphp
        @foreach ($label as $k => $l)
            @php $n = $statusPesanan[$k] ?? 0; @endphp
            <div class="bar-row"><span class="bar-row__name">{{ $l }}</span><div class="bar-row__track"><i style="width:{{ $n / $mSt * 100 }}%"></i></div><b>{{ $n }}</b></div>
        @endforeach
    </section>
    <section class="panel">
        <h3 class="card-title">Kendaraan terlaris</h3>
        @forelse ($terlaris as $t)
            <div class="bar-row"><span class="bar-row__name">{{ $t->name }}</span><div class="bar-row__track"><i style="width:{{ $t->total / $terlaris->first()->total * 100 }}%"></i></div><b>{{ $t->total }}×</b></div>
        @empty
            <p class="muted small">Belum ada data.</p>
        @endforelse
    </section>
    <section class="panel">
        <h3 class="card-title">Status unit</h3>
        @php $totalUnit = max(1, $statusUnit->sum()); @endphp
        @foreach (['tersedia' => 'Tersedia', 'disewa' => 'Disewa', 'perawatan' => 'Perawatan'] as $k => $l)
            @php $n = $statusUnit[$k] ?? 0; @endphp
            <div class="bar-row"><span class="bar-row__name">{{ $l }}</span><div class="bar-row__track"><i style="width:{{ $n / $totalUnit * 100 }}%"></i></div><b>{{ $n }}</b></div>
        @endforeach
    </section>
</div>

<section class="panel">
    <h3 class="card-title">Serah terima 2 hari ke depan</h3>
    @forelse ($serahTerima as $o)
        <div class="row" style="padding:9px 0;border-bottom:1px solid var(--line)">
            <b>{{ $o->start_at->translatedFormat('D, d M H:i') }}</b>
            <span>{{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span></span>
            <span class="muted">{{ $o->user->name }} · {{ $o->pickup_location ?: 'lokasi belum diisi' }}</span>
        </div>
    @empty
        <p class="muted small" style="margin:0">Tidak ada serah terima terjadwal.</p>
    @endforelse
    <p class="mt-2" style="margin-bottom:0"><a href="{{ route('admin.pesanan') }}">Kelola semua pesanan →</a></p>
</section>
@endsection
