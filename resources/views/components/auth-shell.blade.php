@props(['title', 'subtitle' => null, 'wide' => false])
@php
    // panel kiri: foto armada asli bila ada, angka dari database (bukan teks pemasaran)
    $nama = config('rayarent.nama');
    $foto = rescue(fn () => \App\Models\Vehicle::where('is_active', true)->whereNotNull('photo')->orderByDesc('capacity')->first()?->photo_url, null, false);
    $dp = (int) rescue(fn () => \App\Models\Setting::get('dp_percent', 30), 30, false);
    $unit = (int) rescue(fn () => \App\Models\VehicleUnit::where('status', '!=', 'perawatan')->count(), 0, false);
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
@endphp
<div @class(['mx-auto grid overflow-hidden rounded-3xl border border-line bg-white md:grid-cols-[minmax(0,0.9fr)_minmax(0,1fr)]', 'max-w-5xl' => $wide, 'max-w-4xl' => ! $wide])>
    {{-- bidang brand solid (PB-8), bukan gradasi --}}
    <aside class="hidden flex-col gap-6 bg-brand-strong p-8 text-white md:flex" aria-hidden="true">
        <div class="flex items-center gap-2.5 text-lg font-extrabold tracking-wide">
            @if ($logo) <img src="{{ asset('images/logo.'.$logo) }}" alt="" width="30" height="30" class="rounded-full bg-white p-0.5"> @endif
            <span>{{ $nama }}</span>
        </div>
        <div class="flex aspect-[16/10] items-center justify-center rounded-2xl bg-white/95 p-4 text-brand [&>svg]:w-2/3">
            @if ($foto)
                <img src="{{ $foto }}" alt="" width="800" height="500" class="h-full w-full object-contain">
            @else
                <x-car-icon type="mpv" />
            @endif
        </div>
        <ul class="m-0 mt-auto flex list-none flex-col gap-3 p-0 text-white/90 [&_b]:text-white">
            @if ($unit) <li class="border-t border-white/20 pt-3"><b>{{ $unit }} unit</b> siap jalan, dicek otomatis per jam</li> @endif
            <li class="border-t border-white/20 pt-3"><b>DP {{ $dp }}%</b>, sisanya saat serah terima</li>
            <li class="border-t border-white/20 pt-3"><b>Invoice</b> dan riwayat bayar tersimpan di akun</li>
        </ul>
    </aside>

    <section class="p-6 sm:p-9">
        <header class="mb-6">
            <h1 class="m-0 text-3xl font-extrabold tracking-tight">{{ $title }}</h1>
            @if ($subtitle) <p class="mb-0 mt-2 text-ink-2">{{ $subtitle }}</p> @endif
        </header>
        {{ $slot }}
        @isset($footer)
            <div class="mt-6 border-t border-line pt-5 text-sm text-ink-2">{{ $footer }}</div>
        @endisset
    </section>
</div>
