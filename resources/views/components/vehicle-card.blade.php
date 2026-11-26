@props([
    'href',
    'name',
    'photo' => null,
    'icon' => 'mpv',
    'category' => null,
    'chips' => [],
    'variants' => [],
    'services' => null,
    'price' => 0,
    'cat' => null,
    'extra' => false,
])
{{-- Kartu kendaraan untuk halaman publik (landing, detail armada). Tailwind, tanpa bayangan (DESIGN.md §4). --}}
<article {{ $attributes->class(['group flex flex-col overflow-hidden rounded-xl border border-line bg-white transition-colors duration-200 hover:border-line-strong', 'is-extra' => $extra]) }}
         @if ($cat) data-cat="{{ $cat }}" @endif data-reveal>
    <a href="{{ $href }}" tabindex="-1" aria-hidden="true" class="relative block aspect-[16/10] overflow-hidden border-b border-line bg-white">
        @if ($photo)
            <img src="{{ $photo }}" alt="" width="800" height="500" loading="lazy"
                 class="h-full w-full object-contain p-[4%] transition-transform duration-500 ease-out group-hover:scale-[1.04] motion-reduce:transition-none motion-reduce:group-hover:scale-100">
        @else
            <span class="flex h-full items-center justify-center text-brand opacity-50 [&>svg]:w-[55%] [&>svg]:max-w-56"><x-car-icon :type="$icon" /></span>
        @endif
        @if ($category)
            <span class="absolute left-3 top-3 rounded-full border border-line bg-white px-2.5 py-0.5 text-xs font-bold text-ink-2">{{ $category }}</span>
        @endif
    </a>
    <div class="flex flex-1 flex-col gap-2.5 p-4">
        <h3 class="m-0 text-lg font-bold leading-snug">
            <a href="{{ $href }}" class="text-ink hover:text-brand-strong hover:no-underline">{{ $name }}</a>
        </h3>
        @if (count($chips))
            <div class="flex flex-wrap gap-1.5">
                @foreach ($chips as $chip)
                    <span class="rounded-full border border-line bg-canvas px-2.5 py-0.5 text-xs text-ink-2">{{ $chip }}</span>
                @endforeach
            </div>
        @endif
        @if (count($variants))
            <div class="flex gap-1.5">
                @foreach ($variants as $var)
                    <a href="{{ $var['url'] }}" class="rounded-md bg-brand-soft px-2.5 py-1 text-xs font-bold text-brand-strong transition-colors hover:bg-brand-strong hover:text-white hover:no-underline">{{ $var['label'] }}</a>
                @endforeach
            </div>
        @endif
        @if ($services)
            <p class="m-0 text-sm text-muted">{{ $services }}</p>
        @endif
        <div class="mt-auto flex items-end justify-between gap-3 border-t border-line pt-3">
            <p class="m-0 leading-tight">
                <span class="block text-[0.7rem] font-semibold uppercase tracking-wide text-muted">Mulai dari</span>
                <span class="text-lg font-extrabold tabular-nums text-ink">Rp{{ number_format($price, 0, ',', '.') }}</span>
            </p>
            <a href="{{ $href }}" class="btn btn-sm btn-outline">Detail</a>
        </div>
    </div>
</article>
