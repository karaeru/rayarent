<div class="overflow-hidden rounded-xl border border-line bg-white" data-reveal>
    @if ($mapEmbed)
        <iframe src="{{ $mapEmbed }}" title="Lokasi {{ config('rayarent.nama') }} di Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                class="block aspect-[16/10] w-full border-0 lg:aspect-[16/9]"></iframe>
        <div class="flex flex-wrap items-center gap-3 border-t border-line px-4 py-3">
            <span class="min-w-0 flex-1 text-sm text-ink-2">{{ config('rayarent.alamat') ?: 'Lokasi kami' }}</span>
            <a class="btn btn-sm" href="{{ $mapRoute }}" target="_blank" rel="noopener">Petunjuk arah</a>
        </div>
    @else
        <div class="px-5 py-10 text-center text-muted"><strong class="mb-1 block text-ink">Lokasi belum diatur</strong>Isi RAYARENT_ALAMAT atau RAYARENT_MAPS di file .env.</div>
    @endif
</div>
