@extends('app')
@section('title', 'Kendaraan · Admin RAYARENT')
@section('content')
<style>
    .tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tbl th{text-align:left;color:var(--mu);font-weight:600;padding:8px;border-bottom:1px solid var(--ln)}
    .tbl td{padding:8px;border-bottom:1px solid var(--ln);vertical-align:top}
    .pill{display:inline-block;padding:2px 9px;border-radius:20px;font-size:12px;font-weight:700;background:var(--ln);color:var(--mu)}
    .pill.on{background:#dcfce7;color:#166534}
</style>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
    <h2 style="margin:0">Kendaraan dan unit</h2>
    <a class="btn y" style="text-decoration:none" href="{{ route('admin.kendaraan.baru') }}">+ Tambah kendaraan</a>
</div>
<div style="background:var(--sf);border:1px solid var(--ln);border-radius:12px;overflow-x:auto">
<table class="tbl">
    <thead><tr><th>Kendaraan</th><th>Kursi</th><th>Harga mulai</th><th>Unit</th><th>Tampil di katalog</th><th></th></tr></thead>
    <tbody>
    @foreach ($vehicles as $v)
        @php $u = $v->units->countBy('status'); @endphp
        <tr>
            <td><b>{{ $v->name }}</b><br><span style="font-size:12px;color:var(--mu)">{{ $v->transmission ?? '-' }}</span></td>
            <td>{{ $v->seat_label }}</td>
            <td>@if ($v->packages->isNotEmpty()) Rp{{ number_format($v->min_price, 0, ',', '.') }} @else <span style="color:var(--rd)">belum ada paket</span> @endif</td>
            <td>{{ $v->units->count() }}
                <span style="font-size:12px;color:var(--mu)">({{ $u['tersedia'] ?? 0 }} tersedia, {{ $u['disewa'] ?? 0 }} disewa, {{ $u['perawatan'] ?? 0 }} perawatan)</span>
                @if ($v->units->isEmpty()) <br><span style="color:var(--rd);font-size:12px">belum ada unit</span> @endif</td>
            <td><span class="pill {{ $v->is_active ? 'on' : '' }}">{{ $v->is_active ? 'Aktif' : 'Disembunyikan' }}</span></td>
            <td><a href="{{ route('admin.kendaraan.ubah', $v) }}">Kelola</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
