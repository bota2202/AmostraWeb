{{-- Paginação do AmostraWeb: total de registros + páginas, acessível --}}
@if ($paginator->total() > 0)
<nav class="paginacao" aria-label="Paginação">
    <p class="paginacao-total">
        Mostrando <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong> de <strong>{{ $paginator->total() }}</strong>
    </p>

    @if ($paginator->hasPages())
    <ul class="pagination">
        @if ($paginator->onFirstPage())
        <li class="page-item disabled"><span class="page-link" aria-hidden="true">&lsaquo;</span><span class="sr-only">Página anterior</span></li>
        @else
        <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><span aria-hidden="true">&lsaquo;</span><span class="sr-only">Página anterior</span></a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link" aria-current="page"><span class="sr-only">Página </span>{{ $page }}</span></li>
                    @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}"><span class="sr-only">Página </span>{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"><span aria-hidden="true">&rsaquo;</span><span class="sr-only">Próxima página</span></a></li>
        @else
        <li class="page-item disabled"><span class="page-link" aria-hidden="true">&rsaquo;</span><span class="sr-only">Próxima página</span></li>
        @endif
    </ul>
    @endif
</nav>
@endif
