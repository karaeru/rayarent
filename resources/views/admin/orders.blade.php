@extends('app')
@section('title', 'Semua Pesanan · Admin '.config('rayarent.nama'))
@section('content')
<div class="page-head">
    <div><h2>Semua pesanan</h2><p class="page-sub">{{ $orders->total() }} pesanan{{ request()->hasAny(['search','status']) ? ' sesuai filter' : '' }}</p></div>
</div>

<form method="GET" class="card filters">
    <div class="grow"><input name="search" value="{{ request('search') }}" placeholder="Cari kode / nama pelanggan…" aria-label="Cari pesanan"></div>
    <select name="status" aria-label="Status pesanan">
        <option value="">Semua status</option>
        @foreach (['menunggu_konfirmasi' => 'Menunggu Konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang Disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $k => $l)
            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="btn">Filter</button>
    @if (request()->hasAny(['search','status']))
        <a href="{{ route('admin.pesanan') }}" class="btn btn-outline">Reset</a>
    @endif
</form>

<div class="table-wrap">
<table class="tbl">
    <thead>
        <tr><th>Kode</th><th>Pelanggan</th><th>Kendaraan</th><th>Periode</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Tindakan</th></tr>
    </thead>
    <tbody>
    @forelse ($orders as $o)
        <tr>
            <td><b>{{ $o->code }}</b><br><span class="muted small">{{ $o->created_at->format('d/m/Y') }}</span></td>
            <td>{{ $o->user->name }}<br><span class="muted small">{{ $o->user->phone ?? '-' }}</span></td>
            <td>{{ $o->unit->vehicle->name }}<br><span class="plate">{{ $o->unit->plate_number }}</span></td>
            <td class="nowrap small">{{ $o->start_at->format('d/m H:i') }}<br>{{ $o->end_at->format('d/m H:i') }}</td>
            <td class="nowrap">Rp{{ number_format($o->grand_total, 0, ',', '.') }}</td>
            <td><x-payment-status :status="$o->payment_status" /></td>
            <td><x-order-status :status="$o->status" /></td>
            <td style="min-width:200px">
                {{-- verifikasi bukti bayar yang menunggu --}}
                @foreach ($o->payments->where('status', 'menunggu') as $p)
                    <div class="small" style="margin-bottom:6px">
                        <b>Rp{{ number_format($p->amount, 0, ',', '.') }}</b>
                        @if ($p->proof_path) · <a href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">bukti</a> @endif
                        <form class="inline-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="diterima"><button class="btn btn-success btn-sm">Terima</button></form>
                        <form class="inline-form" method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" onsubmit="return confirm('Tolak pembayaran ini?')">@csrf @method('PATCH')<input type="hidden" name="action" value="ditolak"><button class="btn btn-danger-outline btn-sm">Tolak</button></form>
                    </div>
                @endforeach

                @if ($o->status === 'menunggu_konfirmasi')
                    <form class="inline-form" method="POST" action="{{ route('admin.pesanan.konfirmasi', $o) }}">@csrf @method('PATCH')
                        <button class="btn btn-sm" @disabled($o->paid_amount <= 0) title="{{ $o->paid_amount <= 0 ? 'Verifikasi pembayaran dulu' : '' }}">Konfirmasi</button></form>
                @elseif ($o->status === 'dikonfirmasi')
                    <form class="inline-form" method="POST" action="{{ route('admin.pesanan.mulai', $o) }}">@csrf @method('PATCH')
                        <button class="btn btn-sm">Serah terima</button></form>
                @elseif ($o->status === 'sedang_disewa' && ! $o->returned_at)
                    <details>
                        <summary class="btn btn-sm" style="display:inline-flex;list-style:none;cursor:pointer">Catat pengembalian</summary>
                        <form method="POST" action="{{ route('admin.pesanan.pengembalian', $o) }}" class="summary" style="margin-top:8px">@csrf
                            <div class="field"><label>Waktu kembali (batas {{ $o->end_at->format('d/m H:i') }})</label>
                                <input type="datetime-local" name="returned_at" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required></div>
                            <div class="field"><label>Kondisi kendaraan</label>
                                <input name="return_condition" maxlength="200" placeholder="Baik / ada baret…"></div>
                            <div class="field"><label>Biaya kerusakan (Rp)</label>
                                <input type="number" name="damage_fee" min="0" value="0"></div>
                            <button class="btn btn-sm">Simpan pengembalian</button>
                        </form>
                    </details>
                @elseif ($o->status === 'sedang_disewa' && $o->returned_at)
                    <div class="muted small">Kembali {{ $o->returned_at->format('d/m H:i') }}<br>Denda Rp{{ number_format($o->late_fee, 0, ',', '.') }} · kerusakan Rp{{ number_format($o->damage_fee, 0, ',', '.') }}</div>
                @endif

                @if ($o->status === 'sedang_disewa' && $o->returned_at && $o->due_amount > 0)
                    <form class="inline-form" method="POST" action="{{ route('admin.pesanan.tunai', $o) }}" onsubmit="return confirm('Catat pelunasan tunai Rp{{ number_format($o->due_amount, 0, ',', '.') }}?')">@csrf
                        <button class="btn btn-outline btn-sm">Lunasi tunai</button></form>
                @endif

                @if (in_array($o->status, ['menunggu_konfirmasi', 'dikonfirmasi'], true) && $o->paid_amount <= 0)
                    <form class="inline-form" method="POST" action="{{ route('admin.pesanan.batal', $o) }}" onsubmit="return confirm('Batalkan pesanan ini?')">@csrf @method('PATCH')
                        <button class="btn btn-danger-outline btn-sm">Batalkan</button></form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="8"><div class="empty"><strong>Tidak ada pesanan ditemukan</strong>Ubah kata kunci atau filter.</div></td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{ $orders->links('pagination') }}
@endsection
