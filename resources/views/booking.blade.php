@extends('app')
@section('title', 'Pesan '.$vehicle->name)
@section('content')
<div class="card" style="max-width:520px">
    <h2 style="margin:0">{{ $vehicle->name }}</h2>
    <p style="color:var(--mu);margin:2px 0 0">{{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }}</p>

    <form method="POST" action="{{ route('pelanggan.pesan.simpan', $vehicle) }}" id="f">
        @csrf
        <label for="pkg">Paket layanan</label>
        <select id="pkg" name="rental_package_id" style="width:100%;padding:9px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx)">
            @foreach ($vehicle->packages as $p)
                <option value="{{ $p->id }}" @selected(old('rental_package_id') == $p->id)>
                    {{ $p->service_label }} · {{ $p->duration_hours }} jam · Rp{{ number_format($p->price, 0, ',', '.') }}
                </option>
            @endforeach
        </select>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px">
            <div>
                <label for="start">Mulai</label>
                <input id="start" type="datetime-local" name="start_at" min="{{ $minStart }}" value="{{ old('start_at', now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div>
                <label for="qty">Jumlah periode</label>
                <input id="qty" type="number" name="quantity" min="1" max="30" value="{{ old('quantity', 1) }}" required>
            </div>
        </div>
        @error('start_at') <p class="err">{{ $message }}</p> @enderror
        @error('quantity') <p class="err">{{ $message }}</p> @enderror

        <label for="unit">Pilih unit</label>
        <select id="unit" name="vehicle_unit_id" style="width:100%;padding:9px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx)">
            <option value="">Otomatis (unit kosong mana saja)</option>
        </select>
        @error('vehicle_unit_id') <p class="err">{{ $message }}</p> @enderror

        <label for="loc">Lokasi jemput / penggunaan</label>
        <input id="loc" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="Contoh: Stasiun Solo Balapan">

        <label for="mode">Pembayaran</label>
        <select id="mode" name="pay_mode" style="width:100%;padding:9px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx)">
            <option value="dp">DP ({{ $dpPercent }}%)</option>
            <option value="lunas">Lunas</option>
        </select>

        <div id="sum" style="border:1px dashed var(--ln);border-radius:8px;padding:10px;margin-top:14px"></div>
        <p><button class="btn y" id="go" disabled>Buat pesanan</button></p>
    </form>
</div>

<script>
const url = @json(route('pelanggan.ketersediaan', $vehicle));
const $ = id => document.getElementById(id);
const rp = n => 'Rp' + Math.round(n).toLocaleString('id-ID');
let t;

async function cek() {
    const q = new URLSearchParams({
        rental_package_id: $('pkg').value, start_at: $('start').value, quantity: $('qty').value,
    });
    const keep = $('unit').value;
    const res = await fetch(url + '?' + q, { headers: { Accept: 'application/json' } });
    const out = $('sum');

    if (!res.ok) {
        const e = await res.json().catch(() => ({}));
        out.innerHTML = '<span class="err">' + (Object.values(e.errors || {})[0]?.[0] || 'Periksa isian Anda.') + '</span>';
        $('go').disabled = true;
        return;
    }
    const d = await res.json();
    const free = d.units.filter(u => u.ok).length;

    $('unit').innerHTML = '<option value="">Otomatis (unit kosong mana saja)</option>' +
        d.units.map(u => `<option value="${u.id}" ${u.ok ? '' : 'disabled'} ${u.ok && String(u.id) === keep ? 'selected' : ''}>
            ${u.plate} – ${u.ok ? 'tersedia' : u.why}</option>`).join('');

    const bayar = $('mode').value === 'dp' ? d.dp : d.total;
    out.innerHTML = (free
        ? `<span style="color:var(--gn)">${free} dari ${d.units.length} unit tersedia</span>`
        : '<span class="err">Tidak ada unit tersedia pada periode ini. Pilih tanggal atau kendaraan lain.</span>')
        + `<p style="margin:6px 0 0;color:var(--mu)">Selesai: ${d.end}</p>
           <p style="margin:6px 0 0">Total <b>${rp(d.total)}</b></p>
           <p style="margin:2px 0 0;color:var(--mu)">Bayar sekarang: ${rp(bayar)}</p>`;
    $('go').disabled = !free;
}

['pkg', 'start', 'qty', 'mode', 'unit'].forEach(id => $(id).addEventListener('change', cek));
$('qty').addEventListener('input', () => { clearTimeout(t); t = setTimeout(cek, 300); });
cek();
</script>
@endsection
