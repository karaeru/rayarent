@extends('app')
@section('title', 'Pesanan saya · '.config('rayarent.nama'))
@section('content')
<div class="page-head"><div><h2>Pesanan saya</h2><p class="page-sub">Riwayat dan status semua pesanan Anda.</p></div></div>

@forelse ($orders as $o)
    <a href="{{ route('pelanggan.pesanan.show', $o) }}" class="card" style="display:block;color:inherit;margin-bottom:12px;text-decoration:none">
        <div class="card-body row between" style="gap:16px">
            <div class="row" style="gap:14px;flex-wrap:nowrap">
                <div style="width:84px;height:63px;flex:none;border-radius:8px;overflow:hidden;background:var(--brand-soft)">
                    @if ($o->unit->vehicle->photo_url)
                        <img src="{{ $o->unit->vehicle->photo_url }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block">
                    @else
                        <span class="media-placeholder" ><x-car-icon :type="$o->unit->vehicle->icon" /></span>
                    @endif
                </div>
                <div>
                    <b>{{ $o->code }}</b> · {{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span>
                    <div class="muted small">{{ $o->package->service_label }} · {{ $o->start_at->translatedFormat('d M Y H:i') }} → {{ $o->end_at->translatedFormat('d M Y H:i') }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="price">Rp{{ number_format($o->grand_total, 0, ',', '.') }}</div>
                <div class="row" style="justify-content:flex-end;margin-top:4px;gap:6px">
                    <x-order-status :status="$o->status" />
                    <x-payment-status :status="$o->payment_status" />
                </div>
            </div>
        </div>
    </a>
@empty
    <div class="card empty"><strong>Belum ada pesanan</strong><a href="{{ route('pelanggan.katalog') }}">Lihat katalog kendaraan</a></div>
@endforelse
@endsection
