@if ($paginator->hasPages())
    <div class="w3-bar">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <a class="abn w3-button w3-disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                &laquo;
            </a>
        @else
            <a class="abn w3-button" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                &laquo;
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <a class="abn w3-button w3-disabled">{{ $element }}</a>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a class="abn w3-button themebg w3-text-white">{{ $page }}</a>
                    @else
                        <a class="abn w3-button" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="abn w3-button" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                &raquo;
            </a>
        @else
            <a class="abn w3-button w3-disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                &raquo;
            </a>
        @endif
    </div>

    {{-- Showing X-Y out of N --}}
    <div class="w3-small w3-margin-top">
        Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        out of <b class="themetxt">{{ $paginator->total() }}</b> items
    </div>
@endif
