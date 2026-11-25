@extends('app')
@section('title', 'Pesan '.$vehicle->name)
@section('content')
<p class="mb-2"><a href="{{ route('pelanggan.kendaraan', $vehicle) }}">← Detail kendaraan</a></p>
<div class="page-head"><div><h2>Pesan {{ $vehicle->name }}</h2><p class="page-sub">Lengkapi data sewa. Ketersediaan unit dicek otomatis.</p></div></div>

<div class="two-col">
    <form method="POST" action="{{ route('pelanggan.pesan.simpan', $vehicle) }}" id="f" class="card"><div class="card-body">
        @csrf
        <div class="field">
            <label for="pkg">Paket layanan</label>
            <select id="pkg" name="rental_package_id">
                @foreach ($vehicle->packages as $p)
                    <option value="{{ $p->id }}" @selected(old('rental_package_id') == $p->id)>
                        {{ $p->service_label }} · {{ $p->duration_hours }} jam · Rp{{ number_format($p->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="start">Mulai sewa</label>
                <input id="start" type="datetime-local" name="start_at" min="{{ $minStart }}" value="{{ old('start_at', now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i')) }}" required>
                @error('start_at') <p class="err">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="qty">Jumlah periode</label>
                <input id="qty" type="number" name="quantity" min="1" max="30" value="{{ old('quantity', 1) }}" required>
                @error('quantity') <p class="err">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="field">
            <label for="unit">Pilih unit</label>
            <select id="unit" name="vehicle_unit_id">
                <option value="">Otomatis (unit kosong mana saja)</option>
            </select>
            @error('vehicle_unit_id') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="loc">Lokasi jemput / penggunaan</label>
            <input id="loc" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="Contoh: Stasiun Solo Balapan">
        </div>

        <div class="field">
            <label for="mode">Pembayaran</label>
            <select id="mode" name="pay_mode">
                <option value="dp">DP ({{ $dpPercent }}%)</option>
                <option value="lunas">Lunas</option>
            </select>
        </div>

        <div id="sum" class="summary" aria-live="polite"></div>

        <div class="field agree">
            <label class="check">
                <input type="checkbox" id="agree" name="setuju" value="1" @checked(old('setuju')) required>
                <span>Saya sudah membaca dan menyetujui <a href="{{ route('syarat') }}" target="_blank" rel="noopener">Syarat &amp; Ketentuan sewa</a>, termasuk denda keterlambatan dan tanggung jawab atas kerusakan.</span>
            </label>
            @error('setuju') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="row">
            <button class="btn btn-lg" id="go" disabled>Buat pesanan</button>
            <a class="btn btn-lg btn-outline" href="{{ route('pelanggan.kendaraan', $vehicle) }}">Batal</a>
        </div>
    </div></form>

    <aside class="card sticky">
        <div class="media-hero" style="margin:0;border:0;border-radius:var(--radius) var(--radius) 0 0">
            @if ($vehicle->photo_url)
                <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}">
            @else
                <span class="media-placeholder"><x-car-icon :type="$vehicle->icon" /></span>
            @endif
        </div>
        <div class="card-body">
            <h3>{{ $vehicle->name }}</h3>
            <div class="vcard__chips mt-1">
                <span class="chip">{{ $vehicle->seat_label }} kursi</span>
                @if ($vehicle->transmission) <span class="chip">{{ $vehicle->transmission }}</span> @endif
            </div>
        </div>
    </aside>
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
        const pesan = res.status === 422
            ? (Object.values(e.errors || {})[0]?.[0] || 'Periksa isian Anda.')
            : res.status === 401 || res.status === 419
                ? 'Sesi Anda habis. Muat ulang halaman lalu masuk lagi.'
                : 'Gagal mengecek ketersediaan (kode ' + res.status + '). Coba lagi atau hubungi admin.';
        out.innerHTML = '<span class="err">' + pesan + '</span>';
        tersedia = false;
        tombol();
        return;
    }
    const d = await res.json();
    const free = d.units.filter(u => u.ok).length;

    $('unit').innerHTML = '<option value="">Otomatis (unit kosong mana saja)</option>' +
        d.units.map(u => `<option value="${u.id}" ${u.ok ? '' : 'disabled'} ${u.ok && String(u.id) === keep ? 'selected' : ''}>
            ${u.plate} – ${u.ok ? 'tersedia' : u.why}</option>`).join('');

    const bayar = $('mode').value === 'dp' ? d.dp : d.total;
    out.innerHTML = (free
        ? `<p><span class="badge badge-success">${free} dari ${d.units.length} unit tersedia</span></p>`
        : '<p class="err">Tidak ada unit tersedia pada periode ini. Pilih tanggal atau kendaraan lain.</p>')
        + `<p class="muted small">Selesai: ${d.end}</p>
           <p>Total <b>${rp(d.total)}</b></p>
           <p class="muted small">Bayar sekarang: ${rp(bayar)}</p>`;
    tersedia = free > 0;
    tombol();
}

let tersedia = false;
const tombol = () => {
    $('go').disabled = !tersedia || !$('agree').checked;
    $('go').title = !tersedia ? 'Tidak ada unit tersedia' : (!$('agree').checked ? 'Centang persetujuan Syarat & Ketentuan dulu' : '');
};
$('agree').addEventListener('change', tombol);

['pkg', 'start', 'qty', 'mode', 'unit'].forEach(id => $(id).addEventListener('change', cek));
$('qty').addEventListener('input', () => { clearTimeout(t); t = setTimeout(cek, 300); });
cek();
</script>
@endsection
