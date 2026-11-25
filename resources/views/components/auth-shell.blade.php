@props(['title', 'subtitle' => null, 'wide' => false])
@php
    // panel kiri: foto armada asli bila ada, angka dari database (bukan teks pemasaran)
    $nama = config('rayarent.nama');
    $foto = rescue(fn () => \App\Models\Vehicle::where('is_active', true)->whereNotNull('photo')->orderByDesc('capacity')->first()?->photo_url, null, false);
    $dp = (int) rescue(fn () => \App\Models\Setting::get('dp_percent', 30), 30, false);
    $unit = (int) rescue(fn () => \App\Models\VehicleUnit::where('status', '!=', 'perawatan')->count(), 0, false);
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
@endphp
<div @class(['au', 'au--wide' => $wide])>
    <aside class="au-side" aria-hidden="true">
        <div class="au-side__brand">
            @if ($logo) <img src="{{ asset('images/logo.'.$logo) }}" alt=""> @endif
            <span>{{ $nama }}</span>
        </div>

        <div class="au-side__media">
            @if ($foto)
                <img src="{{ $foto }}" alt="">
            @else
                <x-car-icon type="mpv" />
            @endif
        </div>

        <ul class="au-side__facts">
            @if ($unit) <li><b>{{ $unit }} unit</b> siap jalan, dicek otomatis per jam</li> @endif
            <li><b>DP {{ $dp }}%</b>, sisanya saat serah terima</li>
            <li><b>Invoice</b> dan riwayat bayar tersimpan di akun</li>
        </ul>
    </aside>

    <section class="au-main">
        <header class="au-head">
            <h1>{{ $title }}</h1>
            @if ($subtitle) <p>{{ $subtitle }}</p> @endif
        </header>
        {{ $slot }}
        @isset($footer)
            <div class="au-foot">{{ $footer }}</div>
        @endisset
    </section>
</div>
