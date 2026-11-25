@props(['type' => 'mpv'])
@php
    // Siluet samping, menghadap kanan. Warna mengikuti `color` (currentColor).
    $body = [
        'city' => 'M10 44V35c0-4 3-7 9-8l13-9c3-2 6-3 10-3h22c5 0 9 2 12 5l9 8c9 1 14 4 15 9v7z',
        'mpv'  => 'M8 44V33c0-4 3-6 9-7l12-11c2-2 5-3 9-3h56c5 0 8 2 10 6l6 13c2 1 3 3 3 6v7z',
        'suv'  => 'M8 44V31c0-4 3-6 8-7l11-11c2-2 4-3 8-3h62c5 0 8 2 9 6l4 13c2 1 3 3 3 6v9z',
        'van'  => 'M6 44V19c0-6 4-10 10-10h86c6 0 10 3 11 9l3 17c1 1 1 3 1 4v5z',
    ][$type] ?? null;
    $win = [
        'city' => 'M37 26l9-6c2-1 4-2 6-2h4v8zM60 18h5c3 0 6 1 8 3l6 5H60z',
        'mpv'  => 'M33 25l8-8c1-1 3-2 5-2h12v10zM62 15h18v10H62zM84 15h9c2 0 4 1 5 3l4 7H84z',
        'suv'  => 'M31 23l7-8c1-1 3-2 5-2h13v10zM60 13h20v10H60zM84 13h11c2 0 3 1 4 3l3 7H84z',
        'van'  => 'M14 16h18v12H14zM36 16h18v12H36zM58 16h18v12H58zM80 16h18v12H80zM102 16h4c2 0 4 2 4 4l2 8h-10z',
    ][$type] ?? '';
    $body ??= 'M8 44V33c0-4 3-6 9-7l12-11c2-2 5-3 9-3h56c5 0 8 2 10 6l6 13c2 1 3 3 3 6v7z';
    $wheels = $type === 'van' ? [28, 94] : ($type === 'city' ? [30, 92] : [30, 96]);
@endphp
<svg {{ $attributes->merge(['class' => 'car-icon', 'viewBox' => '0 0 124 56', 'fill' => 'none', 'aria-hidden' => 'true', 'focusable' => 'false']) }} xmlns="http://www.w3.org/2000/svg">
    <path d="{{ $body }}" fill="currentColor"/>
    <path d="{{ $win }}" fill="#fff" fill-opacity=".55"/>
    @foreach ($wheels as $x)
        <circle cx="{{ $x }}" cy="44" r="8.5" fill="#fff" stroke="currentColor" stroke-width="4"/>
        <circle cx="{{ $x }}" cy="44" r="2.5" fill="currentColor"/>
    @endforeach
</svg>
