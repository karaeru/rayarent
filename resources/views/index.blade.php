@extends('app')
@section('title', 'Pesanan saya · RAYARENT')
@section('content')
<h2>Pesanan saya</h2>
@forelse ($orders as $o)
    <a href="{{ route('pelanggan.pesanan.show', $o) }}" style="display:block;text-decoration:none;color:inherit;background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:14px;margin-bottom:10px">
        <b>{{ $o->code }}</b> · {{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span>
        <div style="color:var(--mu);font-size:13px">
            {{ $o->package->service_label }} · {{ $o->start_at->translatedFormat('d M Y H:i') }} → {{ $o->end_at->translatedFormat('d M Y H:i') }}
        </div>
        <div>Rp{{ number_format($o->grand_total, 0, ',', '.') }} · {{ str_replace('_', ' ', $o->status) }} · {{ $o->payment_status }}</div>
    </a>
@empty
    <p style="color:var(--mu)">Belum ada pesanan. <a href="{{ route('pelanggan.katalog') }}">Lihat katalog</a>.</p>
@endforelse
@endsection
