@extends('app')
@section('title', 'Laporan · Admin RAYARENT')
@section('content')
<style>
    .tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tbl th{text-align:left;color:var(--mu);font-weight:600;padding:8px;border-bottom:1px solid var(--ln);white-space:nowrap}
    .tbl td{padding:8px;border-bottom:1px solid var(--ln);vertical-align:top}
    .sm{padding:7px 9px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit;font-size:14px;width:auto}
    .sum{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin:14px 0}
    .sum div{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:14px}
    .sum b{font-size:20px;display:block}
</style>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <h2 style="margin:0">Laporan transaksi</h2>
    <a class="btn y" style="text-decoration:none" href="{{ route('admin.laporan.unduh', request()->query()) }}">Unduh CSV</a>
</div>

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:14px">
    <span style="color:var(--mu);font-size:13px">Mulai sewa</span>
    <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" class="sm">
    <span style="color:var(--mu);font-size:13px">s/d</span>
    <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" class="sm">
    <select name="status" class="sm">
        <option value="">Semua status</option>
        @foreach (['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $k => $l)
            <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="btn y" style="padding:7px 14px">Terapkan</button>
    @if ($filters)<a href="{{ route('admin.laporan') }}">Reset</a>@endif
</form>
@if ($errors->any())<p class="err">{{ $errors->first() }}</p>@endif

<div class="sum">
    <div><span style="color:var(--mu);font-size:13px">Jumlah pesanan</span><b>{{ $jumlah }}</b></div>
    <div><span style="color:var(--mu);font-size:13px">Total tagihan</span><b>Rp{{ number_format($tagihan, 0, ',', '.') }}</b></div>
    <div><span style="color:var(--mu);font-size:13px">Sudah diterima</span><b>Rp{{ number_format($diterima, 0, ',', '.') }}</b></div>
    <div><span style="color:var(--mu);font-size:13px">Belum dibayar</span><b>Rp{{ number_format(max(0, $tagihan - $diterima), 0, ',', '.') }}</b></div>
</div>
<p style="color:var(--mu);font-size:12px;margin:0 0 12px">Ringkasan tidak menghitung pesanan yang dibatalkan.</p>

<div style="background:var(--sf);border:1px solid var(--ln);border-radius:12px;overflow-x:auto">
<table class="tbl">
    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Kendaraan</th><th>Mulai</th><th>Status</th><th>Pembayaran</th><th>Total</th><th>Diterima</th></tr></thead>
    <tbody>
    @forelse ($orders as $o)
        <tr>
            <td><b>{{ $o->code }}</b></td>
            <td>{{ $o->user->name }}</td>
            <td>{{ $o->unit->vehicle->name }} <span class="plate" style="font-size:11px">{{ $o->unit->plate_number }}</span></td>
            <td style="white-space:nowrap">{{ $o->start_at->format('d/m/Y H:i') }}</td>
            <td>{{ str_replace('_', ' ', $o->status) }}</td>
            <td>{{ $o->payment_status }}</td>
            <td>Rp{{ number_format($o->grand_total, 0, ',', '.') }}</td>
            <td>Rp{{ number_format($o->paid_amount, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="8" style="text-align:center;color:var(--mu);padding:22px">Tidak ada transaksi pada filter ini.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@if ($orders->hasPages())<div style="margin-top:14px">{{ $orders->links() }}</div>@endif
@endsection
