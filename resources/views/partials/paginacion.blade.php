@if ($paginator->hasPages())
    <nav>
        <ul class="pagination justify-content-end mb-0">

            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link border-0 bg-transparent text-muted" aria-hidden="true">
                        <i class="fas fa-chevron-left"></i> {{-- Icono en vez de texto --}}
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 bg-transparent text-secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Elementos de la Paginación --}}
            @foreach ($elements as $element)
                {{-- "Tres puntos" separadores --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link border-0 bg-transparent text-muted">{{ $element }}</span></li>
                @endif

                {{-- Array de Enlaces --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- PÁGINA ACTIVA (Gris Oscuro y negrita) --}}
                            <li class="page-item active" aria-current="page">
                                <span class="page-link border-0 bg-secondary text-white shadow-sm rounded-circle mx-1">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            {{-- PÁGINA INACTIVA --}}
                            <li class="page-item">
                                <a class="page-link border-0 bg-transparent text-secondary mx-1" href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 bg-transparent text-secondary" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fas fa-chevron-right"></i> {{-- Icono en vez de texto --}}
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link border-0 bg-transparent text-muted" aria-hidden="true">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
