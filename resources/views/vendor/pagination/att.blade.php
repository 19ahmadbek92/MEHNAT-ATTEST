@if ($paginator->hasPages())
    <nav class="att-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="att-pagination__summary">
            @if ($paginator->total() > 0)
                {{ trans_choice('Yozuvlar :first–:last (jami :total)', $paginator->total(), [
                    'first' => $paginator->firstItem(),
                    'last'  => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]) }}
            @endif
        </div>

        <ul class="att-pagination__list">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="att-pagination__item is-disabled" aria-disabled="true">
                    <span class="att-pagination__link">‹</span>
                </li>
            @else
                <li class="att-pagination__item">
                    <a class="att-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">‹</a>
                </li>
            @endif

            {{-- Numeric pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="att-pagination__item is-disabled" aria-disabled="true"><span class="att-pagination__link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="att-pagination__item is-active" aria-current="page">
                                <span class="att-pagination__link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="att-pagination__item">
                                <a class="att-pagination__link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="att-pagination__item">
                    <a class="att-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">›</a>
                </li>
            @else
                <li class="att-pagination__item is-disabled" aria-disabled="true">
                    <span class="att-pagination__link">›</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
