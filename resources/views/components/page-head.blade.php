@props(['title', 'sub' => null])
{{-- Judul halaman area akun (pelanggan, nanti admin): satu h1, keterangan, dan tombol di kanan --}}
<div class="mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
    <div class="min-w-0">
        <h1 class="m-0 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-3xl font-extrabold tracking-tight">{{ $title }}{{ $badges ?? '' }}</h1>
        @if ($sub) <p class="mb-0 mt-1.5 text-muted">{{ $sub }}</p> @endif
    </div>
    @if (isset($slot) && trim((string) $slot) !== '') <div class="flex flex-wrap gap-2">{{ $slot }}</div> @endif
</div>
