@if ($paginator->hasPages())
    <nav class="flex items-center space-x-2 animate-fade-in-up" aria-label="Pagination Navigation">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-lg bg-gray-600/30 text-white opacity-50 cursor-not-allowed">Назад</span>
        @else
            <button wire:click="previousPage" onclick="scrollToProducts()"
                class="px-4 py-2 rounded-lg bg-accent text-black hover:bg-yellow-400 transition">
                Назад
            </button>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-4 py-2 text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-4 py-2 rounded-lg bg-accent text-black font-bold">
                            {{ $page }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" onclick="scrollToProducts()"
                            class="px-4 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-600 transition">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" onclick="scrollToProducts()"
                class="px-4 py-2 rounded-lg bg-accent text-black hover:bg-yellow-400 transition">
                Напред
            </button>
        @else
            <span class="px-4 py-2 rounded-lg bg-gray-600/30 text-white opacity-50 cursor-not-allowed">Напред</span>
        @endif
    </nav>

    <script>
        function scrollToProducts() {
            // Small delay to allow Livewire to update the DOM first
            setTimeout(() => {
                const element = document.getElementById('products');
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    // Fallback: scroll to top of page
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        }
    </script>
@endif
