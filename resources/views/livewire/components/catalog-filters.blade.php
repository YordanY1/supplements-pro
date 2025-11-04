<div class="space-y-10">
    {{-- Categories --}}
    <div>
        <h3 class="text-lg font-semibold text-primary mb-4">Категории</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <button wire:click="toggleCategory('{{ $category['slug'] }}')"
                    class="{{ in_array($category['slug'], $categorySlugs)
                        ? 'bg-primary text-dark border-primary'
                        : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                        px-4 py-2 rounded-full text-sm border transition font-medium">
                    {{ $category['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Brands --}}
    <div>
        <h3 class="text-lg font-semibold text-primary mb-4">Марки</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($brands as $brand)
                <button wire:click="toggleBrand('{{ $brand['slug'] }}')"
                    class="{{ in_array($brand['slug'], $brandSlugs)
                        ? 'bg-primary text-dark border-primary'
                        : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                        px-4 py-2 rounded-full text-sm border transition font-medium">
                    {{ $brand['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Sorting --}}
    <div>
        <h3 class="text-lg font-semibold text-primary mb-4">Сортиране</h3>
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('sort', 'default')"
                class="{{ $sort === 'default'
                    ? 'bg-primary text-dark border-primary'
                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                    px-4 py-2 rounded-full text-sm border transition font-medium">
                По подразбиране
            </button>

            <button wire:click="$set('sort', 'price_asc')"
                class="{{ $sort === 'price_asc'
                    ? 'bg-primary text-dark border-primary'
                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                    px-4 py-2 rounded-full text-sm border transition font-medium">
                ↑ Цена
            </button>

            <button wire:click="$set('sort', 'price_desc')"
                class="{{ $sort === 'price_desc'
                    ? 'bg-primary text-dark border-primary'
                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                    px-4 py-2 rounded-full text-sm border transition font-medium">
                ↓ Цена
            </button>
        </div>
    </div>

    {{-- Per Page --}}
    <div>
        <h3 class="text-lg font-semibold text-primary mb-4">Брой на страница</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ([12, 24] as $count)
                <button wire:click="$set('perPage', {{ $count }})"
                    class="{{ $perPage === $count
                        ? 'bg-primary text-dark border-primary'
                        : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                        px-4 py-2 rounded-full text-sm border transition font-medium">
                    {{ $count }}
                </button>
            @endforeach

            <button wire:click="$set('perPage', 10000)"
                class="{{ $perPage >= 10000
                    ? 'bg-primary text-dark border-primary'
                    : 'bg-light text-dark border-secondary hover:border-primary hover:bg-secondary/40' }}
                    px-4 py-2 rounded-full text-sm border transition font-medium">
                Всички
            </button>
        </div>
    </div>
</div>
