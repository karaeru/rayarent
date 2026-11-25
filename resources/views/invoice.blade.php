@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $nama = config('rayarent.nama');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
    $lunas = $order->due_amount === 0;
    $diterima = $order->payments->where('status', 'diterima')->sortBy('verified_at');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Invoice-{{ $order->code }}</title>
    <style>
        @page{size:A4;margin:16mm}
        *{box-sizing:border-box}
        body{margin:0;background:#eef1f5;color:#111827;font:14px/1.5 Inter,system-ui,"Segoe UI",Roboto,Arial,sans-serif}
        .bar{position:sticky;top:0;display:flex;justify-content:center;gap:8px;padding:12px;background:#111827}
        .bar button,.bar a{font:600 14px inherit;padding:9px 18px;border-radius:8px;border:0;cursor:pointer;text-decoration:none}
        .bar button{background:#fff;color:#111827}
        .bar a{color:#cbd5e1}
        .sheet{width:210mm;max-width:100%;min-height:297mm;margin:24px auto;padding:18mm;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.08)}
        header{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;padding-bottom:20px;border-bottom:2px solid #111827}
        .brand{display:flex;align-items:center;gap:12px}
        .brand img{height:44px}
        .brand b{font-size:20px;letter-spacing:.02em}
        .brand small{display:block;color:#6b7280;font-size:12px}
        h1{margin:0;font-size:28px;letter-spacing:.08em;text-align:right}
        .meta{text-align:right;color:#374151;font-size:13px}
        .stamp{display:inline-block;margin-top:8px;padding:4px 12px;border:2px solid;border-radius:6px;font-weight:800;letter-spacing:.1em;font-size:12px;transform:rotate(-3deg)}
        .stamp.paid{color:#166534}.stamp.due{color:#b91c1c}
        .cols{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin:24px 0}
        .lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:4px}
        table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
        th,td{padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}
        thead th{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #111827}
        .r{text-align:right;white-space:nowrap}
        .plate{display:inline-block;border:1.5px solid #111;border-radius:4px;padding:0 6px;font-size:11px;font-weight:800;background:#f4c20d}
        .tot{width:320px;margin-left:auto;margin-top:12px}
        .tot td{border:0;padding:5px 8px}
        .tot .grand td{border-top:2px solid #111827;font-size:16px;font-weight:800;padding-top:10px}
        .muted{color:#6b7280}
        footer{margin-top:40px;padding-top:16px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;display:flex;justify-content:space-between;gap:24px}
        .rek-inv{width:auto;margin:4px 0 8px}
        .rek-inv td{padding:2px 16px 2px 0;border:0;color:#111827;font-size:12px}
        @media print{
            body{background:#fff}
            .bar{display:none}
            .sheet{width:auto;min-height:0;margin:0;padding:0;box-shadow:none}
            *{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        }
        @media(max-width:640px){.sheet{padding:20px;margin:0}.cols{grid-template-columns:1fr}.tot{width:100%}header{flex-direction:column}h1,.meta{text-align:left}}
    </style>
</head>
<body>
<div class="bar">
    <button type="button" onclick="window.print()">Simpan sebagai PDF / Cetak</button>
    <a href="{{ auth()->user()->isAdmin() ? route('admin.pesanan') : route('pelanggan.pesanan.show', $order) }}">Kembali</a>
</div>

<main class="sheet">
    <header>
        <div class="brand">
            @if ($logo) <img src="{{ asset('images/logo.'.$logo) }}" alt=""> @endif
            <div>
                <b>{{ $nama }}</b>
                <small>{{ config('rayarent.alamat') }}</small>
                <small>{{ collect([$wa ? '+'.$wa : null, config('rayarent.email')])->filter()->implode(' · ') }}</small>
            </div>
        </div>
        <div>
            <h1>INVOICE</h1>
            <div class="meta">
                No. <b>INV/{{ $order->created_at->format('Y/m') }}/{{ $order->code }}</b><br>
                Tanggal {{ $order->created_at->translatedFormat('d F Y') }}
            </div>
            <div style="text-align:right"><span class="stamp {{ $lunas ? 'paid' : 'due' }}">{{ $lunas ? 'LUNAS' : 'BELUM LUNAS' }}</span></div>
        </div>
    </header>

    <div class="cols">
        <div>
            <div class="lbl">Ditagihkan kepada</div>
            <b>{{ $order->user->name }}</b><br>
            {{ $order->user->phone }}<br>
            <span class="muted">{{ $order->user->email }}</span>
        </div>
        <div>
            <div class="lbl">Periode sewa</div>
            {{ $order->start_at->translatedFormat('d M Y, H:i') }} – {{ $order->end_at->translatedFormat('d M Y, H:i') }}<br>
            @if ($order->pickup_location) <span class="muted">Lokasi: {{ $order->pickup_location }}</span><br> @endif
            @if ($order->returned_at) <span class="muted">Dikembalikan: {{ $order->returned_at->translatedFormat('d M Y, H:i') }}</span> @endif
        </div>
    </div>

    <table>
        <thead><tr><th>Keterangan</th><th class="r">Jumlah</th><th class="r">Harga</th><th class="r">Subtotal</th></tr></thead>
        <tbody>
            <tr>
                <td>
                    <b>Sewa {{ $order->unit->vehicle->name }}</b> <span class="plate">{{ $order->unit->plate_number }}</span><br>
                    <span class="muted">{{ $order->package->service_label }} · paket {{ $order->package->duration_hours }} jam</span>
                </td>
                <td class="r">{{ $order->quantity }}×</td>
                <td class="r">{{ $rp(intdiv($order->total_price, max(1, $order->quantity))) }}</td>
                <td class="r">{{ $rp($order->total_price) }}</td>
            </tr>
            @if ($order->late_fee)
                <tr><td>Denda keterlambatan</td><td class="r">1×</td><td class="r">{{ $rp($order->late_fee) }}</td><td class="r">{{ $rp($order->late_fee) }}</td></tr>
            @endif
            @if ($order->damage_fee)
                <tr><td>Biaya kerusakan{!! $order->return_condition ? '<br><span class="muted">'.e($order->return_condition).'</span>' : '' !!}</td><td class="r">1×</td><td class="r">{{ $rp($order->damage_fee) }}</td><td class="r">{{ $rp($order->damage_fee) }}</td></tr>
            @endif
        </tbody>
    </table>

    <table class="tot">
        <tr><td>Total tagihan</td><td class="r">{{ $rp($order->grand_total) }}</td></tr>
        @foreach ($diterima as $p)
            <tr class="muted"><td>Dibayar {{ $p->proof_path ? 'transfer'.($p->bank ? ' '.$p->bank : '') : 'tunai' }} · {{ $p->verified_at?->translatedFormat('d M Y') }}</td><td class="r">−{{ $rp($p->amount) }}</td></tr>
        @endforeach
        <tr class="grand"><td>Sisa tagihan</td><td class="r">{{ $rp($order->due_amount) }}</td></tr>
    </table>

    <footer>
        <div>
            @if (! $lunas && ! empty(config('rayarent.rekening')))
                <div class="lbl">Pembayaran ke</div>
                <table class="rek-inv">
                    @foreach (config('rayarent.rekening') as $r)
                        <tr><td><b>{{ $r['bank'] }}</b></td><td>{{ trim(chunk_split($r['nomor'], 4, ' ')) }}</td><td>a.n. {{ $r['nama'] }}</td></tr>
                    @endforeach
                </table>
                Cantumkan kode <b style="color:#111827">{{ $order->code }}</b> di berita transfer.
            @else
                Terima kasih telah menyewa di {{ $nama }}.
            @endif
        </div>
        <div style="text-align:right">
            Dokumen ini dibuat otomatis oleh sistem<br>dan sah tanpa tanda tangan.
            @if ($order->terms_accepted_at)
                <br>Penyewa menyetujui Syarat &amp; Ketentuan versi {{ $order->terms_version }}<br>pada {{ $order->terms_accepted_at->translatedFormat('d M Y H:i') }}.
            @endif
        </div>
    </footer>
</main>
</body>
</html>
