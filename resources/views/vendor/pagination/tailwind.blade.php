@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-6">
        {{-- Mobile View --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-100 cursor-default rounded-2xl transition-all">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-slate-900 bg-white border border-gray-200 rounded-2xl hover:bg-brand-yellow hover:border-brand-yellow transition-all active:scale-95 shadow-sm">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="relative inline-flex items-center px-6 py-3 ml-3 text-sm font-bold text-slate-900 bg-white border border-gray-200 rounded-2xl hover:bg-brand-yellow hover:border-brand-yellow transition-all active:scale-95 shadow-sm">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-6 py-3 ml-3 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-100 cursor-default rounded-2xl transition-all">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 leading-5">
                    {!! __('Menampilkan') !!}
                    @if ($paginator->firstItem())
                        <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
                        {!! __('sampai') !!}
                        <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('dari') !!}
                    <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                    {!! __('hasil') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex gap-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="relative inline-flex items-center p-3 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-100 cursor-default rounded-xl transition-all"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="relative inline-flex items-center p-3 text-sm font-medium text-slate-600 bg-white border border-gray-200 rounded-xl hover:bg-brand-yellow hover:border-brand-yellow hover:text-slate-900 transition-all active:scale-95 shadow-sm"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="relative inline-flex items-center px-4 py-3 text-sm font-black text-slate-900 bg-brand-yellow border border-brand-yellow rounded-xl shadow-md min-w-[48px] justify-center">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="relative inline-flex items-center px-4 py-3 text-sm font-bold text-slate-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 hover:text-slate-900 transition-all active:scale-95 shadow-sm min-w-[48px] justify-center"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="relative inline-flex items-center p-3 text-sm font-medium text-slate-600 bg-white border border-gray-200 rounded-xl hover:bg-brand-yellow hover:border-brand-yellow hover:text-slate-900 transition-all active:scale-95 shadow-sm"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="relative inline-flex items-center p-3 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-100 cursor-default rounded-xl transition-all"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
