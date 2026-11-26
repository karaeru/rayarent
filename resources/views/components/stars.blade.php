@props(['value' => 0, 'size' => 'size-4'])
{{-- Bintang penilaian (tampilan saja). Warna amber #D97706 (3,2:1 di putih), nilai juga dibacakan sebagai teks. --}}
@php $n = (float) $value; @endphp
<span {{ $attributes->class('inline-flex items-center gap-0.5 align-middle') }}>
    @for ($i = 1; $i <= 5; $i++)
        <svg viewBox="0 0 24 24" aria-hidden="true" @class([$size, 'flex-none', 'text-[#d97706]' => $n >= $i - 0.25, 'text-line-strong' => $n < $i - 0.25])>
            <path fill="currentColor" d="m12 3.2 2.6 5.5 6 .8-4.4 4.1 1.1 5.9L12 16.6l-5.3 2.9 1.1-5.9L3.4 9.5l6-.8Z"/>
        </svg>
    @endfor
    <span class="sr-only">Nilai {{ rtrim(rtrim(number_format($n, 1, ',', ''), '0'), ',') }} dari 5</span>
</span>
