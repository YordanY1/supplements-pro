<nav x-data="{ open: false }"
    class="fixed top-0 inset-x-0 z-50 bg-light/80 backdrop-blur-md border-b border-secondary/20 shadow-sm text-dark font-primary">


    <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 py-3">

        {{-- Logo --}}
        <a href="/" wire:navigate class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="SupplementsPro Logo" class="h-15 w-auto object-contain">
            <span class="sr-only">SupplementsPro</span>
        </a>

        {{-- Desktop Menu --}}
        <ul class="hidden md:flex gap-6 text-base uppercase tracking-wider">
            <li>
                <a href="{{ route('home') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-primary
                    hover:after:w-full after:transition-all after:duration-300 hover:text-primary transition">
                    Начало
                </a>
            </li>

            {{-- Categories Dropdown --}}
            <li class="relative" x-data="{ open: false, timeout: null }" @mouseenter="clearTimeout(timeout); open = true"
                @mouseleave="timeout = setTimeout(() => open = false, 200)">
                <a href="{{ route('catalog') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-primary
                    hover:after:w-full after:transition-all after:duration-300 hover:text-primary transition">
                    Категории
                </a>
                <ul x-show="open" x-transition
                    class="absolute left-0 top-full bg-secondary mt-2 rounded-xl shadow-xl border border-secondary/40 min-w-[200px] z-50
                    max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-accent scrollbar-track-white/10"
                    @click.away="open = false">
                    @foreach ($categories as $category)
                        <li>
                            <a wire:navigate href="{{ route('catalog.category', $category['slug']) }}"
                                class="block px-4 py-2 hover:text-primary transition text-sm">
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            {{-- Brands Dropdown --}}
            <li class="relative" x-data="{ open: false, timeout: null }" @mouseenter="clearTimeout(timeout); open = true"
                @mouseleave="timeout = setTimeout(() => open = false, 200)">
                <a href="{{ route('catalog') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-primary
                    hover:after:w-full after:transition-all after:duration-300 hover:text-primary transition">
                    Марки
                </a>
                <ul x-show="open" x-transition
                    class="absolute left-0 top-full bg-secondary mt-2 rounded-xl shadow-xl border border-secondary/40 min-w-[200px] z-50
                    max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-accent scrollbar-track-white/10"
                    @click.away="open = false">
                    @foreach ($brands as $brand)
                        <li>
                            <a wire:navigate href="{{ route('catalog.brand', $brand['slug']) }}"
                                class="block px-4 py-2 hover:text-primary transition text-sm">
                                {{ $brand['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>

        {{-- Cart icon (desktop) --}}
        <div class="hidden md:block">
            <livewire:components.cart-badge />
        </div>

        {{-- Burger Icon --}}
        <button @click="open = !open" class="md:hidden focus:outline-none transition text-primary text-2xl">
            <i :class="open ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>
    </div>

    {{-- Mobile Dropdown --}}
    <div x-show="open" x-transition.duration.300ms class="md:hidden px-6 pb-6 bg-secondary/30 rounded-b-2xl">
        <ul class="flex flex-col gap-4 text-lg md:text-base tracking-wider mt-4">
            <li>
                <a href="{{ route('home') }}" class="block hover:text-primary transition">
                    Начало
                </a>
            </li>
            <li x-data="{ openCat: false }">
                <button @click="openCat = !openCat"
                    class="w-full text-left flex justify-between items-center hover:text-primary transition">
                    Категории
                    <i :class="openCat ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>
                <ul x-show="openCat" x-transition class="pl-4 mt-2 space-y-1 text-lg max-h-[300px] overflow-y-auto">
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
                    class="w-full text-left flex justify-between items-center hover:text-primary transition">
                    Марки
                    <i :class="openBrand ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>
                <ul x-show="openBrand" x-transition class="pl-4 mt-2 space-y-1 text-lg max-h-[300px] overflow-y-auto">
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
