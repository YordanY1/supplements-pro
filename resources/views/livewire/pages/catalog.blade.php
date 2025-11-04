<div class="bg-light min-h-screen py-16 px-6 font-primary text-dark" id="product">
    <div class="max-w-7xl mx-auto">

        <h1 class="text-4xl font-extrabold text-primary mb-10 text-center">{{ $title }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Filters --}}
            <aside class="md:col-span-1 space-y-10">

                {{-- Categories --}}
                <div>
                    <h3 class="text-lg font-semibold text-primary mb-4">Категории</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $cat)
                            <button wire:click="toggleCategory('{{ $cat['slug'] }}')"
                                class="{{ in_array($cat['slug'], explode(',', $this->category))
                                    ? 'bg-primary text-dark border-primary'
                                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                                px-4 py-2 rounded-full text-sm border transition font-medium">
                                {{ $cat['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Brands --}}
                <div>
                    <h3 class="text-lg font-semibold text-primary mb-4">Марки</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($brands as $b)
                            <button wire:click="toggleBrand('{{ $b['slug'] }}')"
                                class="{{ in_array($b['slug'], explode(',', $this->brand))
                                    ? 'bg-primary text-dark border-primary'
                                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                                px-4 py-2 rounded-full text-sm border transition font-medium">
                                {{ $b['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Sorting --}}
                <div>
                    <h3 class="text-lg font-semibold text-primary mb-4">Сортиране</h3>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('sort','default')"
                            class="{{ $sort === 'default' ? 'bg-primary text-dark border-primary' : 'bg-light text-dark border-secondary' }} px-4 py-2 rounded-full text-sm border">По
                            подразбиране</button>
                        <button wire:click="$set('sort','price_asc')"
                            class="{{ $sort === 'price_asc' ? 'bg-primary text-dark border-primary' : 'bg-light text-dark border-secondary' }} px-4 py-2 rounded-full text-sm border">↑
                            Цена</button>
                        <button wire:click="$set('sort','price_desc')"
                            class="{{ $sort === 'price_desc' ? 'bg-primary text-dark border-primary' : 'bg-light text-dark border-secondary' }} px-4 py-2 rounded-full text-sm border">↓
                            Цена</button>
                    </div>
                </div>

                {{-- Per Page --}}
                <div>
                    <h3 class="text-lg font-semibold text-primary mb-4">Брой на страница</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([12, 24] as $count)
                            <button wire:click="$set('perPage',{{ $count }})"
                                class="{{ $perPage == $count ? 'bg-primary text-dark border-primary' : 'bg-light text-dark border-secondary' }} px-4 py-2 rounded-full text-sm border">
                                {{ $count }}
                            </button>
                        @endforeach
                    </div>
                </div>

            </aside>

            {{-- Products --}}
            <section class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($products as $product)
                    <div
                        class="bg-secondary/40 p-6 rounded-2xl border border-secondary/40 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                        {{-- Clickable product --}}
                        <a href="{{ route('product.show', $product['slug']) }}" class="block group">

                            <div class="bg-white p-4 rounded-xl mb-6 flex justify-center items-center h-[280px]">
                                <img src="{{ $product['image'] ?? asset('images/default.jpg') }}"
                                    alt="{{ $product['title'] }}"
                                    class="h-full w-auto object-contain group-hover:scale-105 transition duration-300">
                            </div>

                            <h2 class="text-xl font-bold text-primary mb-1 group-hover:text-secondary transition">
                                {{ $product['title'] }}
                            </h2>

                            <p class="text-sm text-dark/70 mb-1">
                                {{ $product['brand_name'] ?? 'Без марка' }}
                            </p>

                            <p class="text-lg font-semibold text-dark mb-3">
                                {{ number_format($product['price'], 2) }} {{ $product['currency_symbol'] ?? 'лв.' }}
                            </p>
                        </a>

                        {{-- Add to cart --}}
                        <button wire:click="addToCart('{{ $product['id'] }}')"
                            class="mt-3 w-full px-6 py-2 bg-primary text-dark font-semibold rounded-xl hover:bg-secondary hover:text-white transition cursor-pointer">
                            Добави в количката
                        </button>
                    </div>
                @empty
                    <p class="col-span-full text-center text-dark/60">Няма продукти.</p>
                @endforelse
            </section>


        </div>

        {{-- Pagination --}}
        @if ($total > $perPage)
            @php
                $lastPage = ceil($total / $perPage);
                $start = max(1, $page - 2);
                $end = min($lastPage, $page + 2);
            @endphp

            <div class="flex justify-center mt-10 space-x-2">
                @if ($start > 1)
                    <button wire:click="$set('page',1); window.scrollTo({top:0,behavior:'smooth'})"
                        class="px-3 py-2 border rounded">1</button>
                    @if ($start > 2)
                        <span>…</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <button wire:click="$set('page',{{ $i }}); window.scrollTo({top:0,behavior:'smooth'})"
                        class="px-3 py-2 border rounded {{ $page == $i ? 'bg-primary text-white' : '' }}">
                        {{ $i }}
                    </button>
                @endfor

                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <span>…</span>
                    @endif
                    <button wire:click="$set('page',{{ $lastPage }}); window.scrollTo({top:0,behavior:'smooth'})"
                        class="px-3 py-2 border rounded">{{ $lastPage }}</button>
                @endif
            </div>
        @endif

    </div>
</div>
