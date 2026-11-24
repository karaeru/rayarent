<div class="kt-map" data-reveal>
    @if ($mapEmbed)
        <iframe src="{{ $mapEmbed }}" title="Lokasi {{ config('rayarent.nama') }} di Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        <div class="kt-map__bar">
            <span class="kt-map__pin" aria-hidden="true"></span>
            <span class="grow small">{{ config('rayarent.alamat') ?: 'Lokasi kami' }}</span>
            <a class="btn btn-sm" href="{{ $mapRoute }}" target="_blank" rel="noopener">Petunjuk arah</a>
        </div>
    @else
        <div class="empty"><strong>Lokasi belum diatur</strong>Isi RAYARENT_ALAMAT atau RAYARENT_MAPS di file .env.</div>
    @endif
</div>
