<div class="max-w-7xl mx-auto py-10 px-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- Product Image --}}
        <div class="bg-white p-4 rounded-xl shadow-md flex items-center justify-center">
            <img src="{{ $product['image'] ?? asset('images/default.jpg') }}" alt="{{ $product['title'] }}"
                class="rounded-xl shadow-lg w-full object-contain max-h-[450px]">
        </div>


        {{-- Product Info --}}
        <div>
            <h1 class="text-3xl font-bold text-primary mb-2">
                {{ $product['title'] }}
            </h1>

            <p class="text-gray-600 mb-4 text-lg">
                {{ $product['brand_name'] ?? 'Без марка' }}
            </p>

            {{-- Price --}}
            <div class="mb-6">
                <span class="text-2xl font-extrabold text-dark">
                    {{ number_format($product['price'], 2) }} лв
                </span>

                @if (!empty($product['old_price']))
                    <span class="text-lg text-gray-500 line-through ml-3">
                        {{ number_format($product['old_price'], 2) }} лв
                    </span>
                @endif
            </div>

            {{-- Stock --}}
            @if (!$product['available'])
                <span class="text-red-500 font-bold">Изчерпан</span>
            @else
                <button wire:click="addToCart"
                    class="px-6 py-3 bg-primary text-dark font-bold rounded-xl hover:bg-secondary transition cursor-pointer w-full md:w-auto">
                    Добави в количката
                </button>
            @endif

            {{-- Pack + Category --}}
            <p class="mt-4 text-sm text-gray-500">
                {{ $product['pack'] ?? '' }}

                @if (!empty($product['category']))
                    | Категория: {{ $product['category'] }}
                @endif
            </p>
        </div>

    </div>

    {{-- Tabs --}}
    <div class="mt-12">
        <div x-data="{ tab: 'description' }">

            {{-- Buttons --}}
            <div class="flex gap-4 border-b pb-2 mb-4">
                <button @click="tab = 'description'"
                    :class="tab === 'description' ? 'border-b-2 border-primary font-semibold' : ''" class="pb-2">
                    📝 Описание
                </button>

                <button @click="tab = 'label'"
                    :class="tab === 'label' ? 'border-b-2 border-primary font-semibold' : ''" class="pb-2">
                    🧾 Хранителен състав
                </button>

                <button @click="tab = 'brand'"
                    :class="tab === 'brand' ? 'border-b-2 border-primary font-semibold' : ''" class="pb-2">
                    🏷️ Марка
                </button>
            </div>

            {{-- Tab Content --}}
            <div>

                {{-- Description --}}
                <div x-show="tab === 'description'" class="text-gray-700 leading-relaxed prose max-w-none">
                    @if (!empty($product['description_html']))
                        {!! $product['description_html'] !!}
                    @else
                        <p>Няма описание за този продукт.</p>
                    @endif
                </div>

                {{-- Supplement Facts --}}
                <div x-show="tab === 'label'" class="mt-4">
                    @if (!empty($product['supplement_facts_html']))
                        {!! $product['supplement_facts_html'] !!}
                    @elseif (!empty($product['label']))
                        <img src="{{ $product['label'] }}" class="rounded-lg shadow border max-w-md">
                    @else
                        <p class="text-gray-600">Няма информация за хранителен състав.</p>
                    @endif
                </div>

                {{-- Brand --}}
                <div x-show="tab === 'brand'" class="text-gray-700">
                    <p>Марка: <strong>{{ $product['brand_name'] ?? 'Неизвестна' }}</strong></p>
                </div>

            </div>

        </div>
    </div>
</div>
