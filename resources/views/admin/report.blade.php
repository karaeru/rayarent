@extends('app')
@section('title', 'Laporan · Admin '.config('rayarent.nama'))
@section('content')
<div class="page-head">
    <div><h2>Laporan transaksi</h2><p class="page-sub">Filter berdasarkan tanggal mulai sewa dan status.</p></div>
    <a class="btn btn-outline" href="{{ route('admin.laporan.unduh', request()->query()) }}">Unduh CSV</a>
</div>

<form method="GET" class="card filters" style="align-items:flex-end">
    <div><label class="label" for="dari">Mulai sewa dari</label><input id="dari" type="date" name="dari" value="{{ $filters['dari'] ?? '' }}"></div>
    <div><label class="label" for="sampai">Sampai</label><input id="sampai" type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}"></div>
    <div><label class="label" for="status">Status</label>
        <select id="status" name="status">
            <option value="">Semua status</option>
            @foreach (['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $k => $l)
                <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $l }}</option>
            @endforeach
        </select></div>
    <button class="btn">Terapkan</button>
    @if ($filters)<a href="{{ route('admin.laporan') }}" class="btn btn-outline">Reset</a>@endif
</form>
@if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<div class="stats">
    <div class="stat"><span class="stat__num">{{ $jumlah }}</span><span class="stat__lbl">Jumlah pesanan</span></div>
    <div class="stat"><span class="stat__num is-money">Rp{{ number_format($tagihan, 0, ',', '.') }}</span><span class="stat__lbl">Total tagihan</span></div>
    <div class="stat"><span class="stat__num is-money" style="color:var(--success)">Rp{{ number_format($diterima, 0, ',', '.') }}</span><span class="stat__lbl">Sudah diterima</span></div>
    <div class="stat"><span class="stat__num is-money" style="color:var(--warn)">Rp{{ number_format(max(0, $tagihan - $diterima), 0, ',', '.') }}</span><span class="stat__lbl">Belum dibayar</span></div>
</div>
<p class="muted small">Ringkasan tidak menghitung pesanan yang dibatalkan.</p>

<div class="table-wrap">
<table class="tbl">
    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Kendaraan</th><th>Mulai</th><th>Status</th><th>Pembayaran</th><th>Total</th><th>Diterima</th></tr></thead>
    <tbody>
    @forelse ($orders as $o)
        <tr>
            <td><b>{{ $o->code }}</b></td>
            <td>{{ $o->user->name }}</td>
            <td>{{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span></td>
            <td class="nowrap">{{ $o->start_at->format('d/m/Y H:i') }}</td>
            <td><x-order-status :status="$o->status" /></td>
            <td><x-payment-status :status="$o->payment_status" /></td>
            <td class="nowrap">Rp{{ number_format($o->grand_total, 0, ',', '.') }}</td>
            <td class="nowrap">Rp{{ number_format($o->paid_amount, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="8"><div class="empty"><strong>Tidak ada transaksi</strong>Tidak ada data pada filter ini.</div></td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $orders->links('pagination') }}
@endsection
