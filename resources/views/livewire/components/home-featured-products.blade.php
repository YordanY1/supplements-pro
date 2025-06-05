<section class="bg-card rounded-2xl shadow-2xl px-6 py-16 mt-24 border border-white/10">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-extrabold text-accent mb-4">Нашите предложения</h2>
        <p class="text-text/70 text-lg md:text-xl mb-12">
            Виж някои от най-популярните ни продукти от селекцията ни
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            @foreach ($products as $product)
                <div
                    class="bg-primary/50 p-6 rounded-2xl border border-white/10 shadow-xl hover:shadow-2xl transition duration-300 text-center">
                    <div class="bg-white p-4 rounded-xl mb-6">
                        <img src="{{ asset($product->image ?? 'images/default.jpg') }}" alt="{{ $product->name }}"
                            class="mx-auto h-[300px] w-auto object-contain" />
                    </div>

                    <h3 class="text-xl font-bold text-accent mb-2">{{ $product->name }}</h3>
                    <p class="text-base text-text/60 mb-2">{{ $product->brand->name ?? 'Без марка' }}</p>
                    <p class="text-xl font-semibold text-white mb-4">{{ number_format($product->price, 2) }} лв.</p>

                    <button wire:click="addToCart({{ $product->id }})"
                        class="px-6 py-2 bg-accent text-white font-bold rounded-full hover:bg-accent-2 transition duration-200 cursor-pointer">
                        Добави в количката
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-16">
            <a wire:navigate href="{{ route('catalog') }}"
                class="inline-flex items-center gap-3 px-10 py-4 rounded-full text-white font-bold text-lg bg-cta hover:bg-accent-2 transition-all duration-300 shadow-xl hover:scale-105">
                <i class="fas fa-shopping-cart"></i> Виж всички продукти
            </a>
        </div>
    </div>
</section>
