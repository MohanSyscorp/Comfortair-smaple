@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="carrier-pagination-container">
        {{-- Results Counter Text --}}
        <div class="pagination-results-text">
            <span>{!! __('Showing') !!}</span>
            <span class="pagination-bold-val">{{ $paginator->firstItem() ?? 0 }}</span>
            <span>{!! __('to') !!}</span>
            <span class="pagination-bold-val">{{ $paginator->lastItem() ?? 0 }}</span>
            <span>{!! __('of') !!}</span>
            <span class="pagination-bold-val">{{ $paginator->total() }}</span>
            <span>{!! __('records') !!}</span>
        </div>

        {{-- Pagination Links List --}}
        <ul class="carrier-pagination-list">
            @if ($paginator->hasPages())
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="pagination-item disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="pagination-link nav-btn disabled">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Previous</span>
                        </span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link nav-btn" aria-label="{{ __('pagination.previous') }}">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Previous</span>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="pagination-item disabled" aria-disabled="true">
                            <span class="pagination-link dots">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="pagination-item active" aria-current="page">
                                    <span class="pagination-link page-num active">{{ $page }}</span>
                                </li>
                            @else
                                <li class="pagination-item">
                                    <a href="{{ $url }}" class="pagination-link page-num">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="pagination-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link nav-btn" aria-label="{{ __('pagination.next') }}">
                            <span>Next</span>
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                @else
                    <li class="pagination-item disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="pagination-link nav-btn disabled">
                            <span>Next</span>
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </li>
                @endif
            @else
                <li class="pagination-item active">
                    <span class="pagination-link page-num active" style="font-size: 15px;">1</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
