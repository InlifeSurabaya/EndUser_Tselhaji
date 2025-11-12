@if ($paginator->hasPages())
<div class="flex justify-between flex-wrap items-center">
    <div class="py-1 px-4">
        <p class="text-sm font-normal text-gray-600">
            Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} Data
        </p>
    </div>

    <div class="py-1 px-4">
        <nav class="flex items-center space-x-1" aria-label="Pagination">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <button disabled
                    class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-400 cursor-not-allowed">
                    «
                </button>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-700 hover:bg-primary-600 hover:text-white">
                    «
                </button>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="text-gray-500 px-2">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button disabled aria-current="page"
                                class="min-w-10 flex justify-center items-center text-white bg-primary-700 py-2.5 text-sm rounded-full">
                                {{ $page }}
                            </button>
                        @else
                            <button wire:click="gotoPage({{ $page }})"
                                class="min-w-10 flex justify-center items-center text-gray-700 hover:bg-primary-600 hover:text-white py-2.5 text-sm rounded-full">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-700 hover:bg-primary-600 hover:text-white">
                    »
                </button>
            @else
                <button disabled
                    class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-400 cursor-not-allowed">
                    »
                </button>
            @endif
        </nav>
    </div>
</div>
@endif
