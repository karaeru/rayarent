@props(['status'])
@php
    $map = [
        'Lunas' => ['success', 'Lunas'],
        'DP Dibayar' => ['info', 'DP Dibayar'],
        'Menunggu Verifikasi' => ['warn', 'Menunggu Verifikasi'],
        'Ditolak' => ['danger', 'Ditolak'],
        'Belum Bayar' => ['muted', 'Belum Bayar'],
        // status satu pembayaran
        'diterima' => ['success', 'Diterima'],
        'ditolak' => ['danger', 'Ditolak'],
        'menunggu' => ['warn', 'Menunggu Verifikasi'],
    ];
    [$tone, $label] = $map[$status] ?? ['muted', $status];
@endphp
<span class="badge badge-{{ $tone }}">{{ $label }}</span>
