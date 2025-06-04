<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-accent mb-4">Категории</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <button wire:click="toggleCategory('{{ $category->slug }}')"
                    class="{{ in_array($category->slug, $categorySlugs) ? 'bg-accent text-black border-accent' : 'bg-card text-text border-white/10 hover:border-accent' }} px-4 py-2 rounded-full text-sm border transition">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-accent mb-4">Марки</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($brands as $brand)
                <button wire:click="toggleBrand('{{ $brand->slug }}')"
                    class="{{ in_array($brand->slug, $brandSlugs) ? 'bg-accent text-black border-accent' : 'bg-card text-text border-white/10 hover:border-accent' }} px-4 py-2 rounded-full text-sm border transition">
                    {{ $brand->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-accent mb-4">Сортиране</h3>
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('sort', 'default')"
                class="{{ $sort === 'default' ? 'bg-accent text-black border-accent' : 'bg-card text-text border-white/10 hover:border-accent' }} px-4 py-2 rounded-full text-sm border transition">
                По подразбиране
            </button>
            <button wire:click="$set('sort', 'price_asc')"
                class="{{ $sort === 'price_asc' ? 'bg-accent text-black border-accent' : 'bg-card text-text border-white/10 hover:border-accent' }} px-4 py-2 rounded-full text-sm border transition">
                ↑ Цена
            </button>
            <button wire:click="$set('sort', 'price_desc')"
                class="{{ $sort === 'price_desc' ? 'bg-accent text-black border-accent' : 'bg-card text-text border-white/10 hover:border-accent' }} px-4 py-2 rounded-full text-sm border transition">
                ↓ Цена
            </button>
        </div>
    </div>
</div>
