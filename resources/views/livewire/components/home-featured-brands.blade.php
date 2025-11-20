<div class="mt-16">
    <h2 class="text-3xl font-bold text-primary mb-8 text-center">
        Популярни марки
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">

        @foreach ($brands as $brand)
            <a href="{{ route('catalog.brand', $brand['slug']) }}"
                class="block p-5 rounded-xl text-center border border-secondary/50
                       bg-secondary/20 hover:bg-secondary/30
                       shadow-sm hover:shadow-md transition-all duration-200">

                <p class="font-semibold text-dark text-sm tracking-wide">
                    {{ $brand['name'] }}
                </p>

            </a>
        @endforeach

    </div>
</div>
