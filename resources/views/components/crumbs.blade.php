@props(['items' => []])
{{-- Breadcrumb halaman publik. $items: [['Label', url], ..., ['Halaman ini']] --}}
<nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-muted">
    @foreach ($items as $i => $it)
        @if ($i) <span aria-hidden="true" class="text-line-strong">/</span> @endif
        @if (isset($it[1]))
            <a href="{{ $it[1] }}" class="text-muted hover:text-brand-strong">{{ $it[0] }}</a>
        @else
            <span aria-current="page" class="font-semibold text-ink">{{ $it[0] }}</span>
        @endif
    @endforeach
</nav>
