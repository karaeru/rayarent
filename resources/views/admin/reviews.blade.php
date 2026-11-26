@extends('app')
@section('title', 'Ulasan · Admin '.config('rayarent.nama'))
@php
    $sub = $total
        ? $total.' ulasan · rata-rata '.number_format($ringkas['rata'], 1, ',', '').' dari 5 (yang tampil)'.($tersembunyi ? ' · '.$tersembunyi.' disembunyikan' : '')
        : 'Belum ada ulasan. Pelanggan bisa memberi ulasan setelah pesanannya selesai.';
    $tab = 'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold text-ink-2 hover:bg-canvas hover:no-underline aria-[current=page]:bg-brand-soft aria-[current=page]:text-brand-strong';
@endphp
@section('content')

<x-page-head title="Ulasan pelanggan" :sub="$sub" />

<nav class="mb-4 flex w-fit max-w-full gap-0.5 overflow-x-auto rounded-xl border border-line bg-white p-1" aria-label="Saring ulasan">
    @foreach (['' => 'Semua', '1' => 'Tampil', '0' => 'Disembunyikan'] as $k => $label)
        <a href="{{ route('admin.ulasan', array_filter(['tampil' => $k], fn ($v) => $v !== '')) }}" class="{{ $tab }}" @if ((string) $filter === (string) $k) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>

@if ($reviews->isEmpty())
    <div class="rr-card px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Tidak ada ulasan di sini</strong>Ulasan muncul setelah pelanggan menyelesaikan sewa dan memberi nilai.</div>
@else
    <div class="rr-card divide-y divide-line">
        @foreach ($reviews as $r)
            <article @class(['grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start sm:p-5', 'bg-canvas/60' => ! $r->is_visible])>
                <div class="min-w-0">
                    <p class="m-0 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <x-stars :value="$r->rating" />
                        <b>{{ $r->user?->name }}</b>
                        <span class="text-sm text-muted">{{ $r->vehicle?->name }} · {{ $r->order?->code }} · {{ $r->created_at->translatedFormat('d M Y') }}</span>
                    </p>
                    <p @class(['mb-0 mt-2 leading-relaxed', 'text-ink-2' => $r->comment, 'italic text-muted' => ! $r->comment])>{{ $r->comment ?: 'Tanpa komentar' }}</p>
                    <p class="mb-0 mt-1.5 text-xs text-muted">Tampil di publik sebagai <b class="text-ink-2">{{ $r->public_name }}</b></p>
                </div>
                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                    <span @class(['inline-flex items-center gap-1.5 text-sm font-semibold before:size-2 before:rounded-full', 'text-ok before:bg-ok' => $r->is_visible, 'text-muted before:bg-line-strong' => ! $r->is_visible])>{{ $r->is_visible ? 'Tampil' : 'Disembunyikan' }}</span>
                    <form method="POST" action="{{ route('admin.ulasan.toggle', $r) }}"
                          data-confirm-title="{{ $r->is_visible ? 'Sembunyikan ulasan ini?' : 'Tampilkan lagi ulasan ini?' }}"
                          data-confirm="{{ $r->is_visible ? 'Ulasan dari '.$r->user?->name.' tidak lagi muncul di landing dan halaman kendaraan. Bisa ditampilkan lagi kapan saja.' : 'Ulasan dari '.$r->user?->name.' muncul lagi di landing dan halaman kendaraan.' }}"
                          data-confirm-ok="{{ $r->is_visible ? 'Ya, sembunyikan' : 'Ya, tampilkan' }}" data-confirm-tone="brand">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline btn-sm">{{ $r->is_visible ? 'Sembunyikan' : 'Tampilkan' }}</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-4">{{ $reviews->links('pagination') }}</div>
@endif
@endsection
