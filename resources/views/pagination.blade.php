@if ($paginator->hasPages())
<style>
    .pg{padding:6px 12px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);text-decoration:none;font-size:14px}
    .pg.cur{background:#14181f;color:#fff;border-color:#14181f;font-weight:700}
    .pg.off{color:var(--mu);opacity:.6}
</style>
<nav role="navigation" aria-label="Navigasi halaman" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
    @if ($paginator->onFirstPage())
        <span class="pg off">‹ Sebelumnya</span>
    @else
        <a class="pg" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Sebelumnya</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pg off">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pg cur" aria-current="page">{{ $page }}</span>
                @else
                    <a class="pg" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a class="pg" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya ›</a>
    @else
        <span class="pg off">Berikutnya ›</span>
    @endif
</nav>
@endif
