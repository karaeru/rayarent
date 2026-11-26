@extends('app')
@section('title', $vehicle->name.' · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unitAktif = $vehicle->units->where('status', '!=', 'perawatan')->count();
    $keterangan = ['lepas_kunci' => 'Anda menyetir sendiri.', 'driver' => 'Termasuk driver, BBM ditanggung penyewa.', 'driver_bbm' => 'Termasuk driver dan BBM.'];
@endphp
@section('content')

<x-crumbs :items="[['Katalog', route('pelanggan.katalog')], [$vehicle->name]]" />

<div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)] lg:gap-12">
    <div class="aspect-[16/10] overflow-hidden rounded-3xl border border-line bg-white">
        @if ($vehicle->photo_url)
            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->name }}" width="1600" height="1000" fetchpriority="high" class="h-full w-full object-contain p-[5%]">
        @else
            <span class="flex h-full items-center justify-center text-brand opacity-50 [&>svg]:w-1/2"><x-car-icon :type="$vehicle->icon" /></span>
        @endif
    </div>

    <div>
        <h1 class="m-0 text-4xl font-extrabold leading-tight tracking-tight">{{ $vehicle->name }}</h1>
        <p class="mb-0 mt-2 text-lg text-ink-2">{{ \App\Models\Vehicle::CATEGORIES[$vehicle->category] }} · {{ $vehicle->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
        <dl class="m-0 mt-6 grid grid-cols-3 divide-x divide-line rounded-xl border border-line bg-white">
            <div class="px-4 py-3"><dt class="text-xs font-semibold uppercase tracking-wide text-muted">Kapasitas</dt><dd class="m-0 mt-0.5 text-lg font-bold">{{ $vehicle->seat_label }} <small class="font-medium text-muted">kursi</small></dd></div>
            <div class="px-4 py-3"><dt class="text-xs font-semibold uppercase tracking-wide text-muted">Transmisi</dt><dd class="m-0 mt-0.5 text-lg font-bold">{{ $vehicle->transmission ?? 'Sesuai unit' }}</dd></div>
            <div class="px-4 py-3"><dt class="text-xs font-semibold uppercase tracking-wide text-muted">Unit siap jalan</dt><dd class="m-0 mt-0.5 text-lg font-bold tabular-nums">{{ $unitAktif }}</dd></div>
        </dl>
        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-6">
            <p class="m-0 mr-auto leading-tight">
                <span class="block text-xs font-semibold uppercase tracking-wide text-muted">Mulai dari</span>
                <span class="text-3xl font-extrabold tabular-nums">{{ $rp($vehicle->packages->min('price')) }}</span>
            </p>
            <a class="btn btn-lg" href="{{ route('pelanggan.pesan', $vehicle) }}">Pesan sekarang</a>
        </div>
        <p class="mb-0 mt-2 text-sm text-muted">Pilih paket, tanggal, dan unit di langkah berikutnya. Ketersediaan dicek otomatis.</p>
    </div>
</div>

<section class="pt-12">
    <x-section-head title="Pilih paket" sub="Sewa lebih lama dihitung kelipatan paket. Contoh: 3 hari = 3 × paket 24 jam." />
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
            <a href="{{ route('pelanggan.pesan', [$vehicle, 'paket' => $p->id]) }}"
               class="group flex flex-col gap-1 rounded-xl border border-line bg-white p-5 text-ink transition-colors hover:border-brand-strong hover:no-underline">
                <span class="text-sm font-bold text-brand-strong">{{ $p->service_label }}</span>
                <span class="text-sm text-muted">{{ $p->duration_hours }} jam per periode</span>
                <b class="my-1 text-2xl font-extrabold tabular-nums">{{ $rp($p->price) }}</b>
                <span class="text-sm text-ink-2">{{ $keterangan[$p->service] ?? '' }}</span>
                <span class="mt-3 border-t border-line pt-3 text-sm font-bold text-brand-strong group-hover:underline">Pilih paket ini</span>
            </a>
        @endforeach
    </div>
</section>
@endsection
