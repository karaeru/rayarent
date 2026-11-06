@extends('app')
@section('title', 'Semua Pesanan · Admin RAYARENT')
@section('content')
<style>
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700}
    .badge-yellow{background:#fef9c3;color:#854d0e}
    .badge-green{background:#dcfce7;color:#166534}
    .badge-red{background:#fee2e2;color:#991b1b}
    .badge-blue{background:#dbeafe;color:#1e40af}
    .badge-gray{background:var(--ln);color:var(--mu)}
    .tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tbl th{text-align:left;color:var(--mu);font-weight:600;padding:6px 8px;border-bottom:1px solid var(--ln)}
    .tbl td{padding:8px;border-bottom:1px solid var(--ln);vertical-align:top}
    select.sm{padding:5px 8px;border:1px solid var(--ln);border-radius:6px;font:inherit;background:var(--sf);color:var(--tx);font-size:13px}
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
    <h2 style="margin:0">Semua Pesanan</h2>
    <a href="{{ route('admin.dashboard') }}" style="font-size:13px;color:var(--mu)">← Dashboard</a>
</div>

{{-- Filter --}}
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
    <input name="search" value="{{ request('search') }}" placeholder="Cari kode / nama pelanggan…"
           style="padding:8px 10px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit;flex:1;min-width:200px">
    <select name="status" class="sm" style="min-width:160px">
        <option value="">Semua status</option>
        <option value="menunggu_konfirmasi" @selected(request('status')==='menunggu_konfirmasi')>Menunggu Konfirmasi</option>
        <option value="dikonfirmasi"        @selected(request('status')==='dikonfirmasi')>Dikonfirmasi</option>
        <option value="sedang_disewa"       @selected(request('status')==='sedang_disewa')>Sedang Disewa</option>
        <option value="selesai"             @selected(request('status')==='selesai')>Selesai</option>
        <option value="dibatalkan"          @selected(request('status')==='dibatalkan')>Dibatalkan</option>
    </select>
    <button class="btn y">Filter</button>
    @if (request()->hasAny(['search','status']))
        <a href="{{ route('admin.pesanan') }}" class="btn">Reset</a>
    @endif
</form>

<div style="background:var(--sf);border:1px solid var(--ln);border-radius:12px;overflow:hidden">
<table class="tbl">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Pelanggan</th>
            <th>Kendaraan</th>
            <th>Periode</th>
            <th>Total</th>
            <th>Bayar</th>
            <th>Status</th>
            <th>Ubah</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($orders as $o)
        @php
            $badges = [
                'menunggu_konfirmasi' => ['yellow', 'Menunggu'],
                'dikonfirmasi'        => ['blue',   'Dikonfirmasi'],
                'sedang_disewa'       => ['green',  'Disewa'],
                'selesai'             => ['green',  'Selesai'],
                'dibatalkan'          => ['red',    'Dibatalkan'],
            ];
            [$bc, $bl] = $badges[$o->status] ?? ['gray', $o->status];
            $ps = $o->payment_status;
            $pbc = match($ps) {
                'Lunas'               => 'green',
                'DP Dibayar'          => 'blue',
                'Menunggu Verifikasi' => 'yellow',
                'Ditolak'             => 'red',
                default               => 'gray',
            };
        @endphp
        <tr>
            <td><b>{{ $o->code }}</b><br><span style="font-size:11px;color:var(--mu)">{{ $o->created_at->format('d/m/Y') }}</span></td>
            <td>{{ $o->user->name }}<br><span style="font-size:12px;color:var(--mu)">{{ $o->user->phone ?? '-' }}</span></td>
            <td>{{ $o->unit->vehicle->name }}<br><span class="plate" style="font-size:11px">{{ $o->unit->plate_number }}</span></td>
            <td style="font-size:13px;white-space:nowrap">
                {{ $o->start_at->format('d/m H:i') }}<br>{{ $o->end_at->format('d/m H:i') }}
            </td>
            <td>Rp{{ number_format($o->grand_total, 0, ',', '.') }}</td>
            <td><span class="badge badge-{{ $pbc }}" style="font-size:11px">{{ $ps }}</span></td>
            <td><span class="badge badge-{{ $bc }}">{{ $bl }}</span></td>
            <td style="min-width:170px">
                @php $btn = 'font-size:12px;padding:4px 9px;margin:0 4px 4px 0'; @endphp
                @if ($o->status === 'menunggu_konfirmasi')
                    <form method="POST" action="{{ route('admin.pesanan.konfirmasi', $o) }}" style="display:inline">@csrf @method('PATCH')
                        <button class="btn y" style="{{ $btn }}" @disabled($o->paid_amount <= 0) title="{{ $o->paid_amount <= 0 ? 'Verifikasi pembayaran dulu' : '' }}">Konfirmasi</button></form>
                @elseif ($o->status === 'dikonfirmasi')
                    <form method="POST" action="{{ route('admin.pesanan.mulai', $o) }}" style="display:inline">@csrf @method('PATCH')
                        <button class="btn y" style="{{ $btn }}">Serah terima</button></form>
                @elseif ($o->status === 'sedang_disewa' && ! $o->returned_at)
                    <details>
                        <summary class="btn y" style="{{ $btn }};display:inline-block;cursor:pointer">Catat pengembalian</summary>
                        <form method="POST" action="{{ route('admin.pesanan.pengembalian', $o) }}" style="margin-top:6px">@csrf
                            <label style="font-size:12px">Waktu kembali (batas {{ $o->end_at->format('d/m H:i') }})</label>
                            <input type="datetime-local" name="returned_at" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required style="width:100%;padding:5px">
                            <label style="font-size:12px">Kondisi</label>
                            <input name="return_condition" maxlength="200" placeholder="Baik / ada baret…" style="width:100%;padding:5px">
                            <label style="font-size:12px">Biaya kerusakan (Rp)</label>
                            <input type="number" name="damage_fee" min="0" value="0" style="width:100%;padding:5px">
                            <button class="btn" style="margin-top:6px;{{ $btn }}">Simpan</button>
                        </form>
                    </details>
                @elseif ($o->status === 'sedang_disewa' && $o->returned_at)
                    <div style="font-size:12px;color:var(--mu)">Kembali {{ $o->returned_at->format('d/m H:i') }}<br>Denda Rp{{ number_format($o->late_fee, 0, ',', '.') }} · kerusakan Rp{{ number_format($o->damage_fee, 0, ',', '.') }}</div>
                @endif

                @if (! in_array($o->status, ['dibatalkan', 'selesai'], true) && $o->due_amount > 0 && ($o->status === 'sedang_disewa' && $o->returned_at))
                    <form method="POST" action="{{ route('admin.pesanan.tunai', $o) }}" style="display:inline" onsubmit="return confirm('Catat pelunasan tunai Rp{{ number_format($o->due_amount, 0, ',', '.') }}?')">@csrf
                        <button class="btn" style="{{ $btn }}">Lunasi tunai</button></form>
                @endif

                @if (in_array($o->status, ['menunggu_konfirmasi', 'dikonfirmasi'], true) && $o->paid_amount <= 0)
                    <form method="POST" action="{{ route('admin.pesanan.batal', $o) }}" style="display:inline" onsubmit="return confirm('Batalkan pesanan ini?')">@csrf @method('PATCH')
                        <button class="btn" style="{{ $btn }};background:var(--rd);color:#fff">Batalkan</button></form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="8" style="color:var(--mu);text-align:center;padding:24px">Tidak ada pesanan ditemukan.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- Pagination --}}
@if ($orders->hasPages())
<div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">
    {{ $orders->links() }}
</div>
@endif

@endsection