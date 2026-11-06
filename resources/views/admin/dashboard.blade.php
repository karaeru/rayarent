@extends('app')
@section('title', 'Dashboard Admin · RAYARENT')
@section('content')
<style>
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:20px}
    .stat{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:16px;text-align:center}
    .stat .num{font-size:28px;font-weight:800;line-height:1}
    .stat .lbl{font-size:12px;color:var(--mu);margin-top:4px}
    .tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tbl th{text-align:left;color:var(--mu);font-weight:600;padding:6px 8px;border-bottom:1px solid var(--ln)}
    .tbl td{padding:8px;border-bottom:1px solid var(--ln);vertical-align:top}
    .section-box{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:20px;margin-bottom:20px}
    .cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:20px}
    .cols .section-box{margin:0}
    .bar-row{display:flex;gap:8px;align-items:center;margin:7px 0;font-size:13px}
    .bar-row .nm{width:130px;flex:none}
    .bar-row .tr{flex:1;background:var(--ln);border-radius:4px;height:8px}
    .bar-row .tr i{display:block;height:8px;border-radius:4px;background:#4f86c6}
    .action-form{display:inline}
    .wrap{overflow-x:auto}
</style>

<h2 style="margin:0 0 4px">Dashboard</h2>
<p style="color:var(--mu);margin:0 0 20px">Halo, {{ auth()->user()->name }}.</p>

<div class="stat-grid">
    <div class="stat"><div class="num">{{ $stats['total_pesanan'] }}</div><div class="lbl">Total pesanan</div></div>
    <div class="stat"><div class="num" style="color:#854d0e">{{ $stats['menunggu'] }}</div><div class="lbl">Menunggu konfirmasi</div></div>
    <div class="stat"><div class="num" style="color:#166534">{{ $stats['aktif'] }}</div><div class="lbl">Sedang disewa</div></div>
    <div class="stat"><div class="num" style="color:#1e40af">{{ $stats['menunggu_bayar'] }}</div><div class="lbl">Bukti bayar masuk</div></div>
    <div class="stat"><div class="num" style="color:{{ $stats['telat'] ? 'var(--rd)' : 'inherit' }}">{{ $stats['telat'] }}</div><div class="lbl">Telat kembali</div></div>
    <div class="stat"><div class="num" style="font-size:18px">Rp{{ number_format($stats['pendapatan_bulan'], 0, ',', '.') }}</div><div class="lbl">Diterima bulan ini</div></div>
</div>

{{-- Perlu tindakan --}}
@if ($telat->isNotEmpty())
<div class="section-box" style="border-color:var(--rd)">
    <h3 style="margin:0 0 10px;color:var(--rd)">Kendaraan telat kembali ({{ $telat->count() }})</h3>
    <div class="wrap"><table class="tbl">
        <thead><tr><th>Pesanan</th><th>Kendaraan</th><th>Pelanggan</th><th>Batas kembali</th><th>Telat</th></tr></thead>
        <tbody>
        @foreach ($telat as $o)
            <tr>
                <td><a href="{{ route('admin.pesanan', ['search' => $o->code]) }}"><b>{{ $o->code }}</b></a></td>
                <td>{{ $o->unit->vehicle->name }} <span class="plate" style="font-size:11px">{{ $o->unit->plate_number }}</span></td>
                <td>{{ $o->user->name }}<br><span style="font-size:12px;color:var(--mu)">{{ $o->user->phone ?? '-' }}</span></td>
                <td>{{ $o->end_at->translatedFormat('d M H:i') }}</td>
                <td><b>{{ $o->end_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</b></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

@if ($pembayaranMenunggu->isNotEmpty())
<div class="section-box">
    <h3 style="margin:0 0 14px">Bukti bayar menunggu verifikasi ({{ $pembayaranMenunggu->count() }})</h3>
    <div class="wrap"><table class="tbl">
        <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Kendaraan</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($pembayaranMenunggu as $p)
            <tr>
                <td><b>{{ $p->order->code }}</b><br><span style="color:var(--mu);font-size:12px">{{ $p->created_at->translatedFormat('d M H:i') }}</span></td>
                <td>{{ $p->order->user->name }}</td>
                <td>{{ $p->order->unit->vehicle->name }}</td>
                <td><b>Rp{{ number_format($p->amount, 0, ',', '.') }}</b></td>
                <td>@if ($p->proof_path)<a href="{{ route('bukti', $p) }}" target="_blank" style="font-size:13px">Lihat</a>@else <span style="color:var(--mu)">-</span>@endif</td>
                <td>
                    <form class="action-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">
                        @csrf @method('PATCH')<input type="hidden" name="action" value="diterima">
                        <button class="btn" style="background:#1f7a4d;color:#fff;font-size:13px;padding:5px 10px">Terima</button>
                    </form>
                    <form class="action-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" style="margin-left:4px">
                        @csrf @method('PATCH')<input type="hidden" name="action" value="ditolak">
                        <button class="btn" style="background:var(--rd);color:#fff;font-size:13px;padding:5px 10px" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

{{-- Grafik pendapatan --}}
@php
    $maks = max(1, collect($grafik)->max('total'));
    $total30 = collect($grafik)->sum('total');
@endphp
<div class="section-box">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:6px">
        <h3 style="margin:0">Pendapatan masuk per hari</h3>
        <span style="color:var(--mu);font-size:13px">30 hari terakhir: <b style="color:var(--tx)">Rp{{ number_format($total30, 0, ',', '.') }}</b></span>
    </div>
    <p style="margin:2px 0 8px;color:var(--mu);font-size:13px">Pembayaran yang sudah diterima admin.</p>
    <svg viewBox="0 0 600 150" width="100%" role="img" aria-label="Grafik pendapatan masuk per hari, 30 hari terakhir">
        @foreach ($grafik as $i => $g)
            @php $h = $g['total'] / $maks * 110; @endphp
            <rect x="{{ $i * 20 + 2 }}" y="{{ 122 - $h }}" width="15" height="{{ max($h, 1) }}" rx="2" fill="#4f86c6">
                <title>{{ $g['label'] }}: Rp{{ number_format($g['total'], 0, ',', '.') }}</title>
            </rect>
        @endforeach
        <text x="2" y="144" font-size="11" fill="currentColor" opacity=".6">{{ $grafik[0]['label'] }}</text>
        <text x="300" y="144" font-size="11" text-anchor="middle" fill="currentColor" opacity=".6">{{ $grafik[14]['label'] }}</text>
        <text x="598" y="144" font-size="11" text-anchor="end" fill="currentColor" opacity=".6">{{ $grafik[29]['label'] }}</text>
    </svg>
</div>

<div class="cols">
    <div class="section-box">
        <h3 style="margin:0 0 6px">Status pesanan</h3>
        @php
            $label = ['menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
            $mSt = max(1, $statusPesanan->max() ?? 1);
        @endphp
        @foreach ($label as $k => $l)
            @php $n = $statusPesanan[$k] ?? 0; @endphp
            <div class="bar-row"><span class="nm">{{ $l }}</span><div class="tr"><i style="width:{{ $n / $mSt * 100 }}%"></i></div><b>{{ $n }}</b></div>
        @endforeach
    </div>
    <div class="section-box">
        <h3 style="margin:0 0 6px">Kendaraan terlaris</h3>
        @forelse ($terlaris as $t)
            <div class="bar-row"><span class="nm">{{ $t->name }}</span><div class="tr"><i style="width:{{ $t->total / $terlaris->first()->total * 100 }}%"></i></div><b>{{ $t->total }}×</b></div>
        @empty
            <p style="color:var(--mu);font-size:13px">Belum ada data.</p>
        @endforelse
    </div>
    <div class="section-box">
        <h3 style="margin:0 0 6px">Status unit</h3>
        @php $totalUnit = max(1, $statusUnit->sum()); @endphp
        @foreach (['tersedia' => 'Tersedia', 'disewa' => 'Disewa', 'perawatan' => 'Perawatan'] as $k => $l)
            @php $n = $statusUnit[$k] ?? 0; @endphp
            <div class="bar-row"><span class="nm">{{ $l }}</span><div class="tr"><i style="width:{{ $n / $totalUnit * 100 }}%"></i></div><b>{{ $n }}</b></div>
        @endforeach
    </div>
</div>

<div class="section-box">
    <h3 style="margin:0 0 10px">Serah terima 2 hari ke depan</h3>
    @forelse ($serahTerima as $o)
        <div style="padding:7px 0;border-bottom:1px solid var(--ln);font-size:14px">
            <b>{{ $o->start_at->translatedFormat('D, d M H:i') }}</b> · {{ $o->unit->vehicle->name }}
            <span class="plate" style="font-size:11px">{{ $o->unit->plate_number }}</span>
            · {{ $o->user->name }} · {{ $o->pickup_location ?: 'lokasi belum diisi' }}
        </div>
    @empty
        <p style="color:var(--mu);font-size:13px;margin:0">Tidak ada serah terima terjadwal.</p>
    @endforelse
    <p style="margin:12px 0 0"><a href="{{ route('admin.pesanan') }}">Kelola semua pesanan →</a> · <a href="{{ route('admin.laporan') }}">Laporan transaksi →</a></p>
</div>
@endsection