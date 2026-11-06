@extends('app')
@section('title', 'Pesanan '.$order->code)
@section('content')
<style>
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700}
    .badge-yellow{background:#fef9c3;color:#854d0e}
    .badge-green{background:#dcfce7;color:#166534}
    .badge-red{background:#fee2e2;color:#991b1b}
    .badge-blue{background:#dbeafe;color:#1e40af}
    .badge-gray{background:var(--ln);color:var(--mu)}
    .payment-row{background:var(--sf);border:1px solid var(--ln);border-radius:8px;padding:12px;margin-bottom:8px}
    .upload-box{border:2px dashed var(--ln);border-radius:10px;padding:20px;text-align:center;margin-top:6px}
    .upload-box input[type=file]{margin-top:8px}
    td{padding:5px 0}td:first-child{color:var(--mu);width:140px;font-size:14px}
</style>

<div style="max-width:580px;margin:0 auto">

{{-- Header pesanan --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;flex-wrap:wrap">
    <h2 style="margin:0">{{ $order->code }}</h2>
    @php
        $badges = [
            'menunggu_konfirmasi' => ['yellow', 'Menunggu Konfirmasi'],
            'dikonfirmasi'        => ['blue',   'Dikonfirmasi'],
            'sedang_disewa'       => ['green',  'Sedang Disewa'],
            'selesai'             => ['green',  'Selesai'],
            'dibatalkan'          => ['red',    'Dibatalkan'],
        ];
        [$bc, $bl] = $badges[$order->status] ?? ['gray', $order->status];
    @endphp
    <span class="badge badge-{{ $bc }}">{{ $bl }}</span>
</div>
<p style="color:var(--mu);margin:0 0 16px">
    {{ $order->unit->vehicle->name }}
    <span class="plate">{{ $order->unit->plate_number }}</span>
</p>

{{-- Detail pesanan --}}
<div style="background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:20px;margin-bottom:16px">
    <table style="width:100%;border-collapse:collapse">
        @foreach ([
            'Paket'           => $order->package->service_label.' · '.$order->package->duration_hours.' jam × '.$order->quantity,
            'Mulai'           => $order->start_at->translatedFormat('d M Y H:i'),
            'Selesai'         => $order->end_at->translatedFormat('d M Y H:i'),
            'Lokasi'          => $order->pickup_location ?: '-',
            'Total tagihan'   => 'Rp'.number_format($order->grand_total, 0, ',', '.'),
            'Sudah dibayar'   => 'Rp'.number_format($order->paid_amount, 0, ',', '.'),
            'Sisa tagihan'    => 'Rp'.number_format($order->due_amount, 0, ',', '.'),
        ] as $k => $v)
            <tr><td>{{ $k }}</td><td><b>{{ $v }}</b></td></tr>
        @endforeach
        <tr>
            <td>Status bayar</td>
            <td>
                @php
                    $ps = $order->payment_status;
                    $pbc = match($ps) {
                        'Lunas'               => 'green',
                        'DP Dibayar'          => 'blue',
                        'Menunggu Verifikasi' => 'yellow',
                        'Ditolak'             => 'red',
                        default               => 'gray',
                    };
                @endphp
                <span class="badge badge-{{ $pbc }}">{{ $ps }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- Riwayat pembayaran --}}
@if ($order->payments->isNotEmpty())
<h3 style="margin:0 0 8px">Riwayat Pembayaran</h3>
@foreach ($order->payments as $p)
<div class="payment-row">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px">
        <div>
            <b>Rp{{ number_format($p->amount, 0, ',', '.') }}</b>
            <span style="font-size:13px;color:var(--mu)"> · {{ $p->created_at->translatedFormat('d M Y H:i') }}</span>
        </div>
        @php
            $pst = match($p->status) {
                'diterima' => ['green', 'Diterima'],
                'ditolak'  => ['red',   'Ditolak'],
                default    => ['yellow','Menunggu Verifikasi'],
            };
        @endphp
        <span class="badge badge-{{ $pst[0] }}">{{ $pst[1] }}</span>
    </div>
    @if ($p->proof_path)
    <div style="margin-top:8px">
        <a href="{{ route('bukti', $p) }}" target="_blank"
           style="font-size:13px;color:var(--mu)">📎 Lihat bukti transfer</a>
    </div>
    @endif
</div>
@endforeach
@endif

{{-- Form upload bukti bayar --}}
@if ($order->due_amount > 0 && $order->status !== 'dibatalkan')
<h3 style="margin:16px 0 8px">Upload Bukti Transfer</h3>
<div style="background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:20px">
    <p style="margin:0 0 4px;color:var(--mu);font-size:13px">
        Sisa tagihan: <b style="color:var(--tx)">Rp{{ number_format($order->due_amount, 0, ',', '.') }}</b>
        @if ($order->pay_mode === 'dp')
            · Mode DP aktif
        @endif
    </p>
    @if ($order->payments->where('status', 'menunggu')->isNotEmpty())
    <p style="color:#854d0e;font-size:13px;background:#fef9c3;padding:8px 12px;border-radius:8px">
        ⏳ Ada bukti pembayaran yang sedang menunggu verifikasi admin.
        Kamu tetap bisa kirim bukti baru jika perlu.
    </p>
    @endif
    <form method="POST" action="{{ route('pelanggan.pesanan.bayar', $order) }}" enctype="multipart/form-data">
        @csrf
        <label>Jumlah yang ditransfer (Rp)</label>
        <input type="number" name="amount" min="1" max="{{ $order->due_amount }}"
               value="{{ old('amount', $order->due_amount) }}" required
               placeholder="Contoh: {{ $order->due_amount }}">
        @error('amount') <p class="err">{{ $message }}</p> @enderror

        <label style="margin-top:12px;display:block">Bukti Transfer (JPG/PNG/PDF, maks 2 MB)</label>
        <div class="upload-box">
            <div style="font-size:28px">📤</div>
            <div style="font-size:13px;color:var(--mu)">Upload foto/screenshot bukti transfer</div>
            <input type="file" name="proof" accept="image/jpeg,image/png,application/pdf" required>
        </div>
        @error('proof') <p class="err">{{ $message }}</p> @enderror

        <button class="btn y" style="margin-top:14px;width:100%">Kirim Bukti Pembayaran</button>
    </form>
</div>
@endif

{{-- Tombol batalkan pesanan --}}
@if ($order->status === 'menunggu_konfirmasi')
<div style="margin-top:16px;border-top:1px solid var(--ln);padding-top:16px">
    <form method="POST" action="{{ route('pelanggan.pesanan.batal', $order) }}">
        @csrf
        @method('DELETE')
        <button class="btn" style="color:var(--rd);border:1px solid var(--rd);background:transparent"
                onclick="return confirm('Yakin ingin membatalkan pesanan {{ $order->code }}?')">
            ✕ Batalkan Pesanan
        </button>
    </form>
</div>
@endif

<p style="margin-top:20px"><a href="{{ route('pelanggan.pesanan') }}">← Semua pesanan</a></p>

</div>
@endsection