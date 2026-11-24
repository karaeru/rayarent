@extends('app')
@section('title', 'Kendaraan · Admin '.config('rayarent.nama'))
@section('content')
<div class="page-head">
    <div><h2>Kendaraan dan unit</h2><p class="page-sub">{{ $vehicles->count() }} kendaraan terdaftar.</p></div>
    <a class="btn" href="{{ route('admin.kendaraan.baru') }}">+ Tambah kendaraan</a>
</div>

<div class="table-wrap">
<table class="tbl">
    <thead><tr><th>Kendaraan</th><th>Kursi</th><th>Harga mulai</th><th>Unit</th><th>Katalog</th><th></th></tr></thead>
    <tbody>
    @foreach ($vehicles as $v)
        @php $u = $v->units->countBy('status'); @endphp
        <tr>
            <td>
                <div class="row" style="flex-wrap:nowrap">
                    @if ($v->photo_url)
                        <img src="{{ $v->photo_url }}" alt="" loading="lazy" style="width:64px;height:48px;object-fit:cover;border-radius:6px;border:1px solid var(--line);flex:none">
                    @else
                        <span title="Belum ada foto" style="width:64px;height:48px;border-radius:6px;border:1px dashed var(--line-strong);flex:none"></span>
                    @endif
                    <span><b>{{ $v->name }}</b><br><span class="muted small">{{ $v->transmission ?? '-' }}</span></span>
                </div>
            </td>
            <td>{{ $v->seat_label }}</td>
            <td class="nowrap">@if ($v->packages->isNotEmpty()) Rp{{ number_format($v->min_price, 0, ',', '.') }} @else <span class="badge badge-danger">belum ada paket</span> @endif</td>
            <td>
                <b>{{ $v->units->count() }}</b>
                <span class="muted small">({{ $u['tersedia'] ?? 0 }} tersedia, {{ $u['disewa'] ?? 0 }} disewa, {{ $u['perawatan'] ?? 0 }} perawatan)</span>
                @if ($v->units->isEmpty()) <br><span class="badge badge-danger">belum ada unit</span> @endif
            </td>
            <td><span class="badge {{ $v->is_active ? 'badge-success' : 'badge-muted' }}">{{ $v->is_active ? 'Aktif' : 'Disembunyikan' }}</span></td>
            <td><a class="btn btn-outline btn-sm" href="{{ route('admin.kendaraan.ubah', $v) }}">Kelola</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
