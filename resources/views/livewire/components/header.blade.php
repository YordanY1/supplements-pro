<nav x-data="{ open: false }"
    class="fixed top-0 inset-x-0 z-50 bg-light/80 backdrop-blur-md border-b border-secondary/20 shadow-sm text-dark font-primary">

    <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 py-3">

        {{-- LOGO --}}
        <a href="/" wire:navigate class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="SupplementsPro Logo" class="h-15 w-auto">
            <span class="sr-only">SupplementsPro</span>
        </a>

        {{-- DESKTOP MENU --}}
        <ul class="hidden md:flex gap-6 text-base uppercase tracking-wider">
            <li>
                <a href="{{ route('home') }}" class="cursor-pointer relative hover:text-primary">
                    Начало
                </a>
            </li>

            {{-- Categories --}}
            <li class="relative" x-data="{ catOpen: false, timeout: null }" @mouseenter="clearTimeout(timeout); catOpen = true"
                @mouseleave="timeout = setTimeout(() => catOpen = false, 200)">

                <a href="{{ route('catalog') }}" class="cursor-pointer hover:text-primary">
                    Категории
                </a>

                <ul x-show="catOpen" x-transition
                    class="absolute left-0 top-full bg-secondary mt-2 rounded-xl shadow-xl border border-secondary/40 min-w-[200px] z-50 max-h-60 overflow-y-auto">
                    @foreach ($categories as $category)
                        <li>
                            <a wire:navigate href="{{ route('catalog.category', $category['slug']) }}"
                                class="block px-4 py-2 hover:text-primary text-sm">
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            {{-- Brands --}}
            <li class="relative" x-data="{ brandOpen: false, timeout: null }" @mouseenter="clearTimeout(timeout); brandOpen = true"
                @mouseleave="timeout = setTimeout(() => brandOpen = false, 200)">

                <a href="{{ route('catalog') }}" class="cursor-pointer hover:text-primary">
                    Марки
                </a>

                <ul x-show="brandOpen" x-transition
                    class="absolute left-0 top-full bg-secondary mt-2 rounded-xl shadow-xl border border-secondary/40 min-w-[200px] z-50 max-h-60 overflow-y-auto">
                    @foreach ($brands as $brand)
                        <li>
                            <a wire:navigate href="{{ route('catalog.brand', $brand['slug']) }}"
                                class="block px-4 py-2 hover:text-primary text-sm">
                                {{ $brand['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>

        {{-- SEARCH + DEBUG --}}
        <div class="hidden md:block w-full max-w-sm mx-6 relative" x-data @click.away="$wire.closeSearch()">

            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Търси продукт..."
                class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-dark
           shadow-inner focus:border-primary focus:ring-primary/40 focus:ring-2">


            {{-- Results --}}
            @if ($searchOpen && count($results) > 0)
                <div
                    class="absolute mt-2 w-full bg-white shadow-xl border border-secondary/40 rounded-xl max-h-72 overflow-y-auto z-50">

                    @foreach ($results as $p)
                        <button wire:click="goTo('{{ $p['slug'] }}')"
                            class="flex w-full gap-3 p-3 hover:bg-primary/10 text-left cursor-pointer">

                            <img src="{{ $p['image'] ?? '/placeholder.png' }}" class="h-10 w-10 rounded object-contain">

                            <div>
                                <p class="font-semibold text-dark text-sm">{{ $p['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $p['brand_name'] }}</p>
                                <p class="text-xs text-gray-600 font-bold mt-1">
                                    {{ number_format($p['price'], 2) }} лв
                                </p>
                            </div>
                        </button>
                    @endforeach

                </div>
            @endif

            {{-- No results --}}
            @if ($searchOpen && $search && count($results) === 0)
                <div
                    class="absolute mt-2 w-full bg-white shadow-xl border border-secondary/40 rounded-xl p-3 text-center text-sm text-gray-500 z-50">
                    Няма резултати.
                </div>
            @endif

        </div>

        {{-- CART --}}
        <div class="hidden md:block">
            <livewire:components.cart-badge />
        </div>

        {{-- Burger --}}
        <button @click="open = !open" class="md:hidden text-primary text-2xl">
            <i :class="open ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>
    </div>
</nav>
