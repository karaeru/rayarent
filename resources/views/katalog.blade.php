@extends('app')
@section('title', 'Katalog · '.config('rayarent.nama'))
@php
    $f = array_filter(request()->only(['search', 'service', 'capacity']), fn ($v) => $v !== null && $v !== '');
    $link = fn (array $ubah) => route('pelanggan.katalog', array_filter(array_merge($f, $ubah), fn ($v) => $v !== null && $v !== ''));
    $kapasitas = ['' => 'Semua', 4 => '4+ kursi', 7 => '7+ kursi', 14 => '14+ kursi'];
    $pill = 'rounded-full border border-line-strong bg-white px-3.5 py-1.5 text-sm font-semibold text-ink-2 transition hover:border-brand-strong hover:no-underline aria-[current=true]:border-brand-strong aria-[current=true]:bg-brand-strong aria-[current=true]:text-white';
@endphp
@section('content')

<x-page-head title="Pilih kendaraan" :sub="$vehicles->count().' kendaraan'.($f ? ' sesuai pencarian' : ' siap disewa').' · ketersediaan unit dicek saat Anda memilih tanggal'">
    <a class="btn btn-outline btn-sm" href="{{ route('pelanggan.pesanan') }}">Pesanan saya</a>
</x-page-head>

{{-- filter berupa link biasa: bisa dibagikan, tombol Kembali tetap bekerja --}}
<div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <nav class="flex flex-wrap gap-2" aria-label="Jenis layanan">
        <a href="{{ $link(['service' => null]) }}" class="{{ $pill }}" @if (empty($f['service'])) aria-current="true" @endif>Semua layanan</a>
        @foreach ($services as $k => $label)
            <a href="{{ $link(['service' => $k]) }}" class="{{ $pill }}" @if (($f['service'] ?? '') === $k) aria-current="true" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex rounded-lg border border-line-strong bg-white p-0.5" role="group" aria-label="Kapasitas">
            @foreach ($kapasitas as $k => $label)
                @php $aktif = (string) ($f['capacity'] ?? '') === (string) $k; @endphp
                <a href="{{ $link(['capacity' => $k ?: null]) }}" @class(['rounded-md px-3 py-1.5 text-sm font-semibold hover:no-underline', 'bg-brand-strong text-white hover:text-white' => $aktif, 'text-ink-2 hover:bg-canvas' => ! $aktif]) @if ($aktif) aria-current="true" @endif>{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" role="search" class="relative min-w-56 flex-1 lg:flex-none">
            @foreach (['service', 'capacity'] as $k) @if (! empty($f[$k])) <input type="hidden" name="{{ $k }}" value="{{ $f[$k] }}"> @endif @endforeach
            <svg viewBox="0 0 24 24" aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 fill-none stroke-muted stroke-2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input name="search" value="{{ $f['search'] ?? '' }}" placeholder="Cari nama kendaraan" aria-label="Cari nama kendaraan" class="rr-input py-2 pl-9 pr-9 text-sm">
            @if (! empty($f['search'])) <a href="{{ $link(['search' => null]) }}" class="absolute right-1.5 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-md text-lg text-muted hover:bg-canvas hover:no-underline" aria-label="Hapus pencarian">×</a> @endif
        </form>
    </div>
</div>

@if ($f)
    <p class="mb-5 flex flex-wrap items-center gap-2 text-sm text-muted">
        Filter aktif:
        @if (! empty($f['service'])) <span class="rounded-md bg-brand-soft px-2 py-0.5 font-semibold text-brand-strong">{{ $services[$f['service']] ?? $f['service'] }}</span> @endif
        @if (! empty($f['capacity'])) <span class="rounded-md bg-brand-soft px-2 py-0.5 font-semibold text-brand-strong">min. {{ $f['capacity'] }} kursi</span> @endif
        @if (! empty($f['search'])) <span class="rounded-md bg-brand-soft px-2 py-0.5 font-semibold text-brand-strong">"{{ $f['search'] }}"</span> @endif
        <a href="{{ route('pelanggan.katalog') }}" class="font-semibold text-brand-strong underline underline-offset-2">Hapus semua</a>
    </p>
@endif

@if ($vehicles->isEmpty())
    <div class="rr-card px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Tidak ada kendaraan yang cocok</strong>Ubah kata kunci atau filter Anda.</div>
@else
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($vehicles as $v)
        <x-vehicle-card :href="route('pelanggan.kendaraan', $v)" :name="$v->name" :photo="$v->photo_url" :icon="$v->icon"
            :category="\App\Models\Vehicle::CATEGORIES[$v->category]"
            :chips="array_filter([$v->seat_label.' kursi', $v->transmission, $v->units_count.' unit'])"
            :services="$v->packages->pluck('service_label')->unique()->implode(' · ')" :price="$v->min_price">
            <x-slot:actions>
                <a class="btn btn-sm btn-outline" href="{{ route('pelanggan.kendaraan', $v) }}">Detail</a>
                <a class="btn btn-sm" href="{{ route('pelanggan.pesan', $v) }}">Pesan</a>
            </x-slot:actions>
        </x-vehicle-card>
    @endforeach
</div>
@endif
@endsection
