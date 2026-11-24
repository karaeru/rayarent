@props(['status'])
@php
    $map = [
        'menunggu_konfirmasi' => ['warn', 'Menunggu Konfirmasi'],
        'dikonfirmasi' => ['info', 'Dikonfirmasi'],
        'sedang_disewa' => ['info', 'Sedang Disewa'],
        'selesai' => ['success', 'Selesai'],
        'dibatalkan' => ['danger', 'Dibatalkan'],
    ];
    [$tone, $label] = $map[$status] ?? ['muted', str_replace('_', ' ', $status)];
@endphp
<span class="badge badge-{{ $tone }}">{{ $label }}</span>
