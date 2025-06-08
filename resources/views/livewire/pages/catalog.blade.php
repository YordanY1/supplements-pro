<div class="bg-primary min-h-screen py-16 px-6 font-primary text-text" id="product">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-extrabold text-accent mb-10 text-center">{{ $title }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            {{-- Sidebar --}}
            <aside class="md:col-span-1">
                <livewire:components.catalog-filters />
            </aside>

            {{-- Products --}}
            <section class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($products as $product)
                    <div class="bg-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition text-center">
                        <div class="bg-white p-4 rounded-xl mb-6">
                            <img src="{{ asset($product->image ?? 'images/default.jpg') }}" alt="{{ $product->name }}"
                                class="mx-auto h-[280px] w-auto object-contain" />
                        </div>
                        <h2 class="text-xl font-bold text-accent mb-2">{{ $product->name }}</h2>
                        <p class="text-sm text-text/60 mb-2">{{ $product->brand->name ?? 'Без марка' }}</p>
                        <p class="text-lg font-semibold text-white">
                            {{ number_format($product->price, 2) }}
                            {{ $product->currency?->symbol ?? 'лв.' }}
                        </p>

                        <button wire:click="addToCart({{ $product->id }})"
                            class="mt-4 inline-block px-6 py-2 bg-accent text-white font-bold rounded-xl hover:bg-accent-2 transition cursor-pointer">
                            Добави в количката
                        </button>
                    </div>

                @empty
                    <p class="text-center text-text/60 col-span-full">Няма налични продукти.</p>
                @endforelse
            </section>
        </div>

        {{-- Pagination --}}
        <div class="col-span-full flex justify-center mt-10">
            {{ $products->links('vendor.livewire.components.pagination', data: ['scrollTo' => '#product']) }}
        </div>

    </div>
</div>
