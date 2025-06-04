<nav x-data="{ open: false }"
    class="bg-primary text-text font-primary border-b border-white/10 shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 py-3">

        {{-- Logo --}}
        <a href="/" wire:navigate class="text-2xl font-bold text-accent tracking-wide">
            SupplementsPro
        </a>

        {{-- Desktop Menu --}}
        <ul class="hidden md:flex gap-6 text-base uppercase tracking-wider">
            <li>
                <a href="{{ route('home') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-accent
              hover:after:w-full after:transition-all after:duration-300 hover:text-accent transition">
                    Начало
                </a>
            </li>

            <li class="relative" x-data="{ open: false, timeout: null }" @mouseenter="clearTimeout(timeout); open = true"
                @mouseleave="timeout = setTimeout(() => open = false, 200)">
                <a href="{{ route('catalog') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-accent
                          hover:after:w-full after:transition-all after:duration-300 hover:text-accent transition">
                    Категории
                </a>
                <ul x-show="open" x-transition
                    class="absolute left-0 top-full bg-primary mt-2 rounded-xl shadow-xl border border-white/10 min-w-[200px] z-50"
                    @click.away="open = false">
                    @foreach ($categories as $category)
                        <li>
                            <a wire:navigate href="{{ route('catalog.category', $category->slug) }}"
                                class="block px-4 py-2 hover:text-accent transition text-sm">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="relative" x-data="{ open: false, timeout: null }" @mouseenter="clearTimeout(timeout); open = true"
                @mouseleave="timeout = setTimeout(() => open = false, 200)">
                <a href="{{ route('catalog') }}"
                    class="cursor-pointer relative after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-accent
                          hover:after:w-full after:transition-all after:duration-300 hover:text-accent transition">
                    Марки
                </a>
                <ul x-show="open" x-transition
                    class="absolute left-0 top-full bg-primary mt-2 rounded-xl shadow-xl border border-white/10 min-w-[200px] z-50"
                    @click.away="open = false">
                    @foreach ($brands as $brand)
                        <li>
                            <a wire:navigate href="{{ route('catalog.brand', $brand->slug) }}"
                                class="block px-4 py-2 hover:text-accent transition text-sm">
                                {{ $brand->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>

        {{-- Burger Icon --}}
        <button @click="open = !open" class="md:hidden focus:outline-none transition text-accent text-2xl">
            <i :class="open ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>
    </div>

    {{-- Mobile Dropdown --}}
    <div x-show="open" x-transition.duration.300ms class="md:hidden px-6 pb-6">
        <ul class="flex flex-col gap-4 text-lg md:text-base tracking-wider mt-4">
            <li>
                <a href="{{ route('home') }}" class="block hover:text-accent transition">
                    Начало
                </a>
            </li>

            <li x-data="{ openCat: false }">
                <button @click="openCat = !openCat"
                    class="w-full text-left flex justify-between items-center hover:text-accent transition">
                    Категории
                    <i :class="openCat ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>
                <ul x-show="openCat" x-transition class="pl-4 mt-2 space-y-1 text-lg">
                    @foreach ($categories as $category)
                        <li>
                            <a wire:navigate href="{{ route('catalog.category', $category->slug) }}"
                                class="block hover:text-accent">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li x-data="{ openBrand: false }">
                <button @click="openBrand = !openBrand"
                    class="w-full text-left flex justify-between items-center hover:text-accent transition">
                    Марки
                    <i :class="openBrand ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>
                <ul x-show="openBrand" x-transition class="pl-4 mt-2 space-y-1 text-lg">
                    @foreach ($brands as $brand)
                        <li>
                            <a wire:navigate href="{{ route('catalog.brand', $brand->slug) }}"
                                class="block hover:text-accent">
                                {{ $brand->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
</nav>
