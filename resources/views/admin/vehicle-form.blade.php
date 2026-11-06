@extends('app')
@section('title', ($vehicle->exists ? 'Kelola ' . $vehicle->name : 'Kendaraan baru') . ' · Admin RAYARENT')
@section('content')
<style>
    .box{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:18px;margin-bottom:16px}
    .tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tbl td,.tbl th{padding:7px 6px;border-bottom:1px solid var(--ln);text-align:left;vertical-align:middle}
    .g2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .sm{padding:6px 8px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit;font-size:13px}
    .btn.s{font-size:12px;padding:5px 10px}
    .inl{display:inline}
    input[type=checkbox]{width:auto}
</style>

<p><a href="{{ route('admin.kendaraan') }}" style="font-size:13px;color:var(--mu)">← Semua kendaraan</a></p>
<h2 style="margin:0 0 12px">{{ $vehicle->exists ? $vehicle->name : 'Tambah kendaraan' }}</h2>

@if ($errors->any())
    <div class="ok" style="background:#c8372d22;color:var(--rd)">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
@endif

<form method="POST" class="box" action="{{ $vehicle->exists ? route('admin.kendaraan.perbarui', $vehicle) : route('admin.kendaraan.simpan') }}">
    @csrf @if ($vehicle->exists) @method('PUT') @endif
    <label for="name">Nama kendaraan</label>
    <input id="name" name="name" value="{{ old('name', $vehicle->name) }}" maxlength="100" required>
    <div class="g2">
        <div><label for="seat_label">Label kursi (contoh: 7 atau 3-4)</label>
            <input id="seat_label" name="seat_label" value="{{ old('seat_label', $vehicle->seat_label) }}" required></div>
        <div><label for="capacity">Kapasitas maksimum (untuk filter)</label>
            <input id="capacity" type="number" name="capacity" min="1" max="60" value="{{ old('capacity', $vehicle->capacity) }}" required></div>
    </div>
    <label for="transmission">Transmisi</label>
    <select id="transmission" name="transmission" class="sm" style="width:100%;padding:9px">
        <option value="">Tidak ditentukan</option>
        @foreach (['Matic', 'Manual'] as $t)
            <option @selected(old('transmission', $vehicle->transmission) === $t)>{{ $t }}</option>
        @endforeach
    </select>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400;margin-top:14px">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active))> Tampilkan di katalog
    </label>
    <p><button class="btn y">{{ $vehicle->exists ? 'Simpan' : 'Buat kendaraan' }}</button></p>
</form>

@if ($vehicle->exists)
<div class="box">
    <h3 style="margin:0 0 4px">Paket harga</h3>
    <p style="margin:0 0 10px;color:var(--mu);font-size:13px">Harga baru hanya berlaku untuk pesanan berikutnya.</p>
    <table class="tbl">
        @forelse ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
            <tr>
                <td>{{ $p->service_label }}</td>
                <td>{{ $p->duration_hours }} jam</td>
                <td>
                    <form method="POST" action="{{ route('admin.paket.perbarui', $p) }}" style="display:flex;gap:6px">
                        @csrf @method('PUT')
                        <input type="number" name="price" value="{{ $p->price }}" min="10000" step="1000" class="sm" style="width:130px">
                        <button class="btn s">Simpan</button>
                    </form>
                </td>
                <td>
                    @unless (in_array($p->id, $usedPackageIds, true))
                        <form method="POST" action="{{ route('admin.paket.hapus', $p) }}" class="inl" onsubmit="return confirm('Hapus paket ini?')">
                            @csrf @method('DELETE') <button class="btn s" style="background:var(--rd);color:#fff">Hapus</button>
                        </form>
                    @else
                        <span style="font-size:12px;color:var(--mu)">sudah pernah dipesan</span>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td style="color:var(--mu)">Belum ada paket. Tanpa paket, kendaraan tidak bisa dipesan.</td></tr>
        @endforelse
    </table>
    <form method="POST" action="{{ route('admin.paket.simpan', $vehicle) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;align-items:center">
        @csrf
        <select name="service" class="sm">@foreach ($services as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select>
        <select name="duration_hours" class="sm"><option value="24">24 jam</option><option value="12">12 jam</option></select>
        <input type="number" name="price" min="10000" step="1000" placeholder="Harga (Rp)" class="sm" style="width:150px" required>
        <button class="btn y s">+ Tambah paket</button>
    </form>
</div>

<div class="box">
    <h3 style="margin:0 0 10px">Unit</h3>
    <table class="tbl">
        @forelse ($vehicle->units as $u)
            <tr>
                <td><span class="plate">{{ $u->plate_number }}</span></td>
                <td>
                    @if ($u->status === 'disewa')
                        <span class="sm" style="border:0">Sedang disewa</span>
                    @else
                        <form method="POST" action="{{ route('admin.unit.status', $u) }}">
                            @csrf @method('PATCH')
                            <select name="status" class="sm" onchange="this.form.submit()">
                                <option value="tersedia" @selected($u->status === 'tersedia')>Tersedia</option>
                                <option value="perawatan" @selected($u->status === 'perawatan')>Perawatan</option>
                            </select>
                        </form>
                    @endif
                </td>
                <td style="font-size:12px;color:var(--mu)">{{ $u->orders_count }} pesanan</td>
                <td>
                    @if ($u->orders_count === 0)
                        <form method="POST" action="{{ route('admin.unit.hapus', $u) }}" class="inl" onsubmit="return confirm('Hapus unit {{ $u->plate_number }}?')">
                            @csrf @method('DELETE') <button class="btn s" style="background:var(--rd);color:#fff">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td style="color:var(--mu)">Belum ada unit.</td></tr>
        @endforelse
    </table>
    <form method="POST" action="{{ route('admin.unit.simpan', $vehicle) }}" style="display:flex;gap:8px;margin-top:12px;align-items:center">
        @csrf
        <input name="plate_number" placeholder="AD 1234 RA" class="sm" style="width:160px;text-transform:uppercase" required>
        <button class="btn y s">+ Tambah unit</button>
    </form>
</div>
@endif
@endsection
