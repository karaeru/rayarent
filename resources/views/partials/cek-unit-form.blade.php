{{--
    Kotak "Cek unit kosong". Dipakai di landing (mengambang di blok biru) dan Beranda pelanggan.
    Variabel: $aksi (URL tujuan, sertakan #anchor hasil), $services, $kursiTerbanyak, $kelas (opsional).
--}}
@php
    $mulaiDefault = old('mulai', request('mulai', now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i')));
    $rombongan = collect([1 => '1–4 orang', 5 => '5–7 orang', 8 => '8–14 orang', 15 => '15 orang atau lebih'])->filter(fn ($l, $n) => $n <= max(1, $kursiTerbanyak));
@endphp
<form method="GET" action="{{ $aksi }}" id="cek-unit" aria-label="Cek unit kosong" data-cek-ajax data-swap="cek-form"
      @class(['rr-card grid scroll-mt-28 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[repeat(3,minmax(0,1fr))_auto] lg:items-end', $kelas ?? ''])>
    <div class="min-w-0">
        <label for="cek-mulai" class="mb-1.5 block text-xs font-semibold text-muted">Mulai sewa</label>
        <input id="cek-mulai" type="datetime-local" name="mulai" value="{{ $mulaiDefault }}" min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required class="rr-input font-semibold">
    </div>
    <div class="min-w-0">
        <label for="cek-layanan" class="mb-1.5 block text-xs font-semibold text-muted">Layanan</label>
        <select id="cek-layanan" name="layanan" class="rr-input font-semibold">
            <option value="">Semua layanan</option>
            @foreach ($services as $k => $label) <option value="{{ $k }}" @selected(request('layanan') === $k)>{{ $label }}</option> @endforeach
        </select>
    </div>
    <div class="min-w-0">
        <label for="cek-orang" class="mb-1.5 block text-xs font-semibold text-muted">Jumlah orang</label>
        <select id="cek-orang" name="orang" class="rr-input font-semibold">
            <option value="">Berapa saja</option>
            @foreach ($rombongan as $n => $l) <option value="{{ $n }}" @selected((int) request('orang') === $n)>{{ $l }}</option> @endforeach
        </select>
    </div>
    <button class="btn btn-lg sm:col-span-2 lg:col-span-1" data-label-busy="Mengecek…">Cek unit kosong</button>
    @error('mulai') <p class="rr-err m-0 sm:col-span-2 lg:col-span-4">{{ $message }}</p> @enderror
</form>
