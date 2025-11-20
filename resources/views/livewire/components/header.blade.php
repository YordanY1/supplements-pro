<nav x-data="{ open: false }"
    class="fixed top-0 inset-x-0 z-50 bg-light/80 backdrop-blur-md border-b border-secondary/20 shadow-sm text-dark font-primary">

    <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 py-3">

        {{-- LOGO --}}
        <a href="/" wire:navigate class="flex items-center gap-2">
            <img src="{{ asset('images/logo-removebg.png') }}" alt="Holistica Logo" class="h-20 w-auto">
            <span class="sr-only">Holistica</span>
        </a>

        {{-- DESKTOP MENU (NO DROPDOWN) --}}
        <ul class="hidden md:flex gap-8 text-base uppercase tracking-wider">

            <li>
                <a href="{{ route('home') }}" class="cursor-pointer hover:text-primary transition">
                    Начало
                </a>
            </li>

            <li>
                <a href="{{ route('categories') }}" class="cursor-pointer hover:text-primary transition">
                    Категории
                </a>
            </li>

            <li>
                <a href="{{ route('brands') }}" class="cursor-pointer hover:text-primary transition">
                    Марки
                </a>
            </li>

            <li>
                <a href="{{ route('blog') }}" class="cursor-pointer hover:text-primary transition">
                    Блог
                </a>
            </li>

        </ul>


        {{-- SEARCH (desktop) --}}
        <div class="hidden md:block w-full max-w-sm mx-6 relative" x-data @click.away="$wire.closeSearch()">

            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Търси продукт..."
                class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-dark shadow-inner
                       focus:border-primary focus:ring-primary/40 focus:ring-2">

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
                    class="absolute mt-2 w-full bg-white shadow-xl border border-secondary/40 rounded-xl p-3 text-center
                           text-sm text-gray-500 z-50">
                    Няма резултати.
                </div>
            @endif

        </div>

        {{-- CART --}}
        <div class="hidden md:block">
            <livewire:components.cart-badge />
        </div>

        <button @click="open = !open" class="md:hidden text-primary text-2xl">
            <i :class="open ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>


    </div>

    {{-- MOBILE MENU (WITH DROPDOWNS) --}}
    <div x-show="open" x-transition.duration.300ms class="md:hidden px-6 pb-6 bg-secondary/30 rounded-b-2xl">

        <ul class="flex flex-col gap-4 text-lg mt-4">

            <li>
                <a href="{{ route('home') }}" class="block hover:text-primary">Начало</a>
            </li>

            <li>
                <a href="{{ route('blog') }}" class="block hover:text-primary">Блог</a>
            </li>

            <li x-data="{ openCat: false }">
                <button @click="openCat = !openCat"
                    class="flex justify-between items-center w-full text-left hover:text-primary">
                    Категории
                    <i :class="openCat ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>

                <ul x-show="openCat" class="pl-4 mt-2 space-y-1 text-lg">
                    @foreach ($categories as $category)
                        <li>
                            <a wire:navigate href="{{ route('catalog.category', $category['slug']) }}"
                                class="block hover:text-primary">
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>


            <li x-data="{ openBrand: false }">
                <button @click="openBrand = !openBrand"
                    class="flex justify-between items-center w-full text-left hover:text-primary">
                    Марки
                    <i :class="openBrand ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>

                <ul x-show="openBrand" class="pl-4 mt-2 space-y-1 text-lg">
                    @foreach ($brands as $brand)
                        <li>
                            <a wire:navigate href="{{ route('catalog.brand', $brand['slug']) }}"
                                class="block hover:text-primary">
                                {{ $brand['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>


        </ul>

        <div class="mt-4 md:hidden">
            <livewire:components.cart-badge />
        </div>
    </div>
</nav>
