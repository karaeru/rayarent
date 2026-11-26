@props(['items' => [], 'dark' => false])
{{-- Breadcrumb halaman publik. $items: [['Label', url], ..., ['Halaman ini']]. dark: untuk di atas blok biru. --}}
<nav aria-label="Breadcrumb" @class(['mb-5 flex flex-wrap items-center gap-2 text-sm', 'text-muted' => ! $dark, 'text-white/75' => $dark])>
    @foreach ($items as $i => $it)
        @if ($i) <span aria-hidden="true" @class(['text-line-strong' => ! $dark, 'text-white/40' => $dark])>/</span> @endif
        @if (isset($it[1]))
            <a href="{{ $it[1] }}" @class(['text-muted hover:text-brand-strong' => ! $dark, 'text-white/80 hover:text-white' => $dark])>{{ $it[0] }}</a>
        @else
            <span aria-current="page" @class(['font-semibold', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $it[0] }}</span>
        @endif
    @endforeach
</nav>
